<?php

namespace LiveHelperChat\Mcp\Tools;

use LiveHelperChat\Mcp\ChatStats;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

/**
 * Tools answering quantitative questions about chats - how many chats there were in a period, how
 * they split over departments, operators, statuses or time, how long visitors waited and how fast
 * operators answered.
 *
 * All tools share one filter contract: the `filters` parameter takes the raw Live Helper Chat
 * filter map (`{"filter": {"status": 0}, "filtergte": {"time": 1758000000}}`). Call
 * `get_chat_filter_fields` once to learn the accepted operators and `lh_chat` fields.
 *
 * Everything is read only and installation wide: the endpoint token is the only access control, no
 * per operator department limitation is applied (department scoped access questions belong to
 * `explain_chat_access`).
 *
 * Output obeys the MCP output policy of this feature - identifiers and flags only, never department
 * names, operator names, visitor nicks, e-mail addresses, phone numbers or message bodies. The
 * `nick` parameter filters by a visitor nick (a case insensitive contains match, the same semantic
 * the back office chat search uses); the nick is only ever echoed back as the filter condition it
 * came in as, it is never resolved to a visitor profile or a chat.
 */
class ChatStatsTools
{
    /** Groups returned by `count_chats` when `limit` is omitted. */
    const DEFAULT_GROUPS = 50;

    /** Hard cap of `limit`, so a grouping cannot return an unbounded result. */
    const MAX_GROUPS = 500;

    /** Buckets returned by `get_chat_activity` when `limit` is omitted. */
    const DEFAULT_BUCKETS = 400;

    /** Hard cap of `get_last_chats` - the result set can never be larger. */
    const MAX_RESULTS = 5;

    /** Accepted `sort` values of `get_last_chats`. Sorting is always by chat id. */
    const SORT_ORDERS = array('newest', 'oldest');

    /**
     * Shared schema of the raw filter map - `array` would let the model send a JSON array.
     *
     * `array` is part of the accepted types on purpose: an empty JSON object (`{}`) is decoded into
     * an empty PHP array by the validator, so without it the natural "no filters" call would be
     * rejected before the tool runs. A non empty JSON array still reaches the tool and is refused
     * there with an explanatory message.
     */
    const FILTERS_SCHEMA = array(
        'type' => array('object', 'null', 'array'),
        'description' => 'Raw Live Helper Chat filter map: operator => { lh_chat field: value }. Allowed operators: filter, filterin, filtergte, filterlte, filtergt, filterlt, filterlike, filternot. Values are scalars, or a list for filter/filterin/filternot. The values are validated and the field names are checked against the lh_chat columns, raw SQL is refused. Call get_chat_filter_fields for the accepted fields.',
        'additionalProperties' => array(
            'type' => 'object',
            'additionalProperties' => array('type' => array('string', 'integer', 'number', 'boolean', 'array')),
        ),
        'examples' => array(
            array('filter' => array('status' => 0)),
            array('filterin' => array('dep_id' => array(1, 2))),
            array('filtergte' => array('time' => 1758000000)),
        ),
    );

    /**
     * Counts chats matching a raw filter map and an optional time window, optionally broken down by one dimension. Use it for "how many chats were there in the past 24 hours", "how many pending chats per department", "how many chats did user 12 handle last week", "how many chats came from Germany last month". Without a time window the last 24 hours are used, pass `period` = `all` to count the whole history. Pass `group_by` to get counts per department, operator, status, bot, device type, country, channel, theme, product, subject, day, week, month, hour or weekday; the flat `count` is always returned as well. Pass `nick` to add a visitor nick condition to the filter (`filters` stays available for everything else). Follow up with `get_last_chats` to list the most recent chat ids and dates for the same filters.
     */
    #[McpTool(name: 'count_chats', annotations: new ToolAnnotations(readOnlyHint: true))]
    #[Schema(properties: array('filters' => self::FILTERS_SCHEMA))]
    public function countChats(
        ?array $filters = null,
        #[Schema(description: 'Visitor nick to filter by, a case insensitive contains match. Use `filters` with `filter` on `nick` for an exact match.')]
        ?string $nick = null,
        #[Schema(description: 'Time window shorthand: 1h, 24h, 7d, 30d, today, yesterday, this_week, last_week, this_month, last_month or all. Defaults to 24h.')]
        ?string $period = null,
        #[Schema(description: 'Start of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_from = null,
        #[Schema(description: 'End of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_to = null,
        #[Schema(description: 'Optional grouping dimension.', enum: ChatStats::GROUP_BY)]
        ?string $group_by = null,
        #[Schema(description: 'How many groups to return when `group_by` is used. Default 50, maximum 500.', minimum: 1, maximum: 500)]
        ?int $limit = null
    ): array {
        $filter = ChatStats::withNick(ChatStats::filter($filters), $nick);
        $window = ChatStats::period($period, $time_from, $time_to, '24h');
        $params = ChatStats::merge($window['params'], $filter['params']);

        $groupBy = $group_by !== null && trim($group_by) !== '' ? strtolower(trim($group_by)) : null;
        $limitValue = $limit !== null && $limit > 0 ? min($limit, self::MAX_GROUPS) : self::DEFAULT_GROUPS;

        ChatStats::guard();

        $count = ChatStats::count($params);

        $result = array(
            'answer' => '',
            'count' => $count,
            'period' => self::window($window),
            'filters' => self::filters($filter),
            'group_by' => $groupBy,
            'groups' => null,
            'groups_returned' => null,
            'groups_total' => null,
            'notes' => array(),
        );

        if ($groupBy !== null) {
            $group = ChatStats::groupBy($groupBy);

            $groupParams = $params;

            if (!empty($group['join'])) {
                $groupParams['innerjoin'] = $group['join'];
            }

            $groupParams['limit'] = $limitValue;

            $rows = ChatStats::grouped(
                $groupParams,
                $group['expression'] . ' AS `group_key`, COUNT(*) AS `count`',
                $group['expression'],
                '`count` DESC'
            );

            $groups = array();

            foreach ($rows as $row) {
                $groups[] = array(
                    'key' => $row['group_key'],
                    'label' => ChatStats::groupLabel($groupBy, $row['group_key']),
                    'count' => (int)$row['count'],
                );
            }

            $groupsTotal = (int)array_sum(array_column($groups, 'count'));

            $result['groups'] = $groups;
            $result['groups_returned'] = count($groups);
            $result['groups_total'] = $groupsTotal;

            if (count($groups) >= $limitValue) {
                $result['notes'][] = 'Only the ' . $limitValue . ' largest groups are listed, further groups exist. Raise `limit` (maximum ' . self::MAX_GROUPS . ') or narrow `filters`.';
            } elseif (!empty($group['join']) && $groupsTotal < $count) {
                $result['notes'][] = 'The groups cover ' . $groupsTotal . ' of the ' . $count . ' matching chats: grouping by `' . $groupBy . '` only counts chats linked to that record (an inner join is used), the rest has none.';
            }
        }

        $result['answer'] = 'Chats matching the filters: ' . $count
            . ' (' . self::windowText($window)
            . ($groupBy !== null ? ', grouped by `' . $groupBy . '`' : '') . ').';

        return $result;
    }

    /**
     * Returns the most recent (or the oldest, see `sort`) chats matching the filter map, reduced to a chat id and the chat creation date. Use it as the follow up to a counting question - "how many chats with nick Visitor were there in the past 7 days?" and then "give me the last chat ids of this visitor" or "give me the oldest chats of this visitor". At most 5 chats are ever returned and each one carries only `chat_id` and `date` - no visitor or operator data and no message content. Unlike the other statistics tools it defaults to the whole history, so a follow up question without a period still finds the visitor's previous chats; pass `period` to narrow it. Every answer also carries `list_url` - the back office chat list with the same filters and ordering, so the returned chats can be verified manually.
     */
    #[McpTool(name: 'get_last_chats', annotations: new ToolAnnotations(readOnlyHint: true))]
    #[Schema(properties: array('filters' => self::FILTERS_SCHEMA))]
    public function getLastChats(
        ?array $filters = null,
        #[Schema(description: 'Visitor nick to filter by, a case insensitive contains match. Use `filters` with `filter` on `nick` for an exact match.')]
        ?string $nick = null,
        #[Schema(description: 'Time window shorthand: 1h, 24h, 7d, 30d, today, yesterday, this_week, last_week, this_month, last_month or all. Defaults to `all` - the most recent chats of the whole history.')]
        ?string $period = null,
        #[Schema(description: 'Start of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_from = null,
        #[Schema(description: 'End of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_to = null,
        #[Schema(description: 'Order of the returned chats, always by chat id: `newest` (highest chat id first, the default) or `oldest` (lowest chat id first).', enum: self::SORT_ORDERS)]
        ?string $sort = null,
        #[Schema(description: 'How many chats to return. Default 5, maximum 5 - a higher value is capped.', minimum: 1, maximum: 5)]
        ?int $limit = null
    ): array {
        $filter = ChatStats::withNick(ChatStats::filter($filters), $nick);
        $window = ChatStats::period($period, $time_from, $time_to, 'all');
        $params = ChatStats::merge($window['params'], $filter['params']);

        $sortKey = $sort !== null && trim($sort) !== '' ? strtolower(trim($sort)) : 'newest';

        if (!in_array($sortKey, self::SORT_ORDERS, true)) {
            throw new ToolCallException('Unknown `sort` `' . $sortKey . '`. Use one of: ' . implode(', ', self::SORT_ORDERS) . '.');
        }

        // The cap is enforced here, not only in the schema, so no call can widen it.
        $limitValue = $limit !== null && $limit > 0 ? min($limit, self::MAX_RESULTS) : self::MAX_RESULTS;

        // Built before `limit` / `sort` are added to the params - the link only cares about filters.
        $listUrl = ChatStats::chatListUrl($params, $sortKey);

        $params['limit'] = $limitValue;
        $params['sort'] = '`lh_chat`.`id` ' . ($sortKey === 'oldest' ? 'ASC' : 'DESC');

        ChatStats::guard();

        // Only these two columns are read - the tool cannot leak anything else.
        $rows = ChatStats::rows($params, '`lh_chat`.`id` AS `chat_id`, `lh_chat`.`time` AS `chat_time`');

        $chats = array();
        $readable = array();

        foreach ($rows as $row) {
            $chats[] = array(
                'chat_id' => (int)$row['chat_id'],
                'date' => date('Y-m-d H:i:s', (int)$row['chat_time']),
            );

            $readable[] = '#' . (int)$row['chat_id'] . ' (' . date('Y-m-d H:i:s', (int)$row['chat_time']) . ')';
        }

        $result = array(
            'answer' => empty($chats)
                ? 'No chat matches the filters.'
                : ($sortKey === 'oldest' ? 'First ' : 'Last ') . count($chats) . ' chat(s), ' . $sortKey . ' first: ' . implode(', ', $readable) . '.',
            'chats' => $chats,
            'returned' => count($chats),
            'limit' => $limitValue,
            'sort' => $sortKey,
            'period' => self::window($window),
            'filters' => self::filters($filter),
            'list_url' => $listUrl['url'],
            'notes' => array(
                ucfirst($sortKey) . ' first (chat id ' . ($sortKey === 'oldest' ? 'ascending' : 'descending') . '), at most ' . self::MAX_RESULTS . ' chats per call - the limit cannot be raised.',
                'A chat is described by its id and its creation date only.',
                '`list_url` opens the back office chat list with the same filters and ordering - use it to verify the returned chats manually.',
            ),
        );

        if (!$window['applied']) {
            $result['notes'][] = 'No time restriction was applied, the chats of the whole history were searched. Pass `period` to narrow it.';
        }

        if (!empty($listUrl['unmapped'])) {
            $result['notes'][] = 'Not part of `list_url` because the chat list has no matching filter: '
                . implode('; ', $listUrl['unmapped'])
                . '. The linked list can therefore contain more chats than counted here.';
        }

        return $result;
    }

    /**
     * Returns the chat volume of a time window in one call: total, pending, active, closed, chatbox, operators, bot, unanswered, abandoned and dropped chats, plus the number of visitor, operator, system and bot messages. Use it for "how many chats were there in the past 24 hours, how many are still pending, how many were abandoned and how many messages did customers send". Set `include_live` to also get the live per department counters (their current pending, active, bot and in operator chats). Without a time window the last 24 hours are used, pass `period` = `all` for the whole history. Pass `nick` to add a visitor nick condition to the filter.
     */
    #[McpTool(name: 'get_chat_statistics', annotations: new ToolAnnotations(readOnlyHint: true))]
    #[Schema(properties: array('filters' => self::FILTERS_SCHEMA))]
    public function getChatStatistics(
        ?array $filters = null,
        #[Schema(description: 'Visitor nick to filter by, a case insensitive contains match. Use `filters` with `filter` on `nick` for an exact match.')]
        ?string $nick = null,
        #[Schema(description: 'Time window shorthand: 1h, 24h, 7d, 30d, today, yesterday, this_week, last_week, this_month, last_month or all. Defaults to 24h.')]
        ?string $period = null,
        #[Schema(description: 'Start of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_from = null,
        #[Schema(description: 'End of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_to = null,
        #[Schema(description: 'Also return the live per department counters (current values, independent of the filter and the time window).')]
        ?bool $include_live = null,
        #[Schema(description: 'How many departments the live counters cover. Default 50, maximum 200.', minimum: 1, maximum: 200)]
        ?int $live_limit = null
    ): array {
        $filter = ChatStats::withNick(ChatStats::filter($filters), $nick);
        $window = ChatStats::period($period, $time_from, $time_to, '24h');
        $params = ChatStats::merge($window['params'], $filter['params']);

        ChatStats::guard();

        $summary = \erLhcoreClassChatStatistic::getLast24HStatistic($params);

        if (!is_array($summary)) {
            $summary = array();
        }

        $volume = array(
            'total' => isset($summary['totalchats']) ? (int)$summary['totalchats'] : 0,
            'pending' => isset($summary['totalpendingchats']) ? (int)$summary['totalpendingchats'] : 0,
            'active' => isset($summary['total_active_chats']) ? (int)$summary['total_active_chats'] : 0,
            'closed' => isset($summary['total_closed_chats']) ? (int)$summary['total_closed_chats'] : 0,
            'chatbox' => isset($summary['chatbox_chats']) ? (int)$summary['chatbox_chats'] : 0,
            'operators' => ChatStats::count(ChatStats::merge($params, array('filter' => array('lh_chat.status' => \erLhcoreClassModelChat::STATUS_OPERATORS_CHAT)))),
            'bot' => ChatStats::count(ChatStats::merge($params, array('filter' => array('lh_chat.status' => \erLhcoreClassModelChat::STATUS_BOT_CHAT)))),
            'unanswered' => isset($summary['total_unanswered_chat']) ? (int)$summary['total_unanswered_chat'] : 0,
            'abandoned' => isset($summary['abandoned_chats']) ? (int)$summary['abandoned_chats'] : 0,
            'dropped' => isset($summary['dropped_chats']) ? (int)$summary['dropped_chats'] : 0,
        );

        $messages = array(
            'total' => isset($summary['ttmall']) ? (int)$summary['ttmall'] : 0,
            'visitor' => isset($summary['ttmvis']) ? (int)$summary['ttmvis'] : 0,
            'system_and_bot' => isset($summary['ttmsys']) ? (int)$summary['ttmsys'] : 0,
            'operator' => isset($summary['ttmop']) ? (int)$summary['ttmop'] : 0,
        );

        $result = array(
            'answer' => 'Chats ' . self::windowText($window) . ': ' . $volume['total'] . ' total, '
                . $volume['pending'] . ' pending, ' . $volume['active'] . ' active, ' . $volume['closed'] . ' closed. '
                . $messages['total'] . ' messages.',
            'period' => self::window($window),
            'filters' => self::filters($filter),
            'volume' => $volume,
            'messages' => $messages,
            'live' => null,
            'notes' => array(
                'The status counters are computed with the requested filters plus the status itself, so `volume.total` and the individual statuses only add up when `filters` does not constrain `status`.',
                '`volume.unanswered`, `volume.abandoned` and `volume.dropped` overlap with the status counters - they describe a chat condition, not a status.',
                '`messages.visitor` counts messages sent by the visitor, `messages.system_and_bot` the system (-1) and bot (-2) messages, `messages.operator` the operator replies.',
                'The message counters follow the chat creation window, they are not filterable by message fields.',
            ),
        );

        if ($include_live === true) {
            $result['live'] = $this->liveDepartments($live_limit);
        }

        return $result;
    }

    /**
     * Returns a time series of chats per hour, day, week, month or weekday, each bucket with the total and the pending, active, closed, operators, bot and unanswered split. Use it for "how many chats per day last month", "which weekday is the busiest", "chats per hour yesterday". Buckets are ordered chronologically; `limit` keeps the most recent ones. The `hour` interval covers at most 7 days and `day` at most 31 days - use `week` or `month` for longer windows. Pass `nick` to add a visitor nick condition to the filter.
     */
    #[McpTool(name: 'get_chat_activity', annotations: new ToolAnnotations(readOnlyHint: true))]
    #[Schema(properties: array('filters' => self::FILTERS_SCHEMA))]
    public function getChatActivity(
        ?array $filters = null,
        #[Schema(description: 'Visitor nick to filter by, a case insensitive contains match. Use `filters` with `filter` on `nick` for an exact match.')]
        ?string $nick = null,
        #[Schema(description: 'Bucket size. Defaults to `day` for windows up to 31 days and `month` for longer ones.', enum: ChatStats::INTERVALS)]
        ?string $interval = null,
        #[Schema(description: 'Time window shorthand: 1h, 24h, 7d, 30d, today, yesterday, this_week, last_week, this_month, last_month or all. Defaults to 24h.')]
        ?string $period = null,
        #[Schema(description: 'Start of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_from = null,
        #[Schema(description: 'End of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_to = null,
        #[Schema(description: 'How many buckets to return, most recent first. Default 400.', minimum: 1, maximum: 400)]
        ?int $limit = null
    ): array {
        $filter = ChatStats::withNick(ChatStats::filter($filters), $nick);
        $window = ChatStats::period($period, $time_from, $time_to, '24h');
        $params = ChatStats::merge($window['params'], $filter['params']);

        $intervalKey = $interval !== null && trim($interval) !== '' ? strtolower(trim($interval)) : '';

        if ($intervalKey === '') {
            $span = self::span($window);
            $intervalKey = ($span !== null && $span <= 31 * 86400) ? 'day' : 'month';
        }

        $intervalInfo = ChatStats::interval($intervalKey);
        $span = self::span($window);

        if ($span !== null && $span > $intervalInfo['max_days'] * 86400) {
            throw new ToolCallException('The `' . $intervalKey . '` interval covers at most ' . $intervalInfo['max_days'] . ' days, the requested window is about ' . (int)ceil($span / 86400) . ' days. Use `interval` `week` or `month`, or shorten the period.');
        }

        if ($span === null && $intervalKey === 'hour') {
            throw new ToolCallException('`interval` `hour` needs a bounded window - use a `period` like `24h`/`7d` or pass `time_from` and `time_to`.');
        }

        $limitValue = $limit !== null && $limit > 0 ? min($limit, self::DEFAULT_BUCKETS) : self::DEFAULT_BUCKETS;

        ChatStats::guard();

        $expression = $intervalInfo['expression'];

        $select = $expression . ' AS `bucket`, COUNT(*) AS `total`'
            . ', SUM(`lh_chat`.`status` = ' . \erLhcoreClassModelChat::STATUS_PENDING_CHAT . ') AS `pending`'
            . ', SUM(`lh_chat`.`status` = ' . \erLhcoreClassModelChat::STATUS_ACTIVE_CHAT . ') AS `active`'
            . ', SUM(`lh_chat`.`status` = ' . \erLhcoreClassModelChat::STATUS_CLOSED_CHAT . ') AS `closed`'
            . ', SUM(`lh_chat`.`status` = ' . \erLhcoreClassModelChat::STATUS_OPERATORS_CHAT . ') AS `operators`'
            . ', SUM(`lh_chat`.`status` = ' . \erLhcoreClassModelChat::STATUS_BOT_CHAT . ') AS `bot`'
            . ', SUM(`lh_chat`.`unanswered_chat` = 1) AS `unanswered`';

        $params['limit'] = $limitValue;

        $rows = ChatStats::grouped($params, $select, $expression, '`bucket` DESC');

        // The limit keeps the newest buckets, the answer is chronological.
        $rows = array_reverse($rows);

        $buckets = array();
        $totals = array(
            'total' => 0,
            'pending' => 0,
            'active' => 0,
            'closed' => 0,
            'operators' => 0,
            'bot' => 0,
            'unanswered' => 0,
        );

        foreach ($rows as $row) {
            $bucket = array(
                'bucket' => $row['bucket'],
                'label' => ChatStats::bucketLabel($intervalKey, $row['bucket']),
            );

            foreach ($totals as $key => $unused) {
                $value = isset($row[$key]) ? (int)$row[$key] : 0;
                $bucket[$key] = $value;
                $totals[$key] += $value;
            }

            $buckets[] = $bucket;
        }

        $result = array(
            'answer' => 'Chats ' . self::windowText($window) . ' by `' . $intervalKey . '` in ' . count($buckets) . ' buckets: ' . $totals['total'] . ' total.',
            'period' => self::window($window),
            'filters' => self::filters($filter),
            'interval' => $intervalKey,
            'buckets' => $buckets,
            'buckets_returned' => count($buckets),
            'totals' => $totals,
            'notes' => array(),
        );

        if (count($buckets) >= $limitValue) {
            $result['notes'][] = 'Only the ' . $limitValue . ' most recent buckets are listed, older ones exist. Use a shorter period or a coarser interval.';
        }

        return $result;
    }

    /**
     * Returns how long visitors waited and how fast operators answered: the average chat duration, the average first response time (how long an operator took to send the first message), the average and maximum agent response time, plus optionally the wait time distribution in the same buckets the statistic dashboard uses and the workload per hour of day. Use it for "what is the average first response time last week", "how long do visitors wait on average", "when are we the busiest". Closed, operator handled chats are measured, exactly like the back office statistic. Times are seconds. Pass `nick` to add a visitor nick condition to the filter.
     */
    #[McpTool(name: 'get_chat_performance', annotations: new ToolAnnotations(readOnlyHint: true))]
    #[Schema(properties: array('filters' => self::FILTERS_SCHEMA))]
    public function getChatPerformance(
        ?array $filters = null,
        #[Schema(description: 'Visitor nick to filter by, a case insensitive contains match. Use `filters` with `filter` on `nick` for an exact match.')]
        ?string $nick = null,
        #[Schema(description: 'Time window shorthand: 1h, 24h, 7d, 30d, today, yesterday, this_week, last_week, this_month, last_month or all. Defaults to 30d.')]
        ?string $period = null,
        #[Schema(description: 'Start of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_from = null,
        #[Schema(description: 'End of the window, ISO 8601 or a unix timestamp. Overrides `period` when given.')]
        ?string $time_to = null,
        #[Schema(description: 'Also return the wait time distribution (how many chats waited 0-5s, 6-10s, ... more than 10 min).')]
        ?bool $include_wait_time = null,
        #[Schema(description: 'Also return the workload per hour of day.')]
        ?bool $include_workload = null
    ): array {
        $filter = ChatStats::withNick(ChatStats::filter($filters), $nick);
        $window = ChatStats::period($period, $time_from, $time_to, '30d');
        $params = ChatStats::merge($window['params'], $filter['params']);

        ChatStats::guard();

        // Same population the back office averages use: closed chats handled by an operator.
        $closed = ChatStats::merge($params, array(
            'filter' => array('lh_chat.status' => \erLhcoreClassModelChat::STATUS_CLOSED_CHAT),
            'filtergt' => array('lh_chat.user_id' => 0),
        ));

        $averages = array(
            'avg_chat_duration' => ChatStats::aggregate(ChatStats::merge($closed, array('filtergt' => array('lh_chat.chat_duration' => 0))), 'chat_duration'),
            'avg_first_response_time' => ChatStats::aggregate(ChatStats::merge($closed, array('filtergt' => array('lh_chat.frt' => 0))), 'frt'),
            'avg_agent_response_time' => ChatStats::aggregate(ChatStats::merge($closed, array('filtergt' => array('lh_chat.aart' => 0))), 'aart'),
            'avg_max_agent_response_time' => ChatStats::aggregate(ChatStats::merge($closed, array('filtergt' => array('lh_chat.mart' => 0))), 'mart'),
            'measured_chats' => ChatStats::count($closed),
        );

        $result = array(
            'answer' => 'Chat performance ' . self::windowText($window) . ': '
                . self::seconds($averages['avg_first_response_time']) . ' first response, '
                . self::seconds($averages['avg_agent_response_time']) . ' agent response, '
                . self::seconds($averages['avg_chat_duration']) . ' duration ('
                . $averages['measured_chats'] . ' closed operator chats).',
            'period' => self::window($window),
            'filters' => self::filters($filter),
            'averages' => $averages,
            'wait_time' => null,
            'workload' => null,
            'notes' => array(
                'All times are seconds. Only closed chats handled by an operator (status closed, `user_id` > 0) are measured, like in the back office statistic.',
                'The averages ignore chats where the measured field is 0, a chat without an operator reply does not pull the average down.',
            ),
        );

        foreach ($averages as $name => $value) {
            if ($value === null) {
                $result['notes'][] = 'No data for `' . $name . '`, no chat in the window has that measurement.';
            }
        }

        if ($include_wait_time === true) {
            $result['wait_time'] = $this->waitTimeDistribution($params);
        }

        if ($include_workload === true) {
            $result['workload'] = $this->workload($params);
        }

        return $result;
    }

    /**
     * Lists everything the raw `filters` parameter of the chat statistics tools accepts - the allowed operators, the filterable `lh_chat` fields, the `group_by` dimensions, the `interval` values and the `period` shorthands. Call it once before the first statistics question instead of guessing field names.
     */
    #[McpTool(name: 'get_chat_filter_fields', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function getChatFilterFields(): array
    {
        return ChatStats::filterReference();
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /** Live per department counters, current values, independent of any filter. */
    protected function liveDepartments($liveLimit)
    {
        $limit = $liveLimit !== null && $liveLimit > 0 ? min($liveLimit, 200) : 50;

        $departments = \erLhcoreClassModelDepartament::getList(array(
            'limit' => $limit,
            'sort' => '`pending_chats_counter` DESC, `id` ASC',
            'filter' => array('archive' => 0),
        ));

        $live = array();

        foreach ($departments as $department) {
            $live[] = array(
                'dep_id' => (int)$department->id,
                'label' => 'Department #' . (int)$department->id,
                'pending' => (int)$department->pending_chats_counter,
                'active' => (int)$department->active_chats_counter,
                'bot' => (int)$department->bot_chats_counter,
                'in_operator' => (int)$department->inop_chats_cnt,
                'active_operator' => (int)$department->acop_chats_cnt,
                'inactive' => (int)$department->inactive_chats_cnt,
                'disabled' => $department->disabled == 1,
                'offline' => $department->dep_offline == 1,
            );
        }

        return array(
            'departments' => $live,
            'returned' => count($live),
            'notes' => array(
                'These are the current counters, not history - the filter and the time window do not apply.',
                'Archived departments are excluded.',
            ),
        );
    }

    /** Wait time distribution in the same buckets the statistic dashboard uses. */
    protected function waitTimeDistribution(array $params)
    {
        $ranges = \erLhcoreClassChatStatistic::getRangeWaitTime();

        $parts = array();

        foreach ($ranges as $index => $range) {
            $conditions = array();

            if ($range['from'] !== false) {
                $conditions[] = '`lh_chat`.`wait_time` >= ' . (int)$range['from'];
            }

            if ($range['to'] !== false) {
                $conditions[] = '`lh_chat`.`wait_time` <= ' . (int)$range['to'];
            }

            $parts[] = 'SUM(' . implode(' AND ', $conditions) . ') AS `r' . $index . '`';
        }

        $row = ChatStats::row($params, implode(', ', $parts));

        $buckets = array();

        foreach ($ranges as $index => $range) {
            $buckets[] = array(
                'range' => $range['tt'],
                'from' => $range['from'],
                'to' => $range['to'],
                'chats' => isset($row['r' . $index]) ? (int)$row['r' . $index] : 0,
            );
        }

        return array(
            'buckets' => $buckets,
            'notes' => array('All chats matching the filters, not only the closed ones.'),
        );
    }

    /** Chats per hour of day (0-23). */
    protected function workload(array $params)
    {
        $expression = 'FROM_UNIXTIME(`lh_chat`.`time`, \'%k\')';

        $rows = ChatStats::grouped($params, $expression . ' AS `hour_key`, COUNT(*) AS `total`', $expression, '`hour_key` ASC');

        $byHour = array();

        foreach ($rows as $row) {
            $byHour[(int)$row['hour_key']] = (int)$row['total'];
        }

        $workload = array();

        for ($hour = 0; $hour < 24; $hour++) {
            $workload[] = array(
                'hour' => str_pad((string)$hour, 2, '0', STR_PAD_LEFT) . ':00',
                'chats' => isset($byHour[$hour]) ? $byHour[$hour] : 0,
            );
        }

        return array(
            'by_hour' => $workload,
            'notes' => array('Buckets use the server time zone.'),
        );
    }

    /** Length of the resolved window in seconds, or null when the window is open ended. */
    protected static function span(array $window)
    {
        if ($window['from'] === null) {
            return null;
        }

        return ($window['to'] !== null ? $window['to'] : time()) - $window['from'];
    }

    /** Readable period block. */
    protected static function window(array $window)
    {
        return array(
            'label' => $window['label'],
            'from' => $window['from'],
            'to' => $window['to'],
            'restricted' => $window['applied'],
        );
    }

    /** Readable period sentence. */
    protected static function windowText(array $window)
    {
        if (!$window['applied']) {
            return 'over the whole history';
        }

        return 'in the ' . $window['label'] . ' window';
    }

    /** Readable filter block - no field values beyond what the caller passed. */
    protected static function filters(array $filter)
    {
        return array(
            'applied' => $filter['params'],
            'summary' => $filter['summary'],
        );
    }

    /** Formats a second count for the one line answer. */
    protected static function seconds($value)
    {
        return $value === null ? 'no data' : round($value, 1) . 's';
    }
}
