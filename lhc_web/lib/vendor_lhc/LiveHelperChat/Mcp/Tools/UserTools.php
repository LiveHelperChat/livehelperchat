<?php

namespace LiveHelperChat\Mcp\Tools;

use LiveHelperChat\Mcp\Access;
use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

/**
 * Tools describing Live Helper Chat operator accounts.
 *
 * Tool names are pinned explicitly to the names the endpoint exposed before the SDK migration, so
 * existing prompts and connectors keep working. Parameter schemas are generated from the method
 * signatures, and the docblock becomes the tool description.
 */
class UserTools
{
    /**
     * Finds Live Helper Chat operator accounts by their exact email address and returns all matching user IDs.
     */
    #[McpTool(name: 'get_user_id_by_email', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function getUserIdByEmail(
        #[Schema(description: 'Operator email address to look up')]
        string $email
    ): array {
        $email = trim($email);

        if ($email === '') {
            throw new ToolCallException('`email` is required.');
        }

        $users = \erLhcoreClassModelUser::getList(array(
            'limit' => false,
            'filter' => array('email' => $email),
            'sort' => 'id ASC',
        ));

        $matches = array();
        foreach ($users as $user) {
            $matches[] = array(
                'user_id' => (int)$user->id,
                'email' => $user->email,
            );
        }

        return array(
            'email' => $email,
            'found' => !empty($matches),
            'user_ids' => array_column($matches, 'user_id'),
            'users' => $matches,
        );
    }

    /**
     * Finds Live Helper Chat operator accounts by their username and returns all matching user IDs. The username is matched exactly the same way the back office login form matches it, so a case insensitive database collation resolves `Admin` and `admin` to the same account. Usernames are never echoed back - only numeric user IDs are returned.
     */
    #[McpTool(name: 'get_user_id_by_username', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function getUserIdByUsername(
        #[Schema(description: 'Operator username to look up')]
        string $username
    ): array {
        $username = trim($username);

        if ($username === '') {
            throw new ToolCallException('`username` is required.');
        }

        $users = \erLhcoreClassModelUser::getList(array(
            'limit' => false,
            'filter' => array('username' => $username),
            'sort' => 'id ASC',
        ));

        $userIds = array();
        foreach ($users as $user) {
            $userIds[] = (int)$user->id;
        }

        return array(
            'found' => !empty($userIds),
            'user_ids' => $userIds,
        );
    }

    /**
     * Returns module, function and limitation entries assigned to a Live Helper Chat operator by user ID (lh_users.id) together with the departments the operator can access in read/write mode and read only mode, plus the departments inherited from department groups. Departments are reported as numeric IDs only. A disabled account cannot log in, so its permissions are listed but they cannot be exercised - check `can_login` first.
     */
    #[McpTool(name: 'get_user_permissions', annotations: new ToolAnnotations(readOnlyHint: true))]
    public function getUserPermissions(
        #[Schema(description: 'Live Helper Chat user ID (lh_users.id)')]
        int $user_id
    ): array {
        if ($user_id <= 0) {
            throw new ToolCallException('`user_id` is required and has to be a positive integer.');
        }

        $user = \erLhcoreClassModelUser::fetch($user_id);
        if (!($user instanceof \erLhcoreClassModelUser)) {
            throw new ToolCallException('User with ID ' . $user_id . ' was not found.');
        }

        $accessArray = \erLhcoreClassRole::accessArrayByUserID($user_id);

        $permissions = array();

        foreach ($accessArray as $module => $functions) {
            if ($module == 'ex_perm' || !is_array($functions)) {
                continue;
            }
            foreach ($functions as $function => $limitation) {
                $permissions[] = array(
                    'module' => $module,
                    'function' => $function,
                    'limitation' => $limitation === true ? null : $limitation,
                );
            }
        }

        return array(
            'user_id' => $user_id,
            'disabled' => (int)$user->disabled,
            // A disabled account is refused at login, so the permissions below can never be used.
            'can_login' => $user->disabled != 1,
            'all_departments' => (int)$user->all_departments,
            'is_admin' => isset($accessArray['*']['*']),
            'permissions' => $permissions,
            'departments' => Access::userDepartments($user_id, $user),
        );
    }
}
