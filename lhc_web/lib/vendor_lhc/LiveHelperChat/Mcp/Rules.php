<?php

namespace LiveHelperChat\Mcp;

/**
 * Object level access rules used by the `check_user_object_access` MCP tool.
 *
 * Every rule receives ($userId, $objectId, $action, $accessArray, $user) and returns an array of
 * check entries built with `Access::checkEntry()`.
 *
 * Rules must never put internal details into their output - no titles, user names, chat nicks,
 * department names, message bodies or e-mail addresses. Only numeric identifiers, booleans and
 * generic placeholders (`Department #12`) belong into `details`/`explain`, they end up in the output
 * of the `check_user_object_access` tool verbatim.
 *
 * Extensions can register their own types with the `ai_mcp.object_access_rules` event:
 *
 *     $dispatcher->listen('ai_mcp.object_access_rules', function($params) {
 *         $params['rules']['my_object'] = array(MyRules::class, 'myObject');
 *     });
 */
class Rules
{
    /** Registry of the object types the MCP tool knows how to diagnose. */
    public static function registry()
    {
        $rules = array(
            'department' => array(self::class, 'department'),
            'user' => array(self::class, 'user'),
            'chat' => array(self::class, 'chat'),
            'canned_msg_replace' => array(self::class, 'cannedMsgReplace'),
        );

        \erLhcoreClassChatEventDispatcher::getInstance()->dispatch('ai_mcp.object_access_rules', array('rules' => & $rules));

        return $rules;
    }

    /**
     * Mirrors `modules/lhdepartment/edit.php` - `lhdepartment`/`manageall` or the department has to be assigned to the user.
     */
    public static function department($userId, $objectId, $action, $accessArray, $user)
    {
        $department = \erLhcoreClassModelDepartament::fetch($objectId);

        if (!($department instanceof \erLhcoreClassModelDepartament)) {
            return array(Access::checkEntry('object.exists', false, 'Department #' . $objectId . ' was not found.'));
        }

        $checks = array(Access::accountCheck($user));

        $manageAll = Access::checkPermission($accessArray, 'lhdepartment', 'manageall', 'Allow user to manage all departments, not only assigned to him', array('group' => 'access'));
        $checks[] = $manageAll;

        if (!$manageAll['allowed']) {
            $userDepartments = \erLhcoreClassUserDep::parseUserDepartmetnsForFilter($userId, $user->cache_version);

            $checks[] = Access::checkEntry(
                'scope.assigned_department',
                $userDepartments === true || in_array($objectId, (array)$userDepartments, true),
                $userDepartments === true
                    ? 'User is assigned to all departments.'
                    : 'User departments: [' . implode(', ', (array)$userDepartments) . '], department #' . $objectId . '.',
                'Without `lhdepartment`/`manageall` an operator can only work with departments assigned to him.',
                array('group' => 'access')
            );
        }

        if ($action == 'delete') {
            $checks[] = Access::checkPermission($accessArray, 'lhdepartment', 'delete', 'Allow to delete departments', array('group' => 'delete'));
            $checks[] = Access::checkEntry(
                'object.can_delete',
                $department->can_delete == true,
                $department->can_delete == true ? 'Department can be deleted.' : 'Department cannot be deleted, it still has chats or it is the default department.',
                'Department #1 is never deletable.',
                array('group' => 'delete')
            );
        }

        return $checks;
    }

    /**
     * Mirrors `modules/lhuser/edit.php` - `lhuser`/`editusergroupall` or the target account has to be inside the operated groups.
     */
    public static function user($userId, $objectId, $action, $accessArray, $user)
    {
        $target = \erLhcoreClassModelUser::fetch($objectId);

        if (!($target instanceof \erLhcoreClassModelUser)) {
            return array(Access::checkEntry('object.exists', false, 'User #' . $objectId . ' was not found.'));
        }

        $checks = array(Access::accountCheck($user));
        $targetGroups = array_values((array)$target->user_groups_id);

        $checks[] = Access::checkEntry(
            'object.self',
            $userId == $objectId,
            $userId == $objectId
                ? 'Operator is editing his own account.'
                : 'Operator #' . $userId . ' is editing account #' . $objectId . '.',
            'Own account edit is granted by `lhuser`/`selfedit`.',
            array('affects' => false)
        );

        $canEditGroups = \erLhcoreClassGroupRole::canEditUserGroups($user, $target);

        if ($canEditGroups === true) {
            $checks[] = Access::checkEntry(
                'scope.user_groups',
                true,
                'Operator can work with all groups of this account (`lhuser`/`editusergroupall` or all groups are accessible).',
                'Allow user to edit other user groups even if they are not a member of it.',
                array('group' => 'groups')
            );
        } else {
            $groups = \erLhcoreClassGroupRole::getGroupsAccessedByUser($user);
            $groupsRead = array_merge($groups['groups'], \erLhcoreClassGroupRole::getGroupsAccessedByUser($target)['read']);

            $checks[] = Access::checkPermission($accessArray, 'lhuser', 'editusergroupall', 'Allow user to edit other user groups even if they are not a member of it.', array('group' => 'groups'));
            $checks[] = Access::checkEntry(
                'scope.user_groups.write',
                empty(array_diff($targetGroups, $groups['groups'])),
                'Operator can write groups [' . implode(', ', $groups['groups']) . '], account groups [' . implode(', ', $targetGroups) . '].',
                'Without `editusergroupall` the account groups have to be inside the groups the operator can work with.',
                array('group' => 'groups')
            );
            $checks[] = Access::checkEntry(
                'scope.user_groups.read',
                empty(array_diff($targetGroups, $groupsRead)),
                'Groups with read access: [' . implode(', ', $groupsRead) . '].',
                'Read only access to the account is enough to view it, but not to save it.',
                array('affects' => $action == 'view', 'group' => 'groups_read')
            );
        }

        if ($action == 'delete') {
            $checks[] = Access::checkPermission($accessArray, 'lhuser', 'deleteuser', 'Allow user to delete another user', array('group' => 'delete'));
        }

        return $checks;
    }

    /** Mirrors `erLhcoreClassChat::hasAccessToRead()`/`hasAccessToWrite()` for an arbitrary user. */
    public static function chat($userId, $objectId, $action, $accessArray, $user)
    {
        $chat = \erLhcoreClassModelChat::fetch($objectId);

        if (!($chat instanceof \erLhcoreClassModelChat)) {
            return array(Access::checkEntry('object.exists', false, 'Chat #' . $objectId . ' was not found.'));
        }

        $access = Access::chatAccess($userId, $chat, $action, $accessArray, $user);

        $checks = $access['checks'];
        $checks[] = Access::checkEntry(
            'chat.' . ($action == 'edit' ? 'write' : 'read') . '_access',
            $access['allowed'],
            empty($access['reasons']) ? 'All chat access conditions are satisfied.' : implode(' ', $access['reasons']),
            'Chat department has to be assigned to the operator (or the operator has to have access to all departments) and read only departments cannot be replied in.'
        );

        return $checks;
    }

    /**
     * Mirrors `modules/lhcannedmsg/editreplace.php` - sensitive replaceable variables require an extra permission.
     */
    public static function cannedMsgReplace($userId, $objectId, $action, $accessArray, $user)
    {
        $item = \erLhcoreClassModelCannedMsgReplace::fetch($objectId);

        if (!($item instanceof \erLhcoreClassModelCannedMsgReplace)) {
            return array(Access::checkEntry('object.exists', false, 'Replaceable variable #' . $objectId . ' was not found.'));
        }

        $restricted = !empty($item->configuration_array['restricted_access']);

        if (!$restricted) {
            return array(
                Access::accountCheck($user),
                Access::checkEntry(
                    'object.restricted_access',
                    true,
                    'The variable is not marked as restricted, no extra permission is required.',
                    'Restricted variables may contain sensitive data and are limited to `lhcannedmsg`/`use_replace_sensitive`.',
                    array('affects' => false)
                )
            );
        }

        return array(
            Access::accountCheck($user),
            Access::checkEntry('object.restricted_access', true, 'The variable is marked as restricted.', '', array('affects' => false)),
            Access::checkPermission($accessArray, 'lhcannedmsg', 'use_replace_sensitive', 'Allow operator manage sensitive replaceable variables'),
        );
    }
}
