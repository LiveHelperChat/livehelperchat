<?php

namespace LiveHelperChat\Mcp\Tools;

use LiveHelperChat\Mcp\AutoAssign;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

/**
 * Tools answering "would chat X be auto assigned?" and "why was operator X not auto assigned to
 * chat Y?".
 *
 * They replay the rules of `erLhcoreClassChatWorkflow::autoAssign()` (`lib/core/lhchat/lhchatworkflow.php`)
 * with the department settings, the chat settings and the operator (`lh_userdep`) settings, so the
 * answer covers every reason the workflow can skip an operator.
 *
 * Everything is read only - the candidate queries run without the `FOR UPDATE` lock the workflow uses.
 * Output is limited to identifiers and flags: no department names, operator names, chat nicks or
 * message bodies.
 *
 * NOTE: `composer.json` enables `classmap-authoritative`, so run `composer dump-autoload -o` after
 * adding a tool class, otherwise the discovery never sees it.
 */
class AutoAssignTools
{
    /**
     * Returns every setting which takes part in the auto assignment of a department: the department level switches (auto assignment enabled, department wide limits, assignment delay, reassignment timeout, capacity mode, same language preference, priority queue) together with a per operator view of `lh_userdep` (online state, exclusion flags, chat counters, `max_chats`, `only_priority`, assignment priority, chat priority range). Use it when a department has chats waiting, when nobody seems to receive them, or before explaining a single chat with `explain_chat_auto_assign`.
     */
    #[McpTool(name: 'get_department_auto_assign_settings', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function getDepartmentAutoAssignSettings(
        #[Schema(description: 'Department ID (lh_departament.id)')]
        int $dep_id
    ): array {
        if ($dep_id <= 0) {
            throw new ToolCallException('`dep_id` is required and has to be a positive integer.');
        }

        $department = \erLhcoreClassModelDepartament::fetch($dep_id);
        if (!($department instanceof \erLhcoreClassModelDepartament)) {
            throw new ToolCallException('Department with ID ' . $dep_id . ' was not found.');
        }

        $timestamp = time();
        $rows = AutoAssign::departmentRows($dep_id);

        $operators = array();
        foreach ($rows as $row) {
            $operators[] = $this->operatorOverview($department, $row, $timestamp);
        }

        $notes = array();

        if ($department->active_balancing != 1) {
            $notes[] = 'Auto assignment is disabled for Department #' . $dep_id . ' (`active_balancing` = 0), chats stay pending until an operator accepts them manually.';
        }

        if ($department->disabled == 1 || $department->hidden == 1) {
            $notes[] = 'Department is ' . ($department->disabled == 1 ? 'disabled' : 'hidden') . ' - existing chats keep working, but the department is not offered to visitors.';
        }

        if ($department->max_ac_dep_chats > 0 && $department->active_chats_counter >= $department->max_ac_dep_chats) {
            $notes[] = 'The department active chats limit is reached (' . (int)$department->active_chats_counter . ' of ' . (int)$department->max_ac_dep_chats . '), no operator will receive new chats.';
        }

        if (empty($rows)) {
            $notes[] = 'No `lh_userdep` row points at Department #' . $dep_id . '. Rows with `dep_id` = 0 (all departments) or -1 (all departments, read only) are NOT matched by the auto assignment queries, which filter the exact department ID.';
        }

        return array(
            'department' => AutoAssign::departmentConfig($department),
            'global' => AutoAssign::globalConfig(),
            'operators' => $operators,
            'notes' => $notes,
        );
    }

    /**
     * Answers "would chat X be auto assigned?" and, when an operator is given, "why was this operator not auto assigned to chat X?". Walks every gate of the auto assignment workflow in order: department auto assignment switch, department chat limit, chat status, reassignment timeout, the optional `auto_delay_var` postponement and the operator lock. Then it replays the candidate queries of the workflow (chat priority queue, same language, default) and reports which operator would be picked and in which order the candidates are sorted. Pass `user_id` or `email` to get a rule by rule verdict for one operator, including the case where he simply has no `lh_userdep` row for the chat department. Extensions can override the workflow through the `chat.workflow.autoassign` / `chat.workflow.autoassign_permit` events - the tool cannot see those, it is flagged in the notes.
     */
    #[McpTool(name: 'explain_chat_auto_assign', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function explainChatAutoAssign(
        #[Schema(description: 'Chat ID (lh_chat.id)')]
        int $chat_id,
        #[Schema(description: 'Optional operator user ID to explain')]
        ?int $user_id = null,
        #[Schema(description: 'Optional operator e-mail address, resolved to user IDs first (same lookup as get_user_id_by_email)')]
        ?string $email = null
    ): array {
        $chat = \erLhcoreClassModelChat::fetch($chat_id);
        if (!($chat instanceof \erLhcoreClassModelChat)) {
            throw new ToolCallException('Chat with ID ' . $chat_id . ' was not found.');
        }

        $timestamp = time();
        $department = \erLhcoreClassModelDepartament::fetch($chat->dep_id);

        $gates = AutoAssign::gates($chat, $department, $timestamp);
        $pick = AutoAssign::pick($chat, $department, $timestamp);

        $wouldBeAssigned = $gates['allowed'] && $pick['picked_user_id'] > 0;

        if (!$gates['allowed']) {
            $answer = 'Chat #' . $chat_id . ' would NOT be auto assigned: ' . $this->gateReasonText($gates['blocked_by']) . '.';
        } elseif ($pick['picked_user_id'] > 0) {
            $answer = 'Chat #' . $chat_id . ' would be auto assigned to operator #' . $pick['picked_user_id'] . ' (' . $pick['variant'] . ' variant).';
        } else {
            $answer = 'Chat #' . $chat_id . ' would NOT be auto assigned - every gate passes but no operator satisfies the department requirements (online, not excluded, `max_chats` and `max_active_chats` not reached, assignment delay passed, priority range matched).';
        }

        $result = array(
            'question' => 'Would chat #' . $chat_id . ' be auto assigned?',
            'answer' => $answer,
            'would_be_assigned' => $wouldBeAssigned,
            'chat' => AutoAssign::chatSummary($chat),
            'department' => $department instanceof \erLhcoreClassModelDepartament
                ? AutoAssign::departmentConfig($department)
                : array(
                    'id' => (int)$chat->dep_id,
                    'label' => 'Department #' . (int)$chat->dep_id,
                    'found' => false,
                ),
            'global' => AutoAssign::globalConfig(),
            'gates' => $gates,
            'pick' => $pick,
            'notes' => array(
                'The answer explains what the core workflow would do. Extensions can replace it through the `chat.workflow.autoassign` event (which can return its own `user_id`) or postpone it through `chat.workflow.autoassign_permit`.',
                'A chat which passes every gate is normally assigned by the next `cron/workflow` run or by the next chat status check, so a chat sitting pending usually fails a gate or finds no matching operator.',
            ),
        );

        $operatorIds = array();

        if ($user_id !== null && (int)$user_id > 0) {
            $operatorIds[] = (int)$user_id;
        }

        $emailLookup = null;
        if ($email !== null && trim($email) !== '') {
            $emailLookup = $this->resolveEmail(trim($email));
            foreach ($emailLookup['user_ids'] as $foundUserId) {
                if (!in_array($foundUserId, $operatorIds, true)) {
                    $operatorIds[] = $foundUserId;
                }
            }
        }

        if ($emailLookup !== null) {
            $result['email_lookup'] = $emailLookup;
        }

        if (!empty($operatorIds)) {
            $operators = array();
            foreach ($operatorIds as $operatorId) {
                $operators[] = $this->explainOperator($chat, $department, $operatorId, $timestamp);
            }

            $result['operators'] = $operators;
        }

        return $result;
    }

    /** Chat independent operator overview used by `get_department_auto_assign_settings`. */
    protected function operatorOverview($department, $row, $timestamp)
    {
        $overview = AutoAssign::rowSummary($row, $timestamp);

        $overview['online'] = AutoAssign::operatorOnline($row, $timestamp);
        $overview['counted_chats'] = AutoAssign::countedChats($department, $row);
        $overview['capacity_ok'] = AutoAssign::capacityOk($department, $row);
        $overview['delay_ok'] = AutoAssign::operatorDelayOk($department, $row, $timestamp);

        // Chat independent part of the candidate queries - the current assignee, the priority range and
        // the priority queue path can only be judged against a concrete chat.
        $overview['would_take_part'] = $row->ro == 0
            && $overview['online']
            && $row->exclude_autoasign == 0
            && $row->exc_indv_autoasign == 0
            && $overview['capacity_ok']
            && $overview['delay_ok'];

        return $overview;
    }

    /** Rule by rule verdict for one operator and one chat. */
    protected function explainOperator($chat, $department, $operatorId, $timestamp)
    {
        $user = \erLhcoreClassModelUser::fetch($operatorId);

        if (!($user instanceof \erLhcoreClassModelUser)) {
            return array(
                'user_id' => $operatorId,
                'found' => false,
                'answer' => 'Operator #' . $operatorId . ' was not found.',
            );
        }

        $rows = AutoAssign::userRows($operatorId);

        $departmentRows = array();
        $assignmentRows = array();
        $pickable = false;

        foreach ($rows as $row) {
            $departmentRows[] = AutoAssign::rowSummary($row, $timestamp);

            if ((int)$row->dep_id != (int)$chat->dep_id) {
                continue;
            }

            $checks = AutoAssign::operatorChecks($chat, $department, $row, $timestamp);

            if ($checks['pickable']) {
                $pickable = true;
            }

            $assignmentRows[] = array(
                'user_dep_id' => (int)$row->id,
                'type' => (int)$row->type,
                'dep_group_id' => (int)$row->dep_group_id,
                'pickable' => $checks['pickable'],
                'blocked_by' => $checks['blocked_by'],
                'paths' => $checks['paths'],
                'language_preferred' => $checks['language_preferred'],
                'checks' => $checks['checks'],
            );
        }

        $result = array(
            'user_id' => $operatorId,
            'found' => true,
            'disabled' => (int)$user->disabled,
            'can_login' => $user->disabled != 1,
            'hide_online' => (int)$user->hide_online,
            'always_on' => (int)$user->always_on,
            'exclude_autoasign' => (int)$user->exclude_autoasign,
            'department_rows' => $departmentRows,
            'assignment_rows_for_chat_department' => $assignmentRows,
        );

        if ($user->disabled == 1) {
            $result['answer'] = 'Operator #' . $operatorId . ' cannot be auto assigned: the account is disabled (`lh_users`.`disabled` = 1), so it cannot log in and is never online.';

            return $result;
        }

        if (empty($assignmentRows)) {
            $labels = array();
            foreach ($departmentRows as $departmentRow) {
                $labels[] = $departmentRow['dep_label'];
            }

            $result['answer'] = 'Operator #' . $operatorId . ' cannot be auto assigned to chat #' . (int)$chat->id . ': there is no `lh_userdep` row for Department #' . (int)$chat->dep_id . ' (rows of this operator: '
                . (empty($labels) ? 'none' : implode(', ', $labels)) . '). The candidate queries only match the exact department ID, a row pointing at `dep_id` 0 (all departments) is not enough.';
            $result['blocked_by'] = array('no_department_assignment');

            return $result;
        }

        $blockedBy = array();
        foreach ($assignmentRows as $assignmentRow) {
            $blockedBy = array_merge($blockedBy, $assignmentRow['blocked_by']);
        }
        $blockedBy = array_values(array_unique($blockedBy));

        if ($pickable) {
            $result['answer'] = 'Operator #' . $operatorId . ' is eligible for chat #' . (int)$chat->id . ' and takes part in the auto assignment.';
        } else {
            $result['answer'] = 'Operator #' . $operatorId . ' would NOT be auto assigned to chat #' . (int)$chat->id . ': ' . $this->gateReasonText($blockedBy) . '.';
        }

        $result['blocked_by'] = $blockedBy;

        return $result;
    }

    /** Turns machine readable reasons into a readable sentence. */
    protected function gateReasonText($blockedBy)
    {
        $texts = array(
            'department_missing' => 'the department of the chat was not found',
            'department_auto_balancing_disabled' => 'auto assignment is disabled on the department (`active_balancing` = 0)',
            'department_max_active_chats_reached' => 'the department active chats limit (`max_ac_dep_chats`) is reached',
            'chat_not_pending' => 'the chat is not pending',
            'chat_assigned_without_reassign_timeout' => 'the chat is already assigned and the reassignment timeout did not elapse',
            'auto_delay_var_missing' => 'the chat does not carry the variable required by `auto_delay_var` yet',
            'auto_delay_var_extension' => 'the `auto_delay_var` variable is resolved by an extension event which cannot be evaluated here',
            'no_recently_active_operators' => 'no operator of the department was active in the last ' . AutoAssign::LOCK_ACTIVITY_WINDOW . ' seconds, so the operator lock fails before the candidates are queried',
            'no_matching_operator' => 'no operator matches the candidate queries',
            'no_department_assignment' => 'the operator has no `lh_userdep` row for the chat department',
            'read_only_department_assignment' => 'the operator has read only access to the department',
            'operator_offline' => 'the operator is offline (`hide_online` or a stale `last_activity`)',
            'exclude_autoasign' => 'the account is excluded from auto assignment (`exclude_autoasign`)',
            'exc_indv_autoasign' => 'the operator is excluded from auto assignment in this department (`exc_indv_autoasign`)',
            'delay_before_assign' => 'the minimum delay between two assignments to this operator did not elapse',
            'operator_max_chats_reached' => 'the operator reached his chat limit (`max_chats` / department `max_active_chats`)',
            'operator_is_current_assignee' => 'the chat is already assigned to this operator',
            'only_priority_opt_out' => 'the operator opted into the chat priority queue (`only_priority` = 1) while the chat is not handled by the queue',
            'queue_min_agent_priority' => 'the operator `assign_priority` is below the minimum agent priority of the queue',
            'queue_only_priority_opt_out' => 'the department queues assign priority chats to opted in operators only',
            'queue_chat_priority_range' => 'the chat priority is outside the `chat_min_priority` / `chat_max_priority` range of the operator',
        );

        $reasons = array();
        foreach ((array)$blockedBy as $reason) {
            $reasons[] = isset($texts[$reason]) ? $texts[$reason] : ('unknown reason `' . $reason . '`');
        }

        return empty($reasons) ? 'all conditions are satisfied' : implode(', ', $reasons);
    }

    /** Same e-mail lookup `get_user_id_by_email` performs - the address is only echoed back as the lookup key. */
    protected function resolveEmail($email)
    {
        $users = \erLhcoreClassModelUser::getList(array(
            'limit' => false,
            'filter' => array('email' => $email),
            'sort' => 'id ASC',
        ));

        $userIds = array();
        foreach ($users as $user) {
            $userIds[] = (int)$user->id;
        }

        return array(
            'email' => $email,
            'found' => !empty($userIds),
            'user_ids' => $userIds,
        );
    }
}
