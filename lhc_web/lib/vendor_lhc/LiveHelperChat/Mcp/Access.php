<?php

namespace LiveHelperChat\Mcp;

/**
 * Read only permission evaluation helpers shared by the MCP tools.
 *
 * This is the logic that used to live inline in `modules/lhaimcp/mcp.php`, moved into a class so
 * the tool classes stay thin and the rules live in one place.
 *
 * Live Helper Chat core classes are referenced from the global namespace on purpose (leading
 * backslash) - this file is namespaced, the core is not.
 */
class Access
{
    /**
     * Resolves a Live Helper Chat URL into module/view and the permissions required to access it.
     * Same logic as `lhpermission/explorer.php` (action = 2), just without any HTML output.
     */
    public static function resolveUrl($url)
    {
        $sysConfiguration = \erLhcoreClassSystem::instance();

        // --- Normalize the given URL into a Live Helper Chat RequestURI -------------
        $path = parse_url($url, PHP_URL_PATH);
        if ($path === false || $path === null || $path === '') {
            $path = $url; // A plain path was passed.
        }

        $path = '/' . ltrim(preg_replace('~/{2,}~', '/', $path), '/');

        // RequestURI never contains the installation folder nor index.php.
        $basePath = trim((string)$sysConfiguration->WWWDir, '/');
        if ($basePath != '' && stripos($path, '/' . $basePath . '/') === 0) {
            $path = substr($path, strlen($basePath));
        }
        if (stripos($path, '/index.php/') === 0) {
            $path = substr($path, strlen('/index.php'));
        }

        $segments = array_values(array_filter(explode('/', $path), 'strlen'));

        // URL has to start with a siteaccess, otherwise module/function would be shifted.
        $siteAccesses = (array)\erConfigClassLhConfig::getInstance()->getSetting('site', 'available_site_access', array());
        foreach ((array)\erConfigClassLhConfig::getInstance()->getSetting('site', 'default_admin_site_access', array()) as $siteAccessItem) {
            $siteAccesses[] = $siteAccessItem;
        }
        $siteAccesses[] = $sysConfiguration->SiteAccess;
        $siteAccesses = array_unique($siteAccesses);

        if (empty($segments) || !in_array($segments[0], $siteAccesses, true)) {
            array_unshift($segments, $sysConfiguration->SiteAccess);
        }

        // --- Resolve module/function the exact same way the routing does -------------
        $requestURIOriginal = $sysConfiguration->RequestURI;

        $sysConfiguration->RequestURI = '/' . implode('/', $segments);
        \erLhcoreClassURL::resetInstance();
        $urlInstance = \erLhcoreClassURL::getInstance();

        $currentModuleName = preg_replace('/[^a-zA-Z0-9\-_]/', '', (string)$urlInstance->getParam('module'));
        $currentView = preg_replace('/[^a-zA-Z0-9\-_]/', '', (string)$urlInstance->getParam('function'));

        // Back to the real request, the instance is recreated lazily if anything asks for it.
        $sysConfiguration->RequestURI = $requestURIOriginal;
        \erLhcoreClassURL::resetInstance();

        if ($currentModuleName == '' || $currentView == '') {
            return array(
                'resolved' => false,
                'url' => $url,
                'message' => 'URL does not contain both module and view segments.',
            );
        }

        $permissionModule = 'lh' . $currentModuleName;
        $moduleName = \erLhcoreClassModules::getModuleName($permissionModule);

        if ($moduleName === '') {
            return array(
                'resolved' => false,
                'url' => $url,
                'module' => $permissionModule,
                'view' => $currentView,
                'message' => 'Module `' . $permissionModule . '` was not found.',
            );
        }

        // Permissions which grant access to this URL.
        $moduleFunctions = \erLhcoreClassModules::getModuleFunctions($permissionModule, array('extract_url' => true));

        $requiredPermissions = array();

        foreach ($moduleFunctions as $permission => $moduleFunction) {
            if (isset($moduleFunction['url']) && in_array($permissionModule . '/' . $currentView, $moduleFunction['url'])) {
                $requiredPermissions[] = array(
                    'permission' => $permission,
                    'explain' => $moduleFunction['explain'],
                );
            }
        }

        return array(
            'resolved' => true,
            'url' => $url,
            'module' => $permissionModule,
            'name' => $moduleName,
            'view' => $currentView,
            'public' => empty($requiredPermissions),
            'permissions' => $requiredPermissions,
        );
    }

    /**
     * Single check entry used by `checkUserObjectAccess`.
     *
     * Checks sharing the same `group` are OR-ed (only one of them has to pass), different groups are AND-ed.
     * `affects => false` marks a purely informational entry which does not influence the verdict.
     */
    public static function checkEntry($requirement, $allowed, $details, $explain = '', $options = array())
    {
        $entry = array(
            'requirement' => $requirement,
            'allowed' => (bool)$allowed,
            'details' => $details,
            'explain' => $explain,
        );

        if (isset($options['granted'])) {
            $entry['granted'] = (bool)$options['granted'];
        }
        if (isset($options['group'])) {
            $entry['group'] = $options['group'];
        }
        if (isset($options['affects'])) {
            $entry['affects'] = (bool)$options['affects'];
        }

        return $entry;
    }

    /** Renders the limitation payload (`true` or a JSON string) of a role entry. */
    public static function limitationDetails($limitation)
    {
        if ($limitation === true || $limitation === null || $limitation === '') {
            return '';
        }

        if (is_string($limitation)) {
            $decoded = json_decode($limitation, true);
            return ' Limitation: ' . ($decoded === null ? $limitation : json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return ' Limitation: ' . json_encode($limitation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Evaluates one `module`/`function` requirement against a raw access array
     * (`erLhcoreClassRole::accessArrayByUserID()`). Mirrors `erLhcoreClassRole::canUseByModuleAndFunction()`
     * plus the limitation handling of `erLhcoreClassUser::hasAccessTo()`.
     */
    public static function checkPermission($accessArray, $module, $function, $explain = '', $options = array())
    {
        $requirement = $module . '/' . $function;

        if (isset($accessArray['ex_perm'][$module][$function])) {
            return self::checkEntry($requirement, false, 'Explicitly denied by an `ex_perm` role entry.', $explain, $options + array('granted' => false));
        }

        if (isset($accessArray[$module][$function])) {
            return self::checkEntry($requirement, true, 'Granted directly.' . self::limitationDetails($accessArray[$module][$function]), $explain, $options + array('granted' => true));
        }

        if (isset($accessArray['*']['*'])) {
            return self::checkEntry($requirement, true, 'Granted by the global `*`/`*` role entry.' . self::limitationDetails($accessArray['*']['*']), $explain, $options + array('granted' => true));
        }

        if (isset($accessArray[$module]['*'])) {
            return self::checkEntry($requirement, true, 'Granted by the `' . $module . '`/`*` role entry.' . self::limitationDetails($accessArray[$module]['*']), $explain, $options + array('granted' => true));
        }

        return self::checkEntry($requirement, false, 'Permission is not granted by any of the user groups.', $explain, $options + array('granted' => false));
    }

    /**
     * Account level gate which has to be part of every verdict.
     *
     * `modules/lhuser/login.php` refuses to authenticate a disabled account, so none of its roles,
     * permissions or department assignments can ever be exercised. It is checked separately because
     * the role array of a disabled user is still fully populated - evaluating only the roles is what
     * made the tools answer "yes, he can edit that" for accounts which cannot even log in.
     */
    public static function accountCheck($user)
    {
        $disabled = isset($user->disabled) && $user->disabled == 1;

        return self::checkEntry(
            'user.enabled',
            !$disabled,
            $disabled
                ? 'Account is disabled (`lh_users`.`disabled` = 1).'
                : 'Account is not disabled.',
            'A disabled account cannot log in, therefore it has no permissions or department access at all.',
            array('group' => 'account')
        );
    }

    /** AND between groups, OR inside a group. Returns null when nothing affects the verdict. */
    public static function verdict($checks)
    {
        $groups = array();

        foreach ($checks as $check) {
            if (isset($check['affects']) && $check['affects'] === false) {
                continue;
            }
            $groups[isset($check['group']) ? $check['group'] : $check['requirement']][] = $check['allowed'];
        }

        if (empty($groups)) {
            return null;
        }

        foreach ($groups as $groupResults) {
            if (!in_array(true, $groupResults, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Which departments the operator can access and how.
     *
     * Department assignment is materialized into `lh_userdep`:
     *  - `type = 0` rows are individual assignments (`dep_group_id` = 0)
     *  - `type = 1` rows are expanded from `lh_departament_group_user` + `lh_departament_group_member`
     *    (one row per member department of the group)
     * `ro = 1` marks a read only department, so the operator can read chats but not reply.
     */
    public static function userDepartments($userId, $user)
    {
        $rows = \erLhcoreClassModelUserDep::getList(array(
            'limit' => false,
            'filter' => array('user_id' => $userId),
            'sort' => 'type ASC, id ASC',
        ));

        $individual = array();
        $groups = array();
        $allIds = array();
        $readOnlyIds = array();

        foreach ($rows as $row) {
            $depId = (int)$row->dep_id;
            $allIds[] = $depId;

            if ($row->ro == 1) {
                $readOnlyIds[] = $depId;
            }

            if ($row->type == 0) {
                $individual[] = array(
                    'dep_id' => $depId,
                    'label' => 'Department #' . $depId,
                    'read_only' => $row->ro == 1,
                    'exclude_autoasign' => $row->exclude_autoasign == 1,
                    'exclude_autoasign_mails' => $row->exclude_autoasign_mails == 1,
                    'max_chats' => (int)$row->max_chats,
                    'max_mails' => (int)$row->max_mails,
                );
            } else {
                $groupId = (int)$row->dep_group_id;
                if (!isset($groups[$groupId])) {
                    $groups[$groupId] = array(
                        'dep_group_id' => $groupId,
                        'label' => 'Department group #' . $groupId,
                        'read_only' => $row->ro == 1,
                        'departments' => array(),
                    );
                }
                $groups[$groupId]['departments'][] = $depId;
            }
        }

        $groups = array_values($groups);

        // Names are deliberately not exposed, only `Department #N` placeholders.
        return array(
            'all_departments' => $user->all_departments != 0,
            'department_ids' => array_values(array_unique($allIds)),
            'write_department_ids' => array_values(array_diff(array_unique($allIds), $readOnlyIds)),
            'read_only_department_ids' => array_values(array_unique($readOnlyIds)),
            'individual' => $individual,
            'from_groups' => $groups,
        );
    }

    /**
     * Faithful port of `erLhcoreClassChat::hasAccessToRead()` / `hasAccessToWrite()` for an arbitrary
     * user ID (those helpers always use the currently logged in operator).
     *
     * Returns array('allowed' => bool, 'reasons' => array, 'blocked_by' => array, 'checks' => array).
     * The `checks` are informational - the verdict is `allowed`.
     */
    public static function chatAccess($userId, $chat, $action, $accessArray, $user)
    {
        $isClosed = $chat->status == \erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
        $isOperatorChat = $chat->status == \erLhcoreClassModelChat::STATUS_OPERATORS_CHAT;
        $isOwner = $chat->user_id == $userId;
        $allDepartments = $user->all_departments != 0;

        $userDepartments = $allDepartments ? array() : array_map('intval', \erLhcoreClassUserDep::getUserDepartaments($userId, $user->cache_version));
        $readOnlyDepartments = array_map('intval', \erLhcoreClassUserDep::getUserReadDepartments($userId, $user->cache_version));
        $inDepartment = $allDepartments || in_array((int)$chat->dep_id, $userDepartments, true);

        $allowRemote = self::checkPermission($accessArray, 'lhchat', 'allowopenremotechat', 'Allow operator to open other operators chats from same department', array('affects' => false));
        $allowClosed = self::checkPermission($accessArray, 'lhchat', 'allowopenclosedchats', 'Allow operator to open closed chats', array('affects' => false));

        $reasons = array();
        $blockedBy = array();
        $allowed = false;

        if (!$allDepartments && $chat->dep_id != 0) {
            if ($isClosed && !$allowClosed['allowed']) {
                $blockedBy[] = 'closed_chat_without_allowopenclosedchats';
                $reasons[] = 'Chat is closed and the operator has no `lhchat`/`allowopenclosedchats` permission.';
            } elseif ($isOwner) {
                $allowed = true;
            } elseif (empty($userDepartments)) {
                $blockedBy[] = 'no_departments_assigned';
                $reasons[] = 'Operator is not assigned to any department.';
            } elseif ($inDepartment) {
                if ($allowRemote['allowed'] || $isOperatorChat) {
                    $allowed = true;
                } elseif ($chat->user_id == 0 || $isOwner) {
                    $allowed = true;
                } else {
                    $blockedBy[] = 'other_operator_chat';
                    $reasons[] = 'Chat is assigned to another operator #' . $chat->user_id . ' and the operator has no `lhchat`/`allowopenremotechat` permission.';
                }
            } else {
                $blockedBy[] = 'department_not_assigned';
                $reasons[] = 'Chat department #' . $chat->dep_id . ' is not among the operator departments [' . implode(', ', $userDepartments) . '].';
            }
        } else {
            if ($allDepartments && $chat->user_id != 0 && !$isOwner && !$allowRemote['allowed']) {
                $blockedBy[] = 'other_operator_chat';
                $reasons[] = 'Chat is assigned to another operator #' . $chat->user_id . ' and the operator has no `lhchat`/`allowopenremotechat` permission.';
            } elseif ($isClosed && !$allowClosed['allowed']) {
                $blockedBy[] = 'closed_chat_without_allowopenclosedchats';
                $reasons[] = 'Chat is closed and the operator has no `lhchat`/`allowopenclosedchats` permission.';
            } else {
                $allowed = true;
            }
        }

        // `erLhcoreClassChat::hasAccessToWrite()` - own chats or departments which are not read only.
        if ($action == 'edit') {
            if ($isOwner || !in_array((int)$chat->dep_id, $readOnlyDepartments, true)) {
                if (!$allowed) {
                    $reasons[] = 'Operator can reply in this department, but read access is denied.';
                }
            } else {
                $blockedBy[] = 'read_only_department';
                $reasons[] = 'Operator has read only access to department #' . $chat->dep_id . ' and cannot reply.';
                $allowed = false;
            }
        }

        // The account gate wins over everything above - a disabled account is rejected at login.
        $accountCheck = self::accountCheck($user);

        if ($accountCheck['allowed'] === false) {
            $allowed = false;
            array_unshift($reasons, 'Operator account is disabled (`lh_users`.`disabled` = 1).');
            array_unshift($blockedBy, 'user_disabled');
        }

        $checks = array(
            $accountCheck,
            self::checkEntry(
                'user.all_departments',
                $allDepartments,
                $allDepartments ? 'Operator has access to all departments (`lh_users`.`all_departments` = 1).' : 'Operator is limited to the assigned departments.',
                '',
                array('affects' => false)
            ),
            self::checkEntry(
                'user.departments',
                $inDepartment,
                'Operator departments: [' . implode(', ', $userDepartments) . '], read only: [' . implode(', ', $readOnlyDepartments) . '], chat department: #' . $chat->dep_id . '.',
                '',
                array('affects' => false)
            ),
            self::checkEntry(
                'object.assignee',
                $isOwner,
                $isOwner ? 'Chat is assigned to this operator.' : 'Chat is assigned to operator #' . $chat->user_id . '.',
                '',
                array('affects' => false)
            ),
            self::checkEntry(
                'object.status',
                !$isClosed,
                'Chat status: ' . $chat->status . ($isClosed ? ' (closed)' : '') . '.',
                '',
                array('affects' => false)
            ),
            $allowClosed,
            $allowRemote,
        );

        return array(
            'allowed' => $allowed,
            'reasons' => $reasons,
            'blocked_by' => $blockedBy,
            'checks' => $checks,
        );
    }
}
