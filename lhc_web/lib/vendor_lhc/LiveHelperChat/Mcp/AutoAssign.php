<?php

namespace LiveHelperChat\Mcp;

/**
 * Read only port of the chat auto assignment rules implemented by
 * `erLhcoreClassChatWorkflow::autoAssign()` (lib/core/lhchat/lhchatworkflow.php).
 *
 * The MCP tools use this class to answer questions like
 *
 *   "Would chat #123 be auto assigned?"
 *   "Why was operator example@example.com not auto assigned to chat #123?"
 *
 * Nothing here writes or locks anything. The three SELECT statements of the workflow are replayed
 * without the `FOR UPDATE` lock and every gate is re-evaluated as a pure boolean, so a diagnosis can
 * safely run against a live installation.
 *
 * Output obeys the MCP output policy: identifiers and flags only - no department names, operator
 * names, chat nicks or message bodies.
 */
class AutoAssign
{
    /**
     * Operator activity window used by `erLhcoreClassChat::lockOperatorsByDepartment()`.
     * It is hard coded in the core and differs on purpose from `sync_sound_settings`.`online_timeout`,
     * which is what the candidate queries below use.
     */
    const LOCK_ACTIVITY_WINDOW = 600;

    /**
     * How many candidate rows each variant returns. The workflow itself uses `LIMIT 3` and takes the
     * first row, the extra rows are only reported so the ordering can be explained - the picked
     * operator is the same.
     */
    const CANDIDATE_LIMIT = 20;

    /** Human readable chat status, the workflow only processes `pending` chats. */
    public static function statusLabel($status)
    {
        switch ((int)$status) {
            case \erLhcoreClassModelChat::STATUS_PENDING_CHAT:
                return 'pending';
            case \erLhcoreClassModelChat::STATUS_ACTIVE_CHAT:
                return 'active';
            case \erLhcoreClassModelChat::STATUS_CLOSED_CHAT:
                return 'closed';
            case \erLhcoreClassModelChat::STATUS_CHATBOX_CHAT:
                return 'chatbox';
            case \erLhcoreClassModelChat::STATUS_OPERATORS_CHAT:
                return 'operators';
            case \erLhcoreClassModelChat::STATUS_BOT_CHAT:
                return 'bot';
        }

        return 'unknown';
    }

    /** Operator online timeout used by the candidate queries (`sync_sound_settings`.`online_timeout`). */
    public static function onlineTimeout()
    {
        $settings = \erLhcoreClassModelChatConfig::fetch('sync_sound_settings');

        return isset($settings->data['online_timeout']) ? (int)$settings->data['online_timeout'] : 0;
    }

    /** Global values auto assignment depends on, they are not part of any department. */
    public static function globalConfig()
    {
        return array(
            'online_timeout' => self::onlineTimeout(),
            'operator_lock_activity_window' => self::LOCK_ACTIVITY_WINDOW,
            'assign_workflow_timeout' => (int)\erLhcoreClassModelChatConfig::fetch('assign_workflow_timeout')->current_value,
        );
    }

    /** Chat values the auto assignment decisions are based on. */
    public static function chatSummary($chat)
    {
        return array(
            'id' => (int)$chat->id,
            'status' => (int)$chat->status,
            'status_label' => self::statusLabel($chat->status),
            'status_sub' => (int)$chat->status_sub,
            'dep_id' => (int)$chat->dep_id,
            'label' => 'Department #' . (int)$chat->dep_id,
            'assignee_user_id' => (int)$chat->user_id,
            'priority' => (int)$chat->priority,
            'chat_locale' => (string)$chat->chat_locale,
            'ctime' => (int)$chat->time,
            'pending_time' => (int)$chat->pnd_time,
            'tslasign' => (int)$chat->tslasign,
            'wait_time' => (int)$chat->wait_time,
        );
    }

    /**
     * Auto assignment settings stored on the department.
     *
     * `bot_configuration` keys are normalized exactly the way the workflow compares them, so the
     * reported values are the ones the workflow actually acts on (`== '1'` for the checkboxes and
     * `(int)` for the numeric ones).
     */
    public static function departmentConfig($department)
    {
        $bot = $department->bot_configuration_array;

        return array(
            'id' => (int)$department->id,
            'label' => 'Department #' . (int)$department->id,
            'active_balancing' => (int)$department->active_balancing,
            'max_active_chats' => (int)$department->max_active_chats,
            'max_ac_dep_chats' => (int)$department->max_ac_dep_chats,
            'active_chats_counter' => (int)$department->active_chats_counter,
            'pending_chats_counter' => (int)$department->pending_chats_counter,
            'delay_before_assign' => (int)$department->delay_before_assign,
            'max_timeout_seconds' => (int)$department->max_timeout_seconds,
            'exclude_inactive_chats' => (int)$department->exclude_inactive_chats,
            'assign_same_language' => (int)$department->assign_same_language,
            'disabled' => (int)$department->disabled,
            'hidden' => (int)$department->hidden,
            'archive' => (int)$department->archive,
            'bot_configuration' => self::botConfiguration($department),
        );
    }

    /** Normalized `bot_configuration_array` keys which take part in the auto assignment decision. */
    public static function botConfiguration($department)
    {
        $bot = $department->bot_configuration_array;

        return array(
            'auto_delay_var' => isset($bot['auto_delay_var']) ? (string)$bot['auto_delay_var'] : '',
            'auto_delay_timeout' => isset($bot['auto_delay_timeout']) ? (int)$bot['auto_delay_timeout'] : 0,
            // `== '1'` mirrors erLhcoreClassChatWorkflow::autoAssign() verbatim.
            'auto_lower_limit' => self::flag($bot, 'auto_lower_limit') ? 1 : 0,
            'assign_by_priority' => self::flag($bot, 'assign_by_priority') ? 1 : 0,
            'active_prioritized_assignment' => self::flag($bot, 'active_prioritized_assignment') ? 1 : 0,
            'assign_by_priority_chat' => self::flag($bot, 'assign_by_priority_chat') ? 1 : 0,
            'only_priority' => (isset($bot['only_priority']) && (int)$bot['only_priority'] == 1) ? 1 : 0,
            'min_agent_priority' => isset($bot['min_agent_priority']) ? (int)$bot['min_agent_priority'] : 0,
            'min_chat_priority' => isset($bot['min_chat_priority']) ? (int)$bot['min_chat_priority'] : 0,
            'max_chat_priority' => isset($bot['max_chat_priority']) ? (int)$bot['max_chat_priority'] : 0,
        );
    }

    /**
     * Does the chat priority queue of the department cover this chat?
     *
     * The queue is only consulted when `active_prioritized_assignment` is enabled and the chat
     * priority is inside the configured inclusive priority range (0 means "no bound").
     */
    public static function queueContext($chat, $department)
    {
        $botConfiguration = self::botConfiguration($department);

        $enabled = (int)$botConfiguration['active_prioritized_assignment'] === 1;
        $minChat = (int)$botConfiguration['min_chat_priority'];
        $maxChat = (int)$botConfiguration['max_chat_priority'];
        $priority = (int)$chat->priority;

        $applies = $enabled
            && !($minChat != 0 && $priority < $minChat)
            && !($maxChat != 0 && $priority > $maxChat);

        return array(
            'enabled' => $enabled,
            'applies' => $applies,
            'min_chat_priority' => $minChat,
            'max_chat_priority' => $maxChat,
            'chat_priority' => $priority,
        );
    }

    /**
     * The gates which have to pass before any operator is even looked up - the top level `if`s of
     * `erLhcoreClassChatWorkflow::autoAssign()`.
     *
     * Returns `allowed`, the individual `checks` (same shape as `Access::checkEntry()`) and the
     * machine readable `blocked_by` identifiers.
     */
    public static function gates($chat, $department, $timestamp = null)
    {
        $timestamp = $timestamp === null ? (int)time() : (int)$timestamp;

        if (!is_object($department)) {
            return array(
                'allowed' => false,
                'blocked_by' => array('department_missing'),
                'checks' => array(Access::checkEntry(
                    'department.exists',
                    false,
                    'Department #' . (int)$chat->dep_id . ' was not found.',
                    'Without a department there is nothing to balance chats between.'
                )),
            );
        }

        $checks = array();
        $blockedBy = array();

        // 1. Auto assignment has to be enabled on the department.
        $checks[] = Access::checkEntry(
            'department.active_balancing',
            $department->active_balancing == 1,
            '`lh_departament`.`active_balancing` = ' . (int)$department->active_balancing . '.',
            'The whole workflow is skipped unless `Active chats auto-assignment` is enabled on the department.'
        );
        if ($department->active_balancing != 1) {
            $blockedBy[] = 'department_auto_balancing_disabled';
        }

        // 2. Department wide limit of simultaneously active chats.
        $counter = (int)$department->active_chats_counter;
        $maxDepartmentChats = (int)$department->max_ac_dep_chats;
        $checks[] = Access::checkEntry(
            'department.max_ac_dep_chats',
            $maxDepartmentChats == 0 || $counter < $maxDepartmentChats,
            'Active chats in department: ' . $counter . ', limit: ' . ($maxDepartmentChats == 0 ? 'unlimited' : $maxDepartmentChats) . '.',
            'When the department active chats limit is reached no chat is assigned to any operator.'
        );
        if (!($maxDepartmentChats == 0 || $counter < $maxDepartmentChats)) {
            $blockedBy[] = 'department_max_active_chats_reached';
        }

        // 3. Only pending chats are processed.
        $checks[] = Access::checkEntry(
            'chat.status',
            $chat->status == \erLhcoreClassModelChat::STATUS_PENDING_CHAT,
            'Chat status: ' . (int)$chat->status . ' (' . self::statusLabel($chat->status) . ').',
            'Chats are auto assigned while they are pending - bot, active and closed chats are out of scope.'
        );
        if ($chat->status != \erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
            $blockedBy[] = 'chat_not_pending';
        }

        // 4. An already assigned chat is only re-assigned after max_timeout_seconds.
        $reassignAllowed = $chat->user_id == 0
            || ((int)$department->max_timeout_seconds > 0 && (int)$chat->tslasign < $timestamp - (int)$department->max_timeout_seconds);
        $checks[] = Access::checkEntry(
            'chat.assignment_gate',
            $reassignAllowed,
            'Assigned operator: #' . (int)$chat->user_id . ', assigned at: ' . (int)$chat->tslasign
                . ', `max_timeout_seconds`: ' . (int)$department->max_timeout_seconds . '.',
            'An assigned chat is only taken away from the operator when `Automatically assign chat to another operator if operator did not accepted chat in seconds` is set and has elapsed.'
        );
        if (!$reassignAllowed) {
            $blockedBy[] = 'chat_assigned_without_reassign_timeout';
        }

        // 5. Optional "check for presence of a variable" delay.
        $delay = self::delayGate($chat, $department, $timestamp);
        $checks[] = $delay['check'];
        if ($delay['blocked_by'] != '') {
            $blockedBy[] = $delay['blocked_by'];
        }

        // 6. The workflow locks the department operators first and returns early when there is none.
        $activeOperators = self::recentlyActiveOperatorCount((int)$department->id, $timestamp);
        $checks[] = Access::checkEntry(
            'department.recently_active_operators',
            $activeOperators > 0,
            'Operators of department #' . (int)$department->id . ' with `hide_online` = 0, `ro` = 0 and `last_activity` within the last '
                . self::LOCK_ACTIVITY_WINDOW . ' seconds: ' . $activeOperators . '.',
            'Before any operator is picked the department operators are locked (`erLhcoreClassChat::lockOperatorsByDepartment()`). Without a recently active operator the workflow stops with "Locking operators failed" - note `always_on` alone does not satisfy this lock.'
        );
        if ($activeOperators === 0) {
            $blockedBy[] = 'no_recently_active_operators';
        }

        return array(
            'allowed' => empty($blockedBy),
            'blocked_by' => $blockedBy,
            'checks' => $checks,
        );
    }

    /**
     * Number of `lh_userdep` rows in the department which satisfy the operator lock query.
     * Rows pointing at `dep_id` 0 (all departments) or -1 (read only all departments) are not part of
     * this count - the lock query matches the exact department ID only.
     */
    public static function recentlyActiveOperatorCount($depId, $timestamp = null)
    {
        $timestamp = $timestamp === null ? (int)time() : (int)$timestamp;

        $db = \ezcDbInstance::get();
        $stmt = $db->prepare('SELECT COUNT(*) FROM `lh_userdep` WHERE `dep_id` = :dep_id AND `hide_online` = 0 AND `ro` = 0 AND `last_activity` > :last_activity');
        $stmt->bindValue(':dep_id', (int)$depId, \PDO::PARAM_INT);
        $stmt->bindValue(':last_activity', $timestamp - self::LOCK_ACTIVITY_WINDOW, \PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * The `auto_delay_var` / `auto_delay_timeout` postponement of the department.
     *
     * While the chat is younger than `auto_delay_timeout` seconds the workflow requires the configured
     * variable to be present in the chat (`additional_data.X` or `chat_variable.X`). When the variable
     * name points at neither of them the workflow asks the `chat.workflow.autoassign_permit` event -
     * an MCP tool cannot dispatch that event, so such a chat is reported as undecidable.
     */
    protected static function delayGate($chat, $department, $timestamp)
    {
        $botConfiguration = self::botConfiguration($department);
        $delayVar = trim($botConfiguration['auto_delay_var']);
        $delayTimeout = (int)$botConfiguration['auto_delay_timeout'];
        $active = $delayVar != '' && $delayTimeout > 0 && ($timestamp - (int)$chat->time) < $delayTimeout;

        if (!$active) {
            return array(
                'blocked_by' => '',
                'check' => Access::checkEntry(
                    'department.auto_delay_var',
                    true,
                    'Delay variable: ' . ($delayVar == '' ? 'not configured' : '`' . $delayVar . '`') . ', delay: ' . $delayTimeout . ' seconds, chat age: ' . ($timestamp - (int)$chat->time) . ' seconds.',
                    'The presence check only applies while the chat is younger than `auto_delay_timeout` seconds.',
                    array('affects' => false)
                ),
            );
        }

        if (strpos($delayVar, 'additional_data') !== false) {
            $identifier = str_replace('additional_data.', '', $delayVar);
            $found = false;

            foreach ((array)$chat->additional_data_array as $additionalItem) {
                $key = isset($additionalItem['identifier']) ? $additionalItem['identifier'] : (isset($additionalItem['key']) ? $additionalItem['key'] : '');

                if ($key != '' && $key == $identifier) {
                    // The workflow only keeps looking when the stored value is null (`$valueToCompare === null`).
                    $found = isset($additionalItem['value']) && $additionalItem['value'] !== null;
                    break;
                }
            }

            return array(
                'blocked_by' => $found ? '' : 'auto_delay_var_missing',
                'check' => Access::checkEntry(
                    'department.auto_delay_var',
                    $found,
                    '`additional_data.' . $identifier . '` is ' . ($found ? 'present' : 'missing') . ' in the chat, chat age: ' . ($timestamp - (int)$chat->time) . ' of ' . $delayTimeout . ' seconds.',
                    'While the chat is younger than `auto_delay_timeout` seconds the chat is not auto assigned until the configured chat variable is present.'
                ),
            );
        }

        if (strpos($delayVar, 'chat_variable') !== false) {
            $variableName = str_replace('chat_variable.', '', $delayVar);
            $variables = $chat->chat_variables_array;
            $found = is_array($variables) && isset($variables[$variableName]) && $variables[$variableName] != '';

            return array(
                'blocked_by' => $found ? '' : 'auto_delay_var_missing',
                'check' => Access::checkEntry(
                    'department.auto_delay_var',
                    $found,
                    '`chat_variable.' . $variableName . '` is ' . ($found ? 'present' : 'missing') . ' in the chat, chat age: ' . ($timestamp - (int)$chat->time) . ' of ' . $delayTimeout . ' seconds.',
                    'While the chat is younger than `auto_delay_timeout` seconds the chat is not auto assigned until the configured chat variable is present.'
                ),
            );
        }

        return array(
            'blocked_by' => 'auto_delay_var_extension',
            'check' => Access::checkEntry(
                'department.auto_delay_var',
                false,
                'Delay variable `' . $delayVar . '` is neither an `additional_data` nor a `chat_variable` reference, chat age: ' . ($timestamp - (int)$chat->time) . ' of ' . $delayTimeout . ' seconds.',
                'Such a variable is resolved by the `chat.workflow.autoassign_permit` event. The MCP tool cannot dispatch the event, so the chat is reported as postponed until an extension permits the assignment.'
            ),
        );
    }

    /** All `lh_userdep` rows of a department - one row per assigned operator (or department group). */
    public static function departmentRows($depId)
    {
        return \erLhcoreClassModelUserDep::getList(array(
            'limit' => false,
            'filter' => array('dep_id' => (int)$depId),
        ));
    }

    /** All `lh_userdep` rows of one operator, across every department. */
    public static function userRows($userId)
    {
        return \erLhcoreClassModelUserDep::getList(array(
            'limit' => false,
            'filter' => array('user_id' => (int)$userId),
            'sort' => 'dep_id ASC',
        ));
    }

    /**
     * One row of `lh_userdep` summarized with the values auto assignment looks at. Rows with
     * `dep_id` 0 or -1 are reported with a generic label - they are global assignments and are never
     * matched by the department candidate queries.
     */
    public static function rowSummary($row, $timestamp = null)
    {
        $timestamp = $timestamp === null ? (int)time() : (int)$timestamp;

        return array(
            'user_dep_id' => (int)$row->id,
            'user_id' => (int)$row->user_id,
            'dep_id' => (int)$row->dep_id,
            'dep_label' => self::departmentLabel($row->dep_id),
            'type' => (int)$row->type,
            'dep_group_id' => (int)$row->dep_group_id,
            'ro' => (int)$row->ro,
            'hide_online' => (int)$row->hide_online,
            'always_on' => (int)$row->always_on,
            'last_activity' => (int)$row->last_activity,
            'last_activity_age' => $row->last_activity > 0 ? $timestamp - (int)$row->last_activity : null,
            'last_accepted' => (int)$row->last_accepted,
            'last_accepted_age' => $row->last_accepted > 0 ? $timestamp - (int)$row->last_accepted : null,
            'exclude_autoasign' => (int)$row->exclude_autoasign,
            'exc_indv_autoasign' => (int)$row->exc_indv_autoasign,
            'only_priority' => (int)$row->only_priority,
            'assign_priority' => (int)$row->assign_priority,
            'chat_min_priority' => (int)$row->chat_min_priority,
            'chat_max_priority' => (int)$row->chat_max_priority,
            'max_chats' => (int)$row->max_chats,
            'active_chats' => (int)$row->active_chats,
            'pending_chats' => (int)$row->pending_chats,
            'inactive_chats' => (int)$row->inactive_chats,
            'live_chats' => (int)$row->active_chats + (int)$row->pending_chats - (int)$row->inactive_chats,
        );
    }

    /** Generic placeholder for a department ID, `0` and `-1` are the global assignments. */
    public static function departmentLabel($depId)
    {
        if ((int)$depId == 0) {
            return 'all departments';
        }
        if ((int)$depId == -1) {
            return 'all departments (read only)';
        }

        return 'Department #' . (int)$depId;
    }

    /**
     * Is the operator considered online by the candidate queries?
     *
     * Same condition as the `(last_activity > :last_activity OR always_on = 1)` part combined with
     * `hide_online = 0` - `always_on` operators are considered online around the clock.
     */
    public static function operatorOnline($row, $timestamp = null)
    {
        $timestamp = $timestamp === null ? (int)time() : (int)$timestamp;

        return $row->hide_online == 0
            && ((int)$row->last_activity > $timestamp - self::onlineTimeout() || $row->always_on == 1);
    }

    /** How many chats the candidate queries count for the operator (`exclude_inactive_chats` aware). */
    public static function countedChats($department, $row)
    {
        if ($department->exclude_inactive_chats == 1) {
            return ((int)$row->pending_chats + (int)$row->active_chats) - (int)$row->inactive_chats;
        }

        return (int)$row->active_chats + (int)$row->pending_chats;
    }

    /**
     * The `max_chats` / `max_active_chats` capacity condition of the candidate queries.
     * The tighter of the personal limit and the department wide per operator limit wins, 0 means
     * unlimited and neither of them counts as a limit.
     */
    public static function capacityOk($department, $row)
    {
        $countedChats = self::countedChats($department, $row);
        $maxChats = (int)$row->max_chats;
        $maxActiveChats = (int)$department->max_active_chats;

        if ($maxActiveChats > 0) {
            return ($maxChats == 0 && $countedChats < $maxActiveChats) || ($maxChats > 0 && $countedChats < $maxChats);
        }

        return ($maxChats > 0 && $countedChats < $maxChats) || $maxChats == 0;
    }

    /** Minimum delay between two assignments of the same operator (`last_accepted`). */
    public static function operatorDelayOk($department, $row, $timestamp = null)
    {
        $timestamp = $timestamp === null ? (int)time() : (int)$timestamp;

        return (int)$row->last_accepted < $timestamp - (int)$department->delay_before_assign;
    }

    /**
     * Why would this `lh_userdep` row (not) receive the chat?
     *
     * Mirrors the `WHERE` clause of the three candidate queries. `pickable` is the final verdict,
     * `paths` shows which of the query variants could pick the row at all.
     */
    public static function operatorChecks($chat, $department, $row, $timestamp = null)
    {
        $timestamp = $timestamp === null ? (int)time() : (int)$timestamp;

        $botConfiguration = self::botConfiguration($department);
        $queue = self::queueContext($chat, $department);
        $onlineTimeout = self::onlineTimeout();
        $delayBeforeAssign = (int)$department->delay_before_assign;
        $chatPriority = (int)$chat->priority;

        $checks = array();
        $blockedBy = array();

        // --- `lh_userdep` row level flags -------------------------------------
        $checks[] = Access::checkEntry(
            'operator.ro',
            $row->ro == 0,
            '`lh_userdep`.`ro` = ' . (int)$row->ro . '.',
            'A read only department assignment can read chats but never receives them.'
        );
        if ($row->ro != 0) {
            $blockedBy[] = 'read_only_department_assignment';
        }

        $online = self::operatorOnline($row, $timestamp);

        $checks[] = Access::checkEntry(
            'operator.online',
            $online,
            '`hide_online` = ' . (int)$row->hide_online . ', `always_on` = ' . (int)$row->always_on
                . ', `last_activity` ' . ($row->last_activity > 0 ? ($timestamp - (int)$row->last_activity) . ' seconds ago' : 'never updated')
                . ', online timeout ' . $onlineTimeout . ' seconds.',
            'Only operators whose `last_activity` is fresher than the online timeout - or who are always on - are considered.'
        );
        if (!$online) {
            $blockedBy[] = 'operator_offline';
        }

        $notExcluded = $row->exclude_autoasign == 0 && $row->exc_indv_autoasign == 0;
        $checks[] = Access::checkEntry(
            'operator.exclude_autoasign',
            $notExcluded,
            '`exclude_autoasign` = ' . (int)$row->exclude_autoasign . ', `exc_indv_autoasign` = ' . (int)$row->exc_indv_autoasign . '.',
            'Both the account level and the per department exclusion flag keep the operator out of the auto assignment.'
        );
        if ($row->exclude_autoasign != 0) {
            $blockedBy[] = 'exclude_autoasign';
        }
        if ($row->exc_indv_autoasign != 0) {
            $blockedBy[] = 'exc_indv_autoasign';
        }

        $delayOk = self::operatorDelayOk($department, $row, $timestamp);
        $checks[] = Access::checkEntry(
            'operator.delay_before_assign',
            $delayOk,
            '`last_accepted` ' . ($row->last_accepted > 0 ? ($timestamp - (int)$row->last_accepted) . ' seconds ago' : 'never') . ', minimum delay ' . $delayBeforeAssign . ' seconds.',
            'A chat can only be handed to an operator when `Minimum delay between chat assignment to operator` has passed since his last assignment.'
        );
        if (!$delayOk) {
            $blockedBy[] = 'delay_before_assign';
        }

        // --- capacity --------------------------------------------------------
        $chatsInDepartment = self::countedChats($department, $row);
        $maxChats = (int)$row->max_chats;
        $maxActiveChats = (int)$department->max_active_chats;
        $capacityOk = self::capacityOk($department, $row);

        $checks[] = Access::checkEntry(
            'operator.capacity',
            $capacityOk,
            'Chats counted for the operator: ' . $chatsInDepartment
                . ($department->exclude_inactive_chats == 1 ? ' (inactive chats excluded)' : '')
                . ', `lh_userdep`.`max_chats` = ' . ($maxChats == 0 ? 'unlimited' : $maxChats)
                . ', department `max_active_chats` = ' . ($maxActiveChats == 0 ? 'unlimited' : $maxActiveChats) . '.',
            'The tighter of the personal chat limit and the department wide per operator limit decides, 0 means unlimited.'
        );
        if (!$capacityOk) {
            $blockedBy[] = 'operator_max_chats_reached';
        }

        // --- chat priority queue vs. the default query ------------------------
        // Failing the queue is not automatically a hard block - the workflow falls back to the same
        // language and the default query, which only skip operators who opted into the queue.
        $queueOk = null;
        $queueBlockedBy = array();

        if ($queue['applies']) {
            $minAgentPriority = (int)$botConfiguration['min_agent_priority'];
            $agentPriorityOk = $minAgentPriority == 0 || (int)$row->assign_priority >= $minAgentPriority;

            $checks[] = Access::checkEntry(
                'operator.queue.assign_priority',
                $agentPriorityOk,
                '`assign_priority` = ' . (int)$row->assign_priority . ', minimum agent priority ' . $minAgentPriority . '.',
                'Part of the chat priority queue variant.',
                array('group' => 'queue')
            );
            if (!$agentPriorityOk) {
                $queueBlockedBy[] = 'queue_min_agent_priority';
            }

            $queueOptInOk = true;
            if ($botConfiguration['only_priority'] == 1) {
                $queueOptInOk = (int)$row->only_priority == 1;
                $checks[] = Access::checkEntry(
                    'operator.queue.only_priority',
                    $queueOptInOk,
                    '`only_priority` = ' . (int)$row->only_priority . ', department assigns priority chats to opted in operators only.',
                    'Part of the chat priority queue variant.',
                    array('group' => 'queue')
                );
                if (!$queueOptInOk) {
                    $queueBlockedBy[] = 'queue_only_priority_opt_out';
                }
            }

            $maxPriority = (int)$row->chat_max_priority;
            $rangeOk = ($maxPriority == 0 || $maxPriority >= $chatPriority)
                && ((int)$row->chat_min_priority == 0 || (int)$row->chat_min_priority <= $chatPriority);

            $checks[] = Access::checkEntry(
                'operator.queue.chat_priority_range',
                $rangeOk,
                'Chat priority ' . $chatPriority . ', accepted range ' . (int)$row->chat_min_priority . ' - ' . ($maxPriority == 0 ? 'unlimited' : $maxPriority) . '.',
                'Part of the chat priority queue variant.',
                array('group' => 'queue')
            );
            if (!$rangeOk) {
                $queueBlockedBy[] = 'queue_chat_priority_range';
            }

            $queueOk = $agentPriorityOk && $queueOptInOk && $rangeOk;
        } else {
            $checks[] = Access::checkEntry(
                'operator.queue.not_applied',
                true,
                'Chat priority ' . $chatPriority . ' is not handled by the department chat priority queue.',
                'The chat priority queue (if enabled) only matches chats whose priority is inside `min_chat_priority` - `max_chat_priority`.',
                array('affects' => false)
            );
        }

        // The default and the same language variants require an operator who did not opt into the queue.
        $defaultPathOk = (int)$row->only_priority == 0;
        $checks[] = Access::checkEntry(
            'operator.only_priority',
            $defaultPathOk,
            '`only_priority` = ' . (int)$row->only_priority . '.',
            'Operators who opted into the chat priority queue are only reachable through the queue variants - the default and same language queries exclude them.',
            array('affects' => !$queue['applies'])
        );
        if (!$defaultPathOk && !$queue['applies']) {
            $blockedBy[] = 'only_priority_opt_out';
        }

        // The already assigned operator is never picked again by the candidate queries.
        $notAssignee = (int)$row->user_id != (int)$chat->user_id;
        $checks[] = Access::checkEntry(
            'operator.not_current_assignee',
            $notAssignee,
            'Row operator #' . (int)$row->user_id . ', chat assignee #' . (int)$chat->user_id . '.',
            'The candidate queries exclude the operator the chat is currently assigned to.'
        );
        if (!$notAssignee) {
            $blockedBy[] = 'operator_is_current_assignee';
        }

        $languagePreferred = false;
        if ($department->assign_same_language == 1 && $chat->chat_locale != '') {
            try {
                $db = \ezcDbInstance::get();
                $stmt = $db->prepare('SELECT 1 FROM `lh_speech_user_language` WHERE `user_id` = :user_id AND `language` = :language LIMIT 1');
                $stmt->bindValue(':user_id', (int)$row->user_id, \PDO::PARAM_INT);
                $stmt->bindValue(':language', (string)$chat->chat_locale, \PDO::PARAM_STR);
                $stmt->execute();

                $languagePreferred = $stmt->fetchColumn() !== false;
            } catch (\Exception $e) {
                // Purely informational - the language preference must never break the diagnosis.
                $languagePreferred = false;
            }

            $checks[] = Access::checkEntry(
                'operator.language',
                $languagePreferred,
                'Chat locale ' . ($chat->chat_locale == '' ? 'not set' : '`' . $chat->chat_locale . '`') . ', operator language match: ' . ($languagePreferred ? 'yes' : 'no') . '.',
                'The department prefers same language operators, a language mismatch only loses the first round - the default query runs afterwards.',
                array('affects' => false)
            );
        }

        // A failed queue check only blocks the operator when the queue is his only way in.
        if (!$defaultPathOk && $queue['applies'] && $queueOk !== true) {
            $blockedBy = array_merge($blockedBy, $queueBlockedBy);
        }

        $baseOk = $row->ro == 0 && $online && $notExcluded && $delayOk && $capacityOk && $notAssignee;
        $pickable = $baseOk && ($defaultPathOk || $queueOk === true);

        return array(
            'pickable' => $pickable,
            'blocked_by' => $blockedBy,
            'paths' => array(
                'default_variant' => $defaultPathOk,
                'priority_variant' => $queueOk,
            ),
            'language_preferred' => $languagePreferred,
            'checks' => $checks,
        );
    }

    /**
     * Replays the three candidate queries of the workflow and reports who would be picked.
     *
     * Returns the used `variant`, the `picked_user_id`, the candidate rows in the order the workflow
     * would walk them and a summary of every variant which was tried.
     */
    public static function pick($chat, $department, $timestamp = null)
    {
        $timestamp = $timestamp === null ? (int)time() : (int)$timestamp;

        if (!is_object($department)) {
            return array(
                'variant' => null,
                'picked_user_id' => 0,
                'candidates' => array(),
                'attempts' => array(),
                'sql' => '',
                'order_by' => '',
            );
        }

        $queue = self::queueContext($chat, $department);
        $attempts = array();
        $picked = null;
        $usedAttempt = null;

        if ($queue['applies']) {
            $attempt = self::runVariant('priority', $chat, $department, $timestamp);
            $attempts[] = self::attemptSummary($attempt);

            if (!empty($attempt['rows'])) {
                $picked = $attempt['rows'][0];
                $usedAttempt = $attempt;
            }
        }

        if ($picked === null && $department->assign_same_language == 1 && $chat->chat_locale != '') {
            $attempt = self::runVariant('language', $chat, $department, $timestamp);
            $attempts[] = self::attemptSummary($attempt);

            if (!empty($attempt['rows'])) {
                $picked = $attempt['rows'][0];
                $usedAttempt = $attempt;
            }
        }

        if ($picked === null) {
            $attempt = self::runVariant('default', $chat, $department, $timestamp);
            $attempts[] = self::attemptSummary($attempt);

            if (!empty($attempt['rows'])) {
                $picked = $attempt['rows'][0];
                $usedAttempt = $attempt;
            }
        }

        $candidates = array();
        if ($usedAttempt !== null) {
            foreach ($usedAttempt['rows'] as $row) {
                $candidates[] = array(
                    'user_id' => (int)$row['user_id'],
                    'last_accepted' => (int)$row['last_accepted'],
                    'last_accepted_age' => (int)$row['last_accepted'] > 0 ? $timestamp - (int)$row['last_accepted'] : null,
                    'active_chats' => (int)$row['active_chats'],
                    'pending_chats' => (int)$row['pending_chats'],
                    'inactive_chats' => (int)$row['inactive_chats'],
                    'max_chats' => (int)$row['max_chats'],
                    'assign_priority' => (int)$row['assign_priority'],
                    'chat_min_priority' => (int)$row['chat_min_priority'],
                    'chat_max_priority' => (int)$row['chat_max_priority'],
                    'only_priority' => (int)$row['only_priority'],
                );
            }
        }

        return array(
            'variant' => $usedAttempt === null ? null : $usedAttempt['type'],
            'picked_user_id' => $picked === null ? 0 : (int)$picked['user_id'],
            'candidates' => $candidates,
            'attempts' => $attempts,
            'sql' => $usedAttempt === null ? '' : $usedAttempt['sql'],
            'order_by' => $usedAttempt === null ? '' : $usedAttempt['order_by'],
        );
    }

    /** Short summary of one candidate query attempt (used for the diagnosis output). */
    protected static function attemptSummary($attempt)
    {
        return array(
            'variant' => $attempt['type'],
            'order_by' => $attempt['order_by'],
            'description' => $attempt['description'],
            'matched' => count($attempt['rows']),
            'user_ids' => array_map(function ($row) {
                return (int)$row['user_id'];
            }, $attempt['rows']),
            'error' => $attempt['error'],
            'sql' => $attempt['sql'],
        );
    }

    /**
     * Builds and runs one candidate query. The statements are byte for byte the ones of
     * `erLhcoreClassChatWorkflow::autoAssign()` with two deliberate differences:
     * no `FOR UPDATE` (read only diagnosis) and a wider `LIMIT` so the ordering can be explained.
     */
    protected static function runVariant($type, $chat, $department, $timestamp)
    {
        $botConfiguration = self::botConfiguration($department);
        $onlineTimeout = self::onlineTimeout();

        $columns = '`lh_userdep`.`user_id`, `lh_userdep`.`last_accepted`, `lh_userdep`.`pending_chats`, `lh_userdep`.`active_chats`, '
            . '`lh_userdep`.`inactive_chats`, `lh_userdep`.`max_chats`, `lh_userdep`.`assign_priority`, `lh_userdep`.`chat_min_priority`, '
            . '`lh_userdep`.`chat_max_priority`, `lh_userdep`.`only_priority`';

        // `$appendSQL` of the core - the capacity limits and the auto assignment exclusions.
        $condition = $department->exclude_inactive_chats == 1
            ? '((pending_chats + active_chats) - inactive_chats)'
            : '(active_chats + pending_chats)';

        if ($department->max_active_chats > 0) {
            $appendSQL = " AND ((max_chats = 0 AND {$condition} < :max_active_chats) OR (max_chats > 0 AND {$condition} < max_chats))";
        } else {
            $appendSQL = " AND ((max_chats > 0 AND {$condition} < max_chats) OR (max_chats = 0))";
        }

        $appendSQL .= ' AND exclude_autoasign = 0 AND exc_indv_autoasign = 0';

        $sort = 'last_accepted ASC';
        if ($botConfiguration['auto_lower_limit'] == 1) {
            $sort = 'active_chats ASC, last_accepted ASC';
        }

        $sortGeneral = $sort;
        if ($botConfiguration['assign_by_priority'] == 1) {
            $sortGeneral = 'assign_priority DESC, ' . $sort;
        }

        $sortPriority = $sort;
        if ($botConfiguration['assign_by_priority_chat'] == 1) {
            $sortPriority = 'assign_priority DESC, ' . $sort;
        }

        $bind = array(
            ':dep_id' => array((int)$department->id, \PDO::PARAM_INT),
            ':last_activity' => array($timestamp - $onlineTimeout, \PDO::PARAM_INT),
            ':user_id' => array((int)$chat->user_id, \PDO::PARAM_INT),
            ':last_accepted' => array($timestamp - (int)$department->delay_before_assign, \PDO::PARAM_INT),
        );

        if ($department->max_active_chats > 0) {
            $bind[':max_active_chats'] = array((int)$department->max_active_chats, \PDO::PARAM_INT);
        }

        $whereBase = '`lh_userdep`.`last_accepted` < :last_accepted AND `lh_userdep`.`ro` = 0 AND `lh_userdep`.`hide_online` = 0 AND `lh_userdep`.`dep_id` = :dep_id';
        $whereOnline = '(`lh_userdep`.`last_activity` > :last_activity OR `lh_userdep`.`always_on` = 1) AND `lh_userdep`.`user_id` != :user_id';

        if ($type == 'priority') {
            $appendPriority = $appendSQL;

            if ($botConfiguration['min_agent_priority'] != 0) {
                $appendPriority .= ' AND assign_priority >= ' . (int)$botConfiguration['min_agent_priority'];
            }

            if ($botConfiguration['only_priority'] == 1) {
                $appendPriority .= ' AND `only_priority` = 1';
            }

            $appendPriority .= ' AND (chat_max_priority = 0 OR chat_max_priority >= ' . (int)$chat->priority . ')'
                . ' AND (chat_min_priority = 0 OR chat_min_priority <= ' . (int)$chat->priority . ')';

            $sql = "SELECT {$columns} FROM lh_userdep WHERE {$whereBase} AND {$whereOnline} {$appendPriority} ORDER BY {$sortPriority} LIMIT " . self::CANDIDATE_LIMIT;

            return self::executeVariant('priority', $sql, $bind, $sortPriority, 'Chat priority queue variant - chat priority ' . (int)$chat->priority . ' is handled by the department queue.');
        }

        if ($type == 'language') {
            $bind[':chatlanguage'] = array((string)$chat->chat_locale, \PDO::PARAM_STR);

            $sql = "SELECT {$columns} FROM lh_userdep INNER JOIN lh_speech_user_language ON `lh_speech_user_language`.`user_id` = `lh_userdep`.`user_id` "
                . "WHERE {$whereBase} AND `lh_userdep`.`only_priority` = 0 AND {$whereOnline} AND `lh_speech_user_language`.`language` = :chatlanguage {$appendSQL} "
                . "ORDER BY {$sort} LIMIT " . self::CANDIDATE_LIMIT;

            return self::executeVariant('language', $sql, $bind, $sort, 'Same language variant - the department prefers operators speaking `' . $chat->chat_locale . '`.');
        }

        $sql = "SELECT {$columns} FROM `lh_userdep` WHERE {$whereBase} AND `lh_userdep`.`only_priority` = 0 AND {$whereOnline} {$appendSQL} ORDER BY {$sortGeneral} LIMIT " . self::CANDIDATE_LIMIT;

        return self::executeVariant('default', $sql, $bind, $sortGeneral, 'Default variant - last accepted operator first.');
    }

    /** Runs one variant and de-duplicates the joined rows (same language can match more than once). */
    protected static function executeVariant($type, $sql, $bind, $orderBy, $description)
    {
        $rows = array();
        $error = '';

        try {
            $db = \ezcDbInstance::get();
            $stmt = $db->prepare($sql);

            foreach ($bind as $placeholder => $value) {
                $stmt->bindValue($placeholder, $value[0], $value[1]);
            }

            $stmt->execute();

            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $rawRow) {
                $rows[(int)$rawRow['user_id']] = $rawRow;
            }
        } catch (\Exception $e) {
            // A diagnosis tool must not fail the whole call because one variant cannot be executed.
            $error = $e->getMessage();
        }

        return array(
            'type' => $type,
            'sql' => $sql,
            'order_by' => $orderBy,
            'description' => $description,
            'error' => $error,
            'rows' => array_values($rows),
        );
    }

    /** Mirrors the `== '1'` comparisons of the workflow for the department checkbox settings. */
    protected static function flag($botConfiguration, $key)
    {
        return isset($botConfiguration[$key]) && $botConfiguration[$key] == '1';
    }
}
