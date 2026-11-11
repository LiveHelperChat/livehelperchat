<?php

class erLhcoreClassUserUtils
{
    /**
     * Validates redirect path taken from user input (e.g. login "r" parameter).
     * Ensures the value is a relative path inside the application and can not be used
     * as an open redirect (absolute URL, protocol relative URL, backslashes, path traversal).
     *
     * @param mixed $path
     * @return bool
     */
    public static function isValidRedirectPath($path)
    {
        if (!is_string($path) || $path === '') {
            return false;
        }

        // Browsers treat %2e%2e%2f as "../", decode a few times before validating
        for ($i = 0; $i < 3; $i++) {
            $decoded = rawurldecode($path);
            if ($decoded === $path) {
                break;
            }
            $path = $decoded;
        }

        // Control characters, whitespace and backslashes never belong to a valid internal path
        if (preg_match('/[\x00-\x20\x7F\\\\]/', $path)) {
            return false;
        }

        // Absolute URL (http://, javascript:, data:, ...) or protocol relative URL (//host)
        if (preg_match('#^[^/?\#]*:#', $path) || strpos($path, '//') === 0) {
            return false;
        }

        // Path traversal above the base URL
        $depth = 0;
        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                if (--$depth < 0) {
                    return false;
                }
            } else {
                $depth++;
            }
        }

        return true;
    }

    /**
     * Site accesses from which an operator (back office) login is allowed.
     * Same rules as user/login applies.
     *
     * @return array
     */
    public static function getLoginSiteAccess()
    {
        $adminSiteAccess = erConfigClassLhConfig::getInstance()->getSetting('site', 'default_admin_site_access', false);

        $possibleLoginSiteAccess = is_array($adminSiteAccess) ? $adminSiteAccess : array('site_admin');

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_site_access', array('loginSiteAccess' => & $possibleLoginSiteAccess));

        return $possibleLoginSiteAccess;
    }

    /**
     * Endpoints which log an operator in without a permission check (hash/token based links)
     * have to be served from a back office site access only, just like user/login does.
     *
     * @return bool
     */
    public static function validateLoginSiteAccess()
    {
        return in_array(erLhcoreClassSystem::instance()->SiteAccess, self::getLoginSiteAccess(), true);
    }

    /**
     * Checks whether the current IP is allowed to log this user in.
     * Same rules as user/login and user/autologin apply.
     *
     * @param erLhcoreClassModelUser $user
     * @return bool
     */
    public static function isLoginIpAllowed($user)
    {
        $passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

        if (!isset($passwordData['allow_login_from_ip']) || $passwordData['allow_login_from_ip'] == '') {
            return true;
        }

        $bypassIpUserIds = (isset($passwordData['bypass_ip_user_id']) && $passwordData['bypass_ip_user_id'] != '') ? explode(',', str_replace(' ', '', $passwordData['bypass_ip_user_id'])) : array();

        return in_array((string)$user->id, $bypassIpUserIds) || erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(), explode(',', $passwordData['allow_login_from_ip']));
    }

    /**
     * Account is restricted to REST API usage while it has an active API key.
     *
     * @param erLhcoreClassModelUser $user
     * @return bool
     */
    public static function isApiKeyLoginOnly($user)
    {
        return $user->api_key_login_only == 1;
    }

    /**
     * Direct username/password login is not allowed for API key only accounts.
     *
     * @param erLhcoreClassModelUser $user
     * @return bool
     */
    public static function isPasswordLoginAllowed($user)
    {
        return !self::isApiKeyLoginOnly($user);
    }

    /**
     * Login without credentials (autologin, login as user, login by token, chat accept link)
     * is not allowed for this user. Used by fetchAutologinUser and erLhcoreClassUser::setLoggedUser
     *
     * @param erLhcoreClassModelUser $user
     * @return bool
     */
    public static function isAutologinAllowed($user)
    {
        return $user->disable_autologin != 1 && !self::isApiKeyLoginOnly($user);
    }

    /**
     * Resolves the user who is allowed to be logged in without credentials
     * (autologin, login as user, login by token, chat accept link).
     * Must be used by every endpoint which calls erLhcoreClassUser::setLoggedUser
     *
     * @param mixed $user user model or user ID (false/non numeric values are not resolved)
     * @return erLhcoreClassModelUser|false
     */
    public static function fetchAutologinUser($user)
    {
        if (!($user instanceof erLhcoreClassModelUser)) {
            $user = is_numeric($user) ? erLhcoreClassModelUser::fetch((int)$user) : false;
        }

        if (!($user instanceof erLhcoreClassModelUser) || self::isAutologinAllowed($user) == false || self::isLoginIpAllowed($user) == false) {
            return false;
        }

        return $user;
    }

    public static function updateStats($userlist)
    {
        $remap = array();
        
        if (!empty($userlist)) {
            $db = ezcDbInstance::get();
            $sql = "SELECT distinct user_id, active_chats, pending_chats, inactive_chats FROM lh_userdep WHERE user_id IN (" . implode(',', array_keys($userlist)) . ')';
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($stats as $stat) {
                $remap[$stat['user_id']] = array(
                    'ac' => $stat['active_chats'],
                    'pc' => $stat['pending_chats'],
                    'ic' => $stat['inactive_chats'],
                    'acrt' => erLhcoreClassModelChat::getCount(array(
                        'filter' => array(
                            'user_id' => $stat['user_id'],
                            'status' => 1
                        )
                    )) /*+ erLhcoreClassModelMailconvConversation::getCount(array(
                            'filter' => array(
                                'user_id' => $stat['user_id'],
                                'status' => 1
                            )
                        ))*/
                );
            }
        }
        
        return $remap;
    }
}

?>