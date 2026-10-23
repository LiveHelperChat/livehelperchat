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