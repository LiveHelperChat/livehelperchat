<?php

namespace LiveHelperChat\Mcp\Tools;

use LiveHelperChat\Mcp\Access;
use LiveHelperChat\Mcp\Rules;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

/**
 * Tools answering "can this operator reach this URL / object?" and "why can he not open this chat?".
 *
 * All of these are read only: they fetch configuration and evaluate the very same conditions the
 * back office controllers apply, but never change anything.
 *
 * Output is deliberately limited to identifiers, flags and generic placeholders (`Department #12`).
 * No names, usernames, chat nicks or message content are ever returned - only the e-mail lookup tool
 * exposes an address, and only because it is the lookup key itself.
 */
class AccessTools
{
    /**
     * Pass a Live Helper Chat back office URL (e.g. https://example.com/site_admin/chat/single/123) and get back the module, view name and the permissions (module + function + explain) required to access it. Mirrors the admin "Permissions explorer" URL lookup.
     */
    #[McpTool(name: 'get_url_permissions', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function getUrlPermissions(
        #[Schema(description: 'Full URL or path, e.g. /site_admin/chat/single/123')]
        string $url
    ): array {
        $url = trim($url);

        if ($url === '') {
            throw new ToolCallException('`url` is required.');
        }

        return Access::resolveUrl($url);
    }

    /**
     * Explains why a Live Helper Chat user can or cannot view/edit/delete a concrete back office object. Combines the permissions required by the URL (e.g. `lhdepartment/manageall`) with object level rules such as department assignment, user group scope, chat ownership or read only departments. Returns every individual check with its own verdict. A disabled account (`lh_users`.`disabled` = 1) cannot log in, so it fails every check no matter what its roles say. Only identifiers and generic labels are returned.
     */
    #[McpTool(name: 'check_user_object_access', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function checkUserObjectAccess(
        #[Schema(description: 'Live Helper Chat user ID')]
        int $user_id,
        #[Schema(description: 'What the operator is trying to do: view, edit or delete. Defaults to `edit`.')]
        ?string $action = null,
        #[Schema(description: 'Back office URL which is being opened, e.g. /site_admin/department/edit/3')]
        ?string $url = null,
        #[Schema(description: 'Extra permission to check, e.g. `lhdepartment`')]
        ?string $permission_module = null,
        #[Schema(description: 'Extra permission to check, e.g. `manageall`')]
        ?string $permission_function = null,
        #[Schema(description: 'Object level rule to apply. Registered types: department, user, chat, canned_msg_replace')]
        ?string $object_type = null,
        #[Schema(description: 'Object ID, e.g. the department ID')]
        ?int $object_id = null
    ): array {
        $user = \erLhcoreClassModelUser::fetch($user_id);
        if (!($user instanceof \erLhcoreClassModelUser)) {
            throw new ToolCallException('User with ID ' . $user_id . ' was not found.');
        }

        $action = $action !== null && trim($action) !== '' ? strtolower(trim($action)) : 'edit';
        $url = $url !== null ? trim($url) : '';
        $objectType = $object_type !== null ? trim($object_type) : '';
        $objectId = $object_id !== null ? (int)$object_id : 0;

        $accessArray = \erLhcoreClassRole::accessArrayByUserID($user_id);

        $result = array(
            'user_id' => $user_id,
            'action' => $action,
            'allowed' => null,
            'notes' => array(),
        );

        // --- 1. Can the operator reach the controller at all? ------------------
        $controllerChecks = array();

        if ($url !== '') {
            $urlData = Access::resolveUrl($url);
            $result['url'] = $urlData;

            if ($urlData['resolved'] !== true) {
                $result['notes'][] = 'URL could not be resolved: ' . $urlData['message'];
            } else {
                // A disabled account is rejected at login, so even a public view stays unreachable.
                $controllerChecks[] = Access::accountCheck($user);

                if (empty($urlData['permissions'])) {
                    $result['notes'][] = 'No permission is required to open `' . $url . '`, the view is public - a valid login session is still required.';
                } else {
                    foreach ($urlData['permissions'] as $permission) {
                        $controllerChecks[] = Access::checkPermission($accessArray, $permission['module'], $permission['function'], $permission['explain']);
                    }
                }
            }
        }

        if ($permission_module !== null && $permission_function !== null) {
            if (empty($controllerChecks)) {
                $controllerChecks[] = Access::accountCheck($user);
            }
            $controllerChecks[] = Access::checkPermission($accessArray, $permission_module, $permission_function);
        }

        if (!empty($controllerChecks)) {
            $result['controller_access'] = array(
                'allowed' => Access::verdict($controllerChecks),
                'checks' => $controllerChecks,
            );
        }

        // --- 2. Object level rules --------------------------------------------
        if ($objectType !== '') {
            $rules = Rules::registry();

            if (isset($rules[$objectType])) {
                $objectChecks = call_user_func($rules[$objectType], $user_id, $objectId, $action, $accessArray, $user);

                $result['object_access'] = array(
                    'type' => $objectType,
                    'id' => $objectId,
                    'allowed' => Access::verdict($objectChecks),
                    'checks' => $objectChecks,
                );
            } else {
                $result['notes'][] = 'No rule registered for `object_type` = `' . $objectType . '`. Available types: ' . implode(', ', array_keys($rules)) . '.';
            }
        }

        // --- 3. Overall verdict -----------------------------------------------
        $verdicts = array();
        if (isset($result['controller_access'])) {
            $verdicts[] = $result['controller_access']['allowed'];
        }
        if (isset($result['object_access'])) {
            $verdicts[] = $result['object_access']['allowed'];
        }
        if (!empty($verdicts)) {
            $result['allowed'] = !in_array(false, $verdicts, true);
        } else {
            $result['notes'][] = 'Nothing to evaluate. Pass `url` and/or `object_type`.';
        }

        if ($user->disabled == 1) {
            $result['notes'][] = 'Operator account is disabled (`lh_users`.`disabled` = 1), it cannot log in, so every access check fails.';
        }

        return $result;
    }

    /**
     * Answers questions like "Why user with id X cannot open chat Y?". Fetches the chat, checks its department against the departments assigned to the operator (individual and group based, read/write vs read only) and walks the same conditions as erLhcoreClassChat::hasAccessToRead()/hasAccessToWrite(). Returns the answer, the blocking reasons and the full check trace. The chat is described by identifiers only - the visitor nick and the department name are never returned.
     */
    #[McpTool(name: 'explain_chat_access', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function explainChatAccess(
        #[Schema(description: 'Live Helper Chat user ID of the operator')]
        int $user_id,
        #[Schema(description: 'Chat ID (lh_chat.id)')]
        int $chat_id,
        #[Schema(description: '`view` to only open the chat, `edit` to also reply in it. Defaults to `view`.')]
        ?string $action = null
    ): array {
        $user = \erLhcoreClassModelUser::fetch($user_id);
        if (!($user instanceof \erLhcoreClassModelUser)) {
            throw new ToolCallException('User with ID ' . $user_id . ' was not found.');
        }

        $chat = \erLhcoreClassModelChat::fetch($chat_id);
        if (!($chat instanceof \erLhcoreClassModelChat)) {
            throw new ToolCallException('Chat with ID ' . $chat_id . ' was not found.');
        }

        $action = $action !== null && trim($action) !== '' ? strtolower(trim($action)) : 'view';
        $accessArray = \erLhcoreClassRole::accessArrayByUserID($user_id);

        $access = Access::chatAccess($user_id, $chat, $action, $accessArray, $user);
        $departments = Access::userDepartments($user_id, $user);

        $department = \erLhcoreClassModelDepartament::fetch($chat->dep_id);

        $result = array(
            'question' => 'Why user #' . $user_id . ' cannot ' . ($action == 'edit' ? 'reply in' : 'open') . ' chat #' . $chat_id . '?',
            'answer' => $access['allowed']
                ? 'User CAN access this chat.'
                : 'User CANNOT access this chat: ' . implode(' ', $access['reasons']),
            'allowed' => $access['allowed'],
            'action' => $action,
            'blocked_by' => $access['blocked_by'],
            'reasons' => $access['reasons'],
            'checks' => $access['checks'],
            'user' => array(
                'id' => $user_id,
                'disabled' => $user->disabled == 1,
                'hide_online' => $user->hide_online == 1,
                'departments' => $departments,
            ),
            'chat' => array(
                'id' => (int)$chat->id,
                'status' => (int)$chat->status,
                'status_sub' => (int)$chat->status_sub,
                'dep_id' => (int)$chat->dep_id,
                'assignee_user_id' => (int)$chat->user_id,
                'ctime' => (int)$chat->time,
            ),
            'department' => $department instanceof \erLhcoreClassModelDepartament ? array(
                'id' => (int)$department->id,
                'label' => 'Department #' . (int)$department->id,
                'disabled' => $department->disabled == 1,
                'hidden' => $department->hidden == 1,
                'archive' => $department->archive == 1,
                'dep_offline' => $department->dep_offline == 1,
            ) : array(
                'id' => (int)$chat->dep_id,
                'label' => 'Department #' . (int)$chat->dep_id,
                'found' => false,
            ),
            'notes' => array(),
        );

        if ($user->disabled == 1) {
            $result['notes'][] = 'Operator account is disabled (`lh_users`.`disabled` = 1), it cannot log in, so every access check fails.';
        }

        if ($department instanceof \erLhcoreClassModelDepartament) {
            if ($department->disabled == 1 || $department->hidden == 1) {
                $result['notes'][] = 'Department is ' . ($department->disabled == 1 ? 'disabled' : 'hidden') . ', it is excluded from the widget and assignment, but existing chats can still be opened by operators assigned to it.';
            }
            if ($department->dep_offline == 1) {
                $result['notes'][] = 'Department is marked as offline.';
            }
        } else {
            $result['notes'][] = 'Chat department #' . $chat->dep_id . ' was not found.';
        }

        if (!$departments['all_departments'] && !empty($departments['read_only_department_ids'])) {
            $result['notes'][] = 'Operator has read only access to departments [' . implode(', ', $departments['read_only_department_ids']) . '].';
        }

        return $result;
    }
}
