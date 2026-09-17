<?php

namespace LiveHelperChat\Mcp;

use Mcp\Exception\ToolCallException;

/**
 * Shared statistics engine behind the MCP chat statistics tools.
 *
 * The tools stay thin - every question ("how many chats were there in the past 24 hours", "chats
 * per department last week", "average first response time") is answered from here.
 *
 * The tools accept a raw Live Helper Chat filter map (`{"filter": {"status": 0},
 * "filtergte": {"time": 1758000000}}`) straight from the model, so two guarantees are enforced
 * here instead of trusting the input:
 *
 *  - Only the eight operators understood by BOTH code paths are accepted. The trait's
 *    `erLhcoreClassModelChat::getCount()` supports more, but `erLhcoreClassChat::getCount()` - used
 *    by the core statistic helpers - supports a subset, and an operator silently ignored by one
 *    path would produce contradicting numbers for the same question.
 *  - Field names are validated against the `lh_chat` state and rewritten into the qualified
 *    `lh_chat.<field>` form. That is mandatory, not cosmetic: `ezcQueryExpression` interpolates
 *    column names raw into the SQL, and qualifying also removes the `lh_chat` / `lh_msg` ambiguity
 *    inside the joined message counters of `getLast24HStatistic()`.
 *
 * Everything is read only - only SELECTs and session level safety settings are issued.
 *
 * Output obeys the MCP output policy of this feature: identifiers and flags only, no department
 * names, operator names, visitor nicks, e-mail addresses or message bodies.
 */
class ChatStats
{
    /**
     * Filter operators a caller may use.
     *
     * Deliberately the intersection of the trait's `getConditions()` and
     * `erLhcoreClassChat::getCount()`: `filter`, `filterin`, `filterlt`, `filterlte`, `filtergt`,
     * `filtergte`, `filterlike` and `filternot`.
     */
    const FILTER_OPERATORS = array(
        'filter',
        'filterin',
        'filterlt',
        'filterlte',
        'filtergt',
        'filtergte',
        'filterlike',
        'filternot',
    );

    /** Operators whose value may be a list (rendered as `IN` / `NOT IN`). */
    const LIST_OPERATORS = array('filter', 'filterin', 'filternot');

    /**
     * Operators which look like filters but are refused, together with the reason. Without this a
     * rejected key would only produce a generic "unknown operator" message.
     */
    const FORBIDDEN_OPERATORS = array(
        'customfilter' => 'raw SQL is never accepted from a client',
        'filter_custom' => 'raw SQL is never accepted from a client',
        'having' => 'raw SQL is never accepted from a client',
        'select_columns' => 'the SELECT list is decided by the server',
        'group' => 'use the dedicated `group_by` / `interval` parameter',
        'sort' => 'the result order is decided by the server',
        'limit' => 'use the dedicated `limit` parameter',
        'offset' => 'pagination is not exposed',
        'lock' => 'statistics never lock rows',
        'use_index' => 'index hints are decided by the server',
        'innerjoin' => 'joins are decided by the server',
        'leftjoin' => 'joins are decided by the server',
        'leftouterjoin' => 'joins are decided by the server',
        'innerjoinsame' => 'joins are decided by the server',
        'filterfields' => 'not supported by every statistic path',
        'filterinfields' => 'not supported by every statistic path',
        'filterall' => 'not supported by every statistic path',
        'filterlor' => 'not supported by every statistic path',
        'filterlikeright' => 'not supported by every statistic path',
        'filterlikefields' => 'not supported by every statistic path',
        'filternotin' => 'not supported by every statistic path',
        'filterltfields' => 'not supported by every statistic path',
        'filterltefields' => 'not supported by every statistic path',
        'filtergtfields' => 'not supported by every statistic path',
        'filtergtefields' => 'not supported by every statistic path',
        'filternotfields' => 'not supported by every statistic path',
        'prefill_attributes' => 'not a filter',
        'enable_sql_cache' => 'not a filter',
    );

    /** Supported `period` shorthands. `all` is the explicit opt-out of any time restriction. */
    const PERIODS = array(
        '1h' => 'last hour',
        '24h' => 'last 24 hours',
        '7d' => 'last 7 days',
        '30d' => 'last 30 days',
        'today' => 'today',
        'yesterday' => 'yesterday',
        'this_week' => 'this week',
        'last_week' => 'last week',
        'this_month' => 'this month',
        'last_month' => 'last month',
        'all' => 'all time',
    );

    /** Accepted spellings of `period`. */
    const PERIOD_ALIASES = array(
        'last_hour' => '1h',
        '1d' => '24h',
        'last_24_hours' => '24h',
        '1w' => '7d',
        'last_7_days' => '7d',
        '1m' => '30d',
        'last_30_days' => '30d',
    );

    /** Grouping dimensions of `count_chats`. */
    const GROUP_BY = array(
        'department',
        'operator',
        'status',
        'status_sub',
        'bot',
        'device_type',
        'country',
        'channel',
        'theme',
        'product',
        'subject',
        'day',
        'week',
        'month',
        'hour',
        'weekday',
    );

    /** Bucket sizes of `get_chat_activity`. */
    const INTERVALS = array('hour', 'day', 'week', 'month', 'weekday');

    /**
     * Back office chat list parameters which reproduce a validated filter map. Only fields whose
     * chat list counterpart compares exactly the same way are listed - everything else is reported
     * as unmapped instead of silently widening the linked list.
     *
     * Scalar uparams are validated as `filter` (equals), list uparams as `filterin` (IN) by the
     * chat search (`lib/core/lhchat/searchattr/chat_search.php`).
     */
    const CHAT_LIST_SCALAR = array(
        'user_id' => 'user_id',
        'dep_id' => 'department_id',
        'status' => 'chat_status',
        'product_id' => 'product_id',
        'invitation_id' => 'invitation_id',
        'online_user_id' => 'online_user_id',
        'ip' => 'ip',
        'cls_us' => 'cls_us',
        'has_unread_op_messages' => 'has_unread_op_messages',
        'fbst' => 'fbst',
        'chat_initiator' => 'proactive_chat',
    );

    /** Fields whose `filter` / `filterin` maps onto a repeated (`IN`) chat list parameter. */
    const CHAT_LIST_LIST = array(
        'id' => 'chat_id',
        'user_id' => 'user_ids',
        'dep_id' => 'department_ids',
        'status' => 'chat_status_ids',
        'gbot_id' => 'bot_ids',
        'country_code' => 'country_ids',
        'theme_id' => 'theme_ids',
        'iwh_id' => 'iwh_ids',
    );

    /** Checkbox parameters which only express the "true" state (`1`). */
    const CHAT_LIST_FLAG = array(
        'anonymized' => 'anonymized',
        'unanswered_chat' => 'una',
        'has_unread_messages' => 'hum',
    );

    /** Range fields: the operators and the uparams the lower / upper bound map onto. */
    const CHAT_LIST_RANGE = array(
        'time' => array('from' => 'timefrom', 'till' => 'timeto', 'from_operator' => 'filtergte', 'till_operator' => 'filterlte', 'datetime' => true),
        'wait_time' => array('from' => 'wait_time_from', 'till' => 'wait_time_till', 'from_operator' => 'filtergt', 'till_operator' => 'filterlte'),
        'priority' => array('from' => 'priority_from', 'till' => 'priority_till', 'from_operator' => 'filtergte', 'till_operator' => 'filterlte'),
        'frt' => array('from' => 'frt_from', 'till' => 'frt_till', 'from_operator' => 'filtergt', 'till_operator' => 'filterlte'),
        'mart' => array('from' => 'mart_from', 'till' => 'mart_till', 'from_operator' => 'filtergt', 'till_operator' => 'filterlte'),
        'aart' => array('from' => 'aart_from', 'till' => 'aart_till', 'from_operator' => 'filtergt', 'till_operator' => 'filterlte'),
        'chat_duration' => array('from' => 'chat_duration_from', 'till' => 'chat_duration_till', 'from_operator' => 'filtergt', 'till_operator' => 'filterlte'),
    );

    // -----------------------------------------------------------------------
    // Filters
    // -----------------------------------------------------------------------

    /**
     * Validates and normalises a raw filter map.
     *
     * @param mixed $input
     *
     * @return array{params: array, summary: string}
     */
    public static function filter($input)
    {
        if ($input === null) {
            return array('params' => array(), 'summary' => '');
        }

        if (!is_array($input) || (count($input) > 0 && array_keys($input) === range(0, count($input) - 1))) {
            throw new ToolCallException('`filters` has to be a JSON object of operator => { field: value }, e.g. {"filter": {"status": 0}, "filtergte": {"time": 1758000000}}. Allowed operators: ' . implode(', ', self::FILTER_OPERATORS) . '.');
        }

        $params = array();
        $summary = array();

        foreach ($input as $operator => $fields) {
            if (isset(self::FORBIDDEN_OPERATORS[$operator])) {
                throw new ToolCallException('Filter operator `' . $operator . '` is not accepted: ' . self::FORBIDDEN_OPERATORS[$operator] . '. Allowed operators: ' . implode(', ', self::FILTER_OPERATORS) . '.');
            }

            if (!in_array($operator, self::FILTER_OPERATORS, true)) {
                throw new ToolCallException('Unknown filter operator `' . $operator . '`. Allowed operators: ' . implode(', ', self::FILTER_OPERATORS) . '.');
            }

            if (!is_array($fields) || empty($fields)) {
                throw new ToolCallException('Filter operator `' . $operator . '` expects a non empty object of `field: value` pairs.');
            }

            $parts = array();

            foreach ($fields as $field => $value) {
                $column = self::column($field);
                $name = substr($column, strlen('lh_chat.'));

                // `time` is a range field. Only the two range operators keep it unambiguous in the
                // joined message counters and let the core helpers move it to `lh_msg.time`.
                if ($name === 'time' && $operator !== 'filtergte' && $operator !== 'filterlte') {
                    throw new ToolCallException('`time` is only accepted in `filtergte` (range start) and `filterlte` (range end). Prefer the `period`, `time_from` and `time_to` parameters.');
                }

                if (is_array($value)) {
                    if (!in_array($operator, self::LIST_OPERATORS, true)) {
                        throw new ToolCallException('`' . $operator . '` on `' . $name . '` expects a single value, not a list.');
                    }

                    if (empty($value)) {
                        throw new ToolCallException('`' . $operator . '` on `' . $name . '` expects a non empty list of values.');
                    }

                    $list = array();

                    foreach ($value as $item) {
                        $list[] = self::scalar($operator, $name, $item);
                    }

                    $params[$operator][$column] = $list;
                    $parts[] = $name . ' ' . ($operator == 'filternot' ? 'NOT IN' : 'IN') . ' [' . implode(', ', array_map('strval', $list)) . ']';

                    continue;
                }

                $params[$operator][$column] = self::scalar($operator, $name, $value);
                $parts[] = $name . ' ' . self::operatorText($operator) . ' ' . self::valueText($params[$operator][$column]);
            }

            $summary[] = implode(' AND ', $parts);
        }

        return array('params' => $params, 'summary' => implode(' AND ', $summary));
    }

    /**
     * Adds the convenience `nick` parameter to an already validated filter map.
     *
     * A visitor nick is a `filterlike` (case insensitive contains) match, the same semantic the back
     * office chat search uses for its nick field. An exact match is still available through the raw
     * map (`{"filter": {"nick": "John"}}`). The nick is only ever used as a filter value - it is
     * never resolved or echoed beyond the `filters` block of the answer.
     *
     * @return array
     */
    public static function withNick(array $filter, $nick)
    {
        $nick = $nick !== null ? trim((string)$nick) : '';

        if ($nick === '') {
            return $filter;
        }

        $filter['params']['filterlike']['lh_chat.nick'] = $nick;
        $filter['summary'] = $filter['summary'] === ''
            ? 'nick LIKE ' . $nick
            : $filter['summary'] . ' AND nick LIKE ' . $nick;

        return $filter;
    }

    /**
     * Catalog of everything the filter map accepts - used by the `get_chat_filter_fields` tool so a
     * model can learn the contract instead of guessing.
     *
     * @return array
     */
    public static function filterReference()
    {
        $operators = array(
            array('operator' => 'filter', 'value' => 'single value or list', 'meaning' => 'equals, a list becomes IN'),
            array('operator' => 'filterin', 'value' => 'non empty list', 'meaning' => 'IN (...)'),
            array('operator' => 'filtergte', 'value' => 'single value', 'meaning' => 'greater than or equal (use it for the start of a time range)'),
            array('operator' => 'filterlte', 'value' => 'single value', 'meaning' => 'less than or equal (use it for the end of a time range)'),
            array('operator' => 'filtergt', 'value' => 'single value', 'meaning' => 'greater than'),
            array('operator' => 'filterlt', 'value' => 'single value', 'meaning' => 'less than'),
            array('operator' => 'filterlike', 'value' => 'single value', 'meaning' => 'case insensitive contains (%value%)'),
            array('operator' => 'filternot', 'value' => 'single value or list', 'meaning' => 'not equal, a list becomes NOT IN'),
        );

        return array(
            'operators' => $operators,
            'fields' => self::chatFields(),
            'group_by' => self::GROUP_BY,
            'intervals' => self::INTERVALS,
            'periods' => array_keys(self::PERIODS),
            'notes' => array(
                'Fields are `lh_chat` columns. `lh_chat.<field>` and the bare `<field>` are both accepted, anything from another table is refused.',
                '`time` is the chat creation timestamp. It is only accepted in `filtergte` (range start) and `filterlte` (range end) - prefer the `period`, `time_from` and `time_to` parameters over filtering `time` by hand.',
                'Every statistics tool also takes a dedicated `nick` parameter, which applies `filterlike` on `nick` (case insensitive contains). Use `filter` on `nick` inside `filters` for an exact match.',
                'Raw SQL (`customfilter`), joins, `sort`, `group` and `limit` inside `filters` are refused on purpose - they are decided by the server.',
                'Message counts are not filterable here. Use the message counters of `get_chat_statistics` instead.',
            ),
        );
    }

    /**
     * Resolves the requested time window.
     *
     * @param mixed $period
     * @param mixed $time_from
     * @param mixed $time_to
     * @param string $default
     *
     * @return array{from: int|null, to: int|null, label: string, params: array, applied: bool}
     */
    public static function period($period = null, $time_from = null, $time_to = null, $default = '24h')
    {
        $period = $period !== null ? strtolower(trim((string)$period)) : '';
        $from = null;
        $to = null;
        $label = '';

        $hasExplicitRange = ($time_from !== null && trim((string)$time_from) !== '')
            || ($time_to !== null && trim((string)$time_to) !== '');

        if ($hasExplicitRange) {
            $from = self::timestamp($time_from, 'time_from');
            $to = self::timestamp($time_to, 'time_to');
            $label = 'explicit range';
        } else {
            $key = $period !== '' ? $period : $default;

            if (isset(self::PERIOD_ALIASES[$key])) {
                $key = self::PERIOD_ALIASES[$key];
            }

            if ($key === 'all') {
                $label = self::PERIODS['all'];
            } else {
                if (!isset(self::PERIODS[$key])) {
                    throw new ToolCallException('Unknown `period` `' . $key . '`. Use one of: ' . implode(', ', array_keys(self::PERIODS)) . ', or pass `time_from` / `time_to`.');
                }

                $range = self::periodRange($key, time());

                if ($range === null) {
                    throw new ToolCallException('`period` `' . $key . '` cannot be resolved on the server.');
                }

                list($from, $to) = $range;
                $label = self::PERIODS[$key];
            }
        }

        if ($from !== null && $to !== null && $from > $to) {
            list($from, $to) = array($to, $from);
            $label .= ' (range swapped)';
        }

        $params = array();

        if ($from !== null) {
            $params['filtergte']['lh_chat.time'] = $from;
        }

        if ($to !== null) {
            $params['filterlte']['lh_chat.time'] = $to;
        }

        return array(
            'from' => $from,
            'to' => $to,
            'label' => $label,
            'params' => $params,
            'applied' => $from !== null || $to !== null,
        );
    }

    /**
     * Merges filter maps. Later values win, so the caller can pass the period first and let an
     * explicit user filter override it.
     *
     * @return array
     */
    public static function merge(array $first, array $second)
    {
        return array_replace_recursive($first, $second);
    }

    // -----------------------------------------------------------------------
    // Back office chat list link
    // -----------------------------------------------------------------------

    /**
     * Translates a validated filter map into a back office chat list URL (`chat/list`), so the
     * answer of a chat list tool can be verified by hand.
     *
     * The conditions are collected as chat list search parameters and turned into the URL by
     * `erLhcoreClassSearchHandler::getURLAppendFromInput()` - the very same generator the chat
     * list paginator and export screens use - so encoding, repeated `/(user_ids)/1/2` parameters
     * and the trailing `sortby` cannot drift from the back office. Conditions which have no
     * equivalent in the chat list search are returned in `unmapped` and are NOT applied, so the
     * linked list can only be wider - it never hides a chat the answer counted.
     *
     * @param array $params Validated filter map, as built by `filter()`, `period()` and `merge()`.
     * @param string|null $sort `newest` / `oldest` to mirror the tool ordering (`id_desc` / `id_asc`).
     *
     * @return array{url: string, mapped: int, unmapped: array}
     */
    public static function chatListUrl(array $params, $sort = null)
    {
        $query = array();
        $unmapped = array();
        $mapped = 0;

        foreach ($params as $operator => $fields) {
            if (!is_array($fields)) {
                // `limit` / `sort` are added by the tool after the link has been built.
                continue;
            }

            foreach ($fields as $column => $value) {
                $field = strpos($column, 'lh_chat.') === 0 ? substr($column, strlen('lh_chat.')) : $column;
                $range = isset(self::CHAT_LIST_RANGE[$field]) ? self::CHAT_LIST_RANGE[$field] : null;

                if ($range !== null && !is_array($value)) {
                    $bound = null;

                    if ($operator === $range['from_operator']) {
                        $bound = $range['from'];
                    } elseif ($operator === $range['till_operator']) {
                        $bound = $range['till'];
                    }

                    if ($bound !== null) {
                        if (isset($range['datetime'])) {
                            // Date bounds are a date plus separate hours / minutes / seconds parameters.
                            $timestamp = (int)$value;
                            $query[$bound] = date('Y-m-d', $timestamp);
                            $query[$bound . '_hours'] = (int)date('H', $timestamp);
                            $query[$bound . '_minutes'] = (int)date('i', $timestamp);
                            $query[$bound . '_seconds'] = (int)date('s', $timestamp);
                        } else {
                            $query[$bound] = $value;
                        }

                        $mapped++;
                        continue;
                    }
                }

                if ($operator === 'filter' && isset(self::CHAT_LIST_FLAG[$field])) {
                    if ((int)$value === 1) {
                        $query[self::CHAT_LIST_FLAG[$field]] = 1;
                        $mapped++;
                    } else {
                        $unmapped[] = $field . ' = ' . self::valueText($value) . ' (the chat list only filters this checkbox when it is set)';
                    }
                    continue;
                }

                if ($operator === 'filter' && !is_array($value) && isset(self::CHAT_LIST_SCALAR[$field])) {
                    $query[self::CHAT_LIST_SCALAR[$field]] = $value;
                    $mapped++;
                    continue;
                }

                if (($operator === 'filter' || $operator === 'filterin') && isset(self::CHAT_LIST_LIST[$field])) {
                    $values = is_array($value) ? array_values($value) : array($value);

                    // `chat_id` is the one parameter the chat search reads comma separated.
                    $query[self::CHAT_LIST_LIST[$field]] = $field === 'id' ? implode(',', $values) : $values;
                    $mapped++;
                    continue;
                }

                if ($operator === 'filterlike' && $field === 'nick' && !is_array($value)) {
                    // The chat list `nick` parameter is a contains match, same as `filterlike`.
                    $query['nick'] = $value;
                    $mapped++;
                    continue;
                }

                $unmapped[] = $field . ' ' . self::operatorText($operator) . ' '
                    . (is_array($value) ? '[' . implode(', ', array_map('strval', $value)) . ']' : self::valueText($value));
            }
        }

        if ($sort === 'oldest' || $sort === 'newest') {
            $query['sortby'] = $sort === 'oldest' ? 'id_asc' : 'id_desc';
        }

        // Same generator the chat list paginator and export screens use, so the produced segments
        // (encoding, repeated parameters, the trailing `sortby`) cannot drift.
        $append = \erLhcoreClassSearchHandler::getURLAppendFromInput($query);

        return array(
            'url' => \erLhcoreClassSystem::getHost() . \erLhcoreClassDesign::baseurl('chat/list') . $append,
            'mapped' => $mapped,
            'unmapped' => $unmapped,
        );
    }

    // -----------------------------------------------------------------------
    // Query plumbing
    // -----------------------------------------------------------------------

    /**
     * Applies the same session level safety net the statistics module uses, so a heavy question
     * cannot hang the MCP endpoint and the SQL buckets share the instance time zone.
     */
    public static function guard()
    {
        $db = \ezcDbInstance::get();

        foreach (array(
            "SET sql_mode=''",
            "SET LOCAL time_zone='" . (new \DateTime())->format('P') . "'",
            'SET SESSION max_execution_time=15000',
            'SET SESSION max_statement_time=15',
            'SET SESSION wait_timeout=30',
            'SET SESSION interactive_timeout=30',
            'SET SESSION innodb_lock_wait_timeout=10',
        ) as $query) {
            try {
                $db->query($query);
            } catch (\Exception $e) {
                // MySQL and MariaDB each understand only one of the timeout variables.
            }
        }
    }

    /** Number of chats matching the filter map. */
    public static function count(array $params)
    {
        return (int)\erLhcoreClassModelChat::getCount($params);
    }

    /**
     * Single aggregate over a `lh_chat` column, e.g. `AVG` of `chat_duration`.
     *
     * @return float|int|null
     */
    public static function aggregate(array $params, $field, $operation = 'AVG')
    {
        $value = \erLhcoreClassModelChat::getCount($params, $operation, $field);

        return $value === null ? null : $value + 0;
    }

    /**
     * Raw rows of a fixed SELECT fragment. `$select`, the `limit` and the `sort` come from this class
     * or from the tool, never from the caller.
     *
     * @return array
     */
    public static function rows(array $params, $select)
    {
        return (array)\erLhcoreClassModelChat::getCount($params, 'COUNT', false, $select, false, true, false, false);
    }

    /**
     * Single aggregated row of a fixed SELECT fragment.
     *
     * @return array
     */
    public static function row(array $params, $select)
    {
        $rows = self::rows($params, $select);

        return empty($rows) ? array() : $rows[0];
    }

    /**
     * Grouped aggregate. `$select` and `$groupBy` are fixed fragments built by this class, never
     * caller input.
     *
     * @return array
     */
    public static function grouped(array $params, $select, $groupBy, $orderBy = null)
    {
        $params['group'] = $groupBy;

        if ($orderBy !== null) {
            $params['sort'] = $orderBy;
        }

        return (array)\erLhcoreClassModelChat::getCount($params, 'COUNT', false, $select, false, true, false, false);
    }

    // -----------------------------------------------------------------------
    // Grouping
    // -----------------------------------------------------------------------

    /**
     * Resolves a `group_by` value into the SQL expression, an optional join and the label builder.
     *
     * @return array{expression: string, join: array, label: string}
     */
    public static function groupBy($groupBy)
    {
        $columns = array(
            'department' => 'dep_id',
            'operator' => 'user_id',
            'status' => 'status',
            'status_sub' => 'status_sub',
            'bot' => 'gbot_id',
            'device_type' => 'device_type',
            'country' => 'country_code',
            'channel' => 'iwh_id',
            'theme' => 'theme_id',
            'product' => 'product_id',
        );

        if (isset($columns[$groupBy])) {
            return array(
                'expression' => '`lh_chat`.`' . $columns[$groupBy] . '`',
                'join' => array(),
                'label' => $groupBy,
            );
        }

        if ($groupBy === 'subject') {
            return array(
                'expression' => '`lh_abstract_subject_chat`.`subject_id`',
                'join' => array('lh_abstract_subject_chat' => array('`lh_abstract_subject_chat`.`chat_id`', '`lh_chat`.`id`')),
                'label' => 'subject',
            );
        }

        if (isset(self::bucketFormats()[$groupBy])) {
            return array(
                'expression' => 'FROM_UNIXTIME(`lh_chat`.`time`, \'' . self::bucketFormats()[$groupBy] . '\')',
                'join' => array(),
                'label' => $groupBy,
            );
        }

        throw new ToolCallException('Unknown `group_by` `' . $groupBy . '`. Use one of: ' . implode(', ', self::GROUP_BY) . '.');
    }

    /**
     * Resolves an activity `interval`.
     *
     * @return array{expression: string, format: string, label: string, max_days: int}
     */
    public static function interval($interval)
    {
        $formats = self::bucketFormats();

        if (!isset($formats[$interval])) {
            throw new ToolCallException('Unknown `interval` `' . $interval . '`. Use one of: ' . implode(', ', self::INTERVALS) . '.');
        }

        return array(
            'expression' => 'FROM_UNIXTIME(`lh_chat`.`time`, \'' . $formats[$interval] . '\')',
            'format' => $formats[$interval],
            'label' => $interval,
            'max_days' => $interval === 'hour' ? 7 : ($interval === 'day' ? 31 : 400),
        );
    }

    /** Human readable bucket label. */
    public static function bucketLabel($interval, $key)
    {
        $key = (string)$key;

        switch ($interval) {
            case 'hour':
                $timestamp = mktime((int)substr($key, 8, 2), 0, 0, (int)substr($key, 4, 2), (int)substr($key, 6, 2), (int)substr($key, 0, 4));
                return date('Y-m-d H:00', $timestamp);
            case 'day':
                $timestamp = mktime(0, 0, 0, (int)substr($key, 4, 2), (int)substr($key, 6, 2), (int)substr($key, 0, 4));
                return date('Y-m-d', $timestamp);
            case 'month':
                return substr($key, 0, 4) . '-' . substr($key, 4, 2);
            case 'week':
                return substr($key, 0, 4) . '-W' . substr($key, 4);
            case 'weekday':
                $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                return isset($days[(int)$key]) ? $days[(int)$key] : 'weekday ' . $key;
        }

        return $key;
    }

    /**
     * Human readable group key. Identifiers only - names are never resolved.
     *
     * @return string
     */
    public static function groupLabel($groupBy, $key)
    {
        $prefixes = array(
            'department' => 'Department #',
            'operator' => 'User #',
            'bot' => 'Bot #',
            'theme' => 'Theme #',
            'product' => 'Product #',
            'channel' => 'Channel #',
            'subject' => 'Subject #',
        );

        // `0` is a real value in these columns - "nothing selected" rather than an identifier.
        $zero = array(
            'operator' => 'Unassigned',
            'bot' => 'No bot',
            'theme' => 'Default theme',
            'product' => 'No product',
            'channel' => 'No incoming webhook',
        );

        if ((int)$key === 0 && isset($zero[$groupBy])) {
            return $zero[$groupBy];
        }

        if (isset($prefixes[$groupBy])) {
            return $prefixes[$groupBy] . (int)$key;
        }

        if ($groupBy === 'status') {
            return self::statusLabel((int)$key);
        }

        if ($groupBy === 'device_type') {
            return self::deviceLabel((int)$key);
        }

        if ($groupBy === 'country') {
            return $key === '' ? 'unknown' : (string)$key;
        }

        if (isset(self::bucketFormats()[$groupBy])) {
            return self::bucketLabel($groupBy, $key);
        }

        return (string)$key;
    }

    /** Status constant name, matching the back office labels. */
    public static function statusLabel($status)
    {
        $statuses = array(
            \erLhcoreClassModelChat::STATUS_PENDING_CHAT => 'pending',
            \erLhcoreClassModelChat::STATUS_ACTIVE_CHAT => 'active',
            \erLhcoreClassModelChat::STATUS_CLOSED_CHAT => 'closed',
            \erLhcoreClassModelChat::STATUS_CHATBOX_CHAT => 'chatbox',
            \erLhcoreClassModelChat::STATUS_OPERATORS_CHAT => 'operators',
            \erLhcoreClassModelChat::STATUS_BOT_CHAT => 'bot',
        );

        return isset($statuses[$status]) ? $statuses[$status] : 'status ' . $status;
    }

    /** Device type label, matching the statistic module. */
    public static function deviceLabel($type)
    {
        $types = array(0 => 'PC', 1 => 'Mobile', 2 => 'Table');

        return isset($types[$type]) ? $types[$type] : 'device type ' . $type;
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    /**
     * `lh_chat` columns which may appear in a filter. Read from the model state so a new column
     * becomes filterable as soon as the model knows about it.
     *
     * @return array
     */
    public static function chatFields()
    {
        static $fields = null;

        if ($fields === null) {
            $fields = array_keys((new \erLhcoreClassModelChat())->getState());
            sort($fields);
        }

        return $fields;
    }

    /** Timestamp bucket formats, also used to detect the date based `group_by` values. */
    protected static function bucketFormats()
    {
        return array(
            'hour' => '%Y%m%d%H',
            'day' => '%Y%m%d',
            'week' => '%Y%v',
            'month' => '%Y%m',
            'weekday' => '%w',
        );
    }

    /** Resolves a period key into a `[from, to]` pair. */
    protected static function periodRange($key, $now)
    {
        switch ($key) {
            case '1h':
                return array($now - 3600, null);
            case '24h':
                return array($now - 86400, null);
            case '7d':
                return array($now - 7 * 86400, null);
            case '30d':
                return array($now - 30 * 86400, null);
            case 'today':
                return array(strtotime('today'), null);
            case 'yesterday':
                return array(strtotime('yesterday'), strtotime('today') - 1);
            case 'this_week':
                return array(strtotime('monday this week'), null);
            case 'last_week':
                return array(strtotime('monday last week'), strtotime('monday this week') - 1);
            case 'this_month':
                return array(strtotime(date('Y-m-01')), null);
            case 'last_month':
                return array(strtotime(date('Y-m-01', strtotime('first day of last month'))), strtotime(date('Y-m-01')) - 1);
        }

        return null;
    }

    /** Accepts a unix timestamp, an ISO 8601 date/time or a relative expression. */
    protected static function timestamp($value, $name)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (int)$value;
        }

        $value = trim((string)$value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{9,11}$/', $value) === 1) {
            return (int)$value;
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            throw new ToolCallException('`' . $name . '` has to be an ISO 8601 date/time, a unix timestamp or a relative expression like `-3 days`; got `' . $value . '`.');
        }

        return (int)$timestamp;
    }

    /**
     * Maps a caller supplied field name onto a qualified, allowlisted `lh_chat` column.
     *
     * @param mixed $field
     *
     * @return string
     */
    protected static function column($field)
    {
        if (!is_string($field) && !is_int($field)) {
            throw new ToolCallException('Filter fields have to be named columns, e.g. `status` or `lh_chat.dep_id`.');
        }

        $field = str_replace('`', '', trim((string)$field));

        if ($field === '' || preg_match('/^[A-Za-z0-9_.]+$/', $field) !== 1) {
            throw new ToolCallException('Unsupported filter field `' . $field . '`. Fields accept letters, digits, dots and underscores only.');
        }

        $name = $field;

        if (strpos($field, '.') !== false) {
            $parts = explode('.', $field);

            if (count($parts) !== 2) {
                throw new ToolCallException('Unsupported filter field `' . $field . '`. Only `lh_chat.<field>` is accepted.');
            }

            list($table, $name) = $parts;

            if ($table !== 'lh_chat') {
                throw new ToolCallException('Filter field `' . $field . '` points at table `' . $table . '`. Only `lh_chat` columns are accepted - call `get_chat_filter_fields` for the list.');
            }
        }

        if (!in_array($name, self::chatFields(), true)) {
            throw new ToolCallException('Unknown chat field `' . $name . '`. Call `get_chat_filter_fields` for the accepted `lh_chat` columns.');
        }

        return 'lh_chat.' . $name;
    }

    /** Validates a single filter value. */
    protected static function scalar($operator, $field, $value)
    {
        if ($value === null || is_array($value) || is_object($value)) {
            throw new ToolCallException('`' . $operator . '` on `' . $field . '` accepts scalars only.');
        }

        if (is_bool($value) || is_int($value) || is_float($value)) {
            return $value;
        }

        $value = trim((string)$value);

        if ($value === '') {
            throw new ToolCallException('`' . $operator . '` on `' . $field . '` got an empty value.');
        }

        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }

        return $value;
    }

    /** SQL-ish text of an operator, used in the readable filter summary. */
    protected static function operatorText($operator)
    {
        $texts = array(
            'filter' => '=',
            'filterin' => 'IN',
            'filterlt' => '<',
            'filterlte' => '<=',
            'filtergt' => '>',
            'filtergte' => '>=',
            'filterlike' => 'LIKE',
            'filternot' => '!=',
        );

        return isset($texts[$operator]) ? $texts[$operator] : $operator;
    }

    /** Renders a scalar for the readable filter summary. */
    protected static function valueText($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string)$value;
    }
}
