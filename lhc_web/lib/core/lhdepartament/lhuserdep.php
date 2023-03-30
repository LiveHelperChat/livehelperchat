<?php

class erLhcoreClassUserDep
{
    function __construct()
    {

    }

    public static function getUserReadDepartments($userID = false, $cacheVersion = 0)
    {
        if ($userID === false && ($currentUser = erLhcoreClassUser::instance()) && $currentUser->isLogged()) {
            $userID = $currentUser->getUserID();
            $cacheVersion = $currentUser->cache_version;
        }

        if (isset($GLOBALS['lhCacheUserDepartamentsRo_' . $userID . '_' . $cacheVersion])) return $GLOBALS['lhCacheUserDepartamentsRo_' . $userID.'_'.$cacheVersion];
        if (isset($_SESSION['lhCacheUserDepartamentsRo_' . $userID . '_' . $cacheVersion])) return $_SESSION['lhCacheUserDepartamentsRo_' . $userID.'_'.$cacheVersion];

        self::getUserDepartaments($userID,$cacheVersion);

        return isset($GLOBALS['lhCacheUserDepartamentsRo_' . $userID . '_' . $cacheVersion]) ? $GLOBALS['lhCacheUserDepartamentsRo_' . $userID.'_'.$cacheVersion] : array();
    }

    public static function getUserDepartaments($userID = false, $cacheVersion = 0)
    {
        if ($userID === false && ($currentUser = erLhcoreClassUser::instance()) && $currentUser->isLogged()) {
            $userID = $currentUser->getUserID();
            $cacheVersion = $currentUser->cache_version;
        }

        if (isset($GLOBALS['lhCacheUserDepartaments_' . $userID . '_' . $cacheVersion])) return $GLOBALS['lhCacheUserDepartaments_' . $userID . '_' . $cacheVersion];
        if (isset($_SESSION['lhCacheUserDepartaments_' . $userID . '_' . $cacheVersion])) return $_SESSION['lhCacheUserDepartaments_' . $userID . '_' . $cacheVersion];

        $db = ezcDbInstance::get();

        $stmt = $db->prepare('SELECT lh_userdep.dep_id, lh_userdep.ro FROM lh_userdep WHERE user_id = :user_id ORDER BY id ASC');
        $stmt->bindValue(':user_id', $userID);

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $idArray = array();
        $idArrayRo = array();

        foreach ($rows as $row) {
            $idArray[] = $row['dep_id'];
            if ($row['ro'] == 1) {
                $idArrayRo[] = $row['dep_id'];
            }
        }

        $GLOBALS['lhCacheUserDepartaments_' . $userID . '_' . $cacheVersion] = $idArray;
        $_SESSION['lhCacheUserDepartaments_' . $userID . '_' . $cacheVersion] = $idArray;

        $GLOBALS['lhCacheUserDepartamentsRo_' . $userID . '_' . $cacheVersion] = $idArrayRo;
        $_SESSION['lhCacheUserDepartamentsRo_' . $userID . '_' . $cacheVersion] = $idArrayRo;

        return $idArray;
    }

    public static function getUserDepartamentsIndividual($userID = false, $readOnly = false)
    {
        $db = ezcDbInstance::get();

        if ($userID === false) {
            $userID = erLhcoreClassUser::instance()->getUserID();
        }

        $stmt = $db->prepare('SELECT dep_id FROM lh_userdep WHERE user_id = :user_id AND type = 0 AND ro = ' . (int)$readOnly . ' ORDER BY id ASC');
        $stmt->bindValue(':user_id', $userID);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getUserDepartamentsExcAutoassignIds($userID = false)
    {
        $db = ezcDbInstance::get();

        if ($userID === false) {
            $userID = erLhcoreClassUser::instance()->getUserID();
        }

        $stmt = $db->prepare('SELECT dep_id FROM lh_userdep WHERE user_id = :user_id AND type = 0 AND exc_indv_autoasign = 1 ORDER BY id ASC');
        $stmt->bindValue(':user_id', $userID);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getUserIndividualParams($userID = false)
    {
        $db = ezcDbInstance::get();

        if ($userID === false) {
            $userID = erLhcoreClassUser::instance()->getUserID();
        }

        $stmt = $db->prepare('SELECT `assign_priority`,`chat_min_priority`,`chat_max_priority`,`dep_id` FROM `lh_userdep` WHERE `user_id` = :user_id AND `type` = 0 ORDER BY `id` ASC');
        $stmt->bindValue(':user_id', $userID);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rowsReturn = [];

        foreach ($rows as $row) {
            $rowsReturn[$row['dep_id']] = $row;
        }

        return $rowsReturn;
    }

    public static function conditionalDepartmentGroupFilter($userID = false, $column = 'id') {

        $useUser = false;

        if ($userID === false) {
            $userID = erLhcoreClassUser::instance()->getUserID();
            $useUser = true;
        }

        if (erLhcoreClassRole::hasAccessTo($userID, 'lhdepartment', 'see_all') === true) {
            if ($useUser === true) {
                $limitation = erLhcoreClassUser::instance()->hasAccessTo('lhdepartment', 'see_all', true);
                if ($limitation !== true) {
                    $limitationParams = json_decode($limitation, true);

                    $departments = [];
                    $limitsApplied = false;

                    if (isset($limitationParams['group'])) {
                        erLhcoreClassChat::validateFilterIn($limitationParams['group']);
                        $departments = $limitationParams['group'];
                        $limitsApplied = true;
                    }

                    if ($limitsApplied === true && empty($departments)) {
                        return array('filterin' => array($column => [-1]));
                    } elseif ($limitsApplied === true) {
                        erLhcoreClassChat::validateFilterIn($departments);
                        return array('filterin' => array($column => $departments));
                    }
                }
            }

            return array();
        }

        $db = ezcDbInstance::get();
        $stmt = $db->prepare('SELECT dep_group_id FROM lh_departament_group_user WHERE user_id = :user_id');
        $stmt->bindValue( ':user_id',$userID);
        $stmt->execute();
        $userGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($userGroups)) {
            return array('filter' => array($column => -1));
        } else {
            return array('filterin' => array($column => $userGroups));
        }
    }

    public static function conditionalDepartmentFilter($userID = false, $column = 'id', $cacheVersion = 0) {

        $useUser = false;

        if ($userID === false) {
            $userID = erLhcoreClassUser::instance()->getUserID();
            $cacheVersion = erLhcoreClassUser::instance()->cache_version;
            $useUser = true;
        }

        if (erLhcoreClassRole::hasAccessTo($userID, 'lhdepartment', 'see_all') === true) {
            if ($useUser === true) {
                $limitation = erLhcoreClassUser::instance()->hasAccessTo('lhdepartment', 'see_all', true);
                if ($limitation !== true) {
                    $limitationParams = json_decode($limitation, true);

                    $departments = [];
                    $limitsApplied = false;

                    if (isset($limitationParams['group'])) {
                        erLhcoreClassChat::validateFilterIn($limitationParams['group']);
                        $departments = erLhcoreClassModelDepartamentGroupMember::getCount(['filterin' => ['dep_group_id' => $limitationParams['group']]],false,'dep_id', 'dep_id', false, true, true);
                        $limitsApplied = true;
                    }

                    if (isset($limitationParams['department'])) {
                        $departments = array_merge($departments, $limitationParams['department']);
                        $limitsApplied = true;
                    }

                    if ($limitsApplied === true && empty($departments)) {
                        return array('filterin' => array($column => [-1]));
                    } elseif ($limitsApplied === true) {
                        erLhcoreClassChat::validateFilterIn($departments);
                        return array('filterin' => array($column => $departments));
                    }
                }
            }
            return array();
        }

        $departments = self::parseUserDepartmetnsForFilter($userID, $cacheVersion);

        if ($departments === true) {
            return  array();
        }

        return array('filterin' => array($column => $departments));

    }

    public static function parseUserDepartmetnsForFilter($userID, $cacheVersion = 0)
    {
        $userDepartments = self::getUserDepartaments($userID, $cacheVersion);

        if (!empty($userDepartments)) {

            // Not needed
            $index = array_search(-1, $userDepartments);
            if ($index !== false) {
                unset($userDepartments[$index]);
            }

            $index = array_search(0, $userDepartments);
            if ($index !== false) {
                return true; // All departments
            }

            if (!empty($userDepartments)) {
                return $userDepartments;
            } else {
                return array(-1); // No assigned departments
            }

        } else {
            return array(-1); // No assigned departments
        }
    }

    public static function getDefaultUserDepartment($userID = false)
    {
        $db = ezcDbInstance::get();

        $stmt = $db->prepare('SELECT lh_userdep.dep_id FROM lh_userdep WHERE user_id = :user_id and ro = 0 ORDER BY id ASC LIMIT 1');
        $stmt->bindValue(':user_id', $userID);
        $stmt->execute();

        $userDepartment = $stmt->fetch(PDO::FETCH_COLUMN);

        return $userDepartment;
    }

    public static function addUserDepartaments($Departaments, $userID = false, $UserData = false, $readOnly = array(), $excludeAutoAssign = array(), $paramsAssignment = array())
    {
        $db = ezcDbInstance::get();
        if ($userID === false) {
            $currentUser = erLhcoreClassUser::instance();
            $userID = $currentUser->getUserID();
        }

        if (!isset($paramsAssignment['only_global'])){
            $stmt = $db->prepare('DELETE FROM lh_userdep WHERE user_id = :user_id AND type = 0');
            $stmt->bindValue(':user_id', $userID);
            $stmt->execute();
        } else {
            $stmt = $db->prepare('DELETE FROM lh_userdep WHERE user_id = :user_id AND type = 0 AND `dep_id` IN (0,-1)');
            $stmt->bindValue(':user_id', $userID);
            $stmt->execute();
        }

        foreach ($Departaments as $DepartamentID) {
            $stmt = $db->prepare('INSERT INTO lh_userdep (user_id,dep_id,hide_online,last_activity,last_accepted,active_chats,type,dep_group_id,max_chats,ro,pending_chats,inactive_chats,exclude_autoasign,always_on,exc_indv_autoasign,assign_priority,chat_max_priority,chat_min_priority) VALUES (:user_id,:dep_id,:hide_online,0,0,:active_chats,0,0,:max_chats,:ro,0,0,:exclude_autoasign,:always_on,:exc_indv_autoasign,:assign_priority,:chat_max_priority,:chat_min_priority)');
            $stmt->bindValue(':user_id', $userID);
            $stmt->bindValue(':max_chats', $UserData->max_active_chats);
            $stmt->bindValue(':dep_id', $DepartamentID);
            $stmt->bindValue(':hide_online', $UserData->hide_online);
            $stmt->bindValue(':exclude_autoasign', $UserData->exclude_autoasign);
            $stmt->bindValue(':exc_indv_autoasign', (in_array($DepartamentID, $excludeAutoAssign) || $DepartamentID == -1) ? 1 : 0);
            $stmt->bindValue(':ro', (in_array($DepartamentID, $readOnly) || $DepartamentID == -1) ? 1 : 0);
            $stmt->bindValue(':active_chats', erLhcoreClassChat::getCount(array('filter' => array('user_id' => $UserData->id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))));
            $stmt->bindValue(':always_on',$UserData->always_on);
            $stmt->bindValue(':assign_priority',(isset($paramsAssignment['assign_priority'][$DepartamentID]) ? (int)$paramsAssignment['assign_priority'][$DepartamentID] : 0));
            $stmt->bindValue(':chat_max_priority',(isset($paramsAssignment['chat_max_priority'][$DepartamentID]) ? (int)$paramsAssignment['chat_max_priority'][$DepartamentID] : 0));
            $stmt->bindValue(':chat_min_priority',(isset($paramsAssignment['chat_min_priority'][$DepartamentID]) ? (int)$paramsAssignment['chat_min_priority'][$DepartamentID] : 0));
            $stmt->execute();
        }

        if (isset($_SESSION['lhCacheUserDepartaments_' . $userID])) {
            unset($_SESSION['lhCacheUserDepartaments_' . $userID]);
        }

        if (isset($_SESSION['lhCacheUserDepartamentsRo_' . $userID])) {
            unset($_SESSION['lhCacheUserDepartamentsRo_' . $userID]);
        }
    }

    public static function getUserDepIds($user_id)
    {
        $db = ezcDbInstance::get();

        // Update in a such way to avoid deadlocks
        $stmt = $db->prepare('SELECT lh_userdep.id FROM lh_userdep WHERE user_id = :user_id');
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function setHideOnlineStatus($UserData)
    {

        // Update in a such way to avoid deadlocks
        $ids = self::getUserDepIds($UserData->id);

        if (!empty($ids)) {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE lh_userdep SET hide_online = :hide_online, hide_online_ts = :hide_online_ts, always_on = :always_on  WHERE id IN (' . implode(',', $ids) . ')');
            $stmt->bindValue(':hide_online', $UserData->hide_online);
            $stmt->bindValue(':hide_online_ts', time());
            $stmt->bindValue(':always_on', $UserData->always_on);
            $stmt->execute();
        }
    }

    public static function updateLastActivityByUser($user_id, $lastActivity, $lda = 0)
    {
        $ids = self::getUserDepIds($user_id);

        if (!empty($ids)) {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE lh_userdep SET last_activity = :last_activity'. ($lda > 0 ? ', lastd_activity = :lastd_activity' : '') .' WHERE id IN (' . implode(',', $ids) . ');');
            $stmt->bindValue(':last_activity', $lastActivity, PDO::PARAM_INT);
            $lda > 0 && $stmt->bindValue(':lastd_activity', (int)$lda, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public static function updateLastAcceptedByUser($user_id, $lastAccepted)
    {
        $ids = self::getUserDepIds($user_id);

        if (!empty($ids)) {
            $db = ezcDbInstance::get();
            try {
                $stmt = $db->prepare('UPDATE lh_userdep SET last_accepted = :last_accepted WHERE id IN (' . implode(',', $ids) . ');');
                $stmt->bindValue(':last_accepted', $lastAccepted, PDO::PARAM_INT);
                $stmt->execute();
            } catch (Exception $e) {
                try {
                    usleep(500);
                    $stmt = $db->prepare('UPDATE lh_userdep SET last_accepted = :last_accepted WHERE id IN (' . implode(',', $ids) . ');');
                    $stmt->bindValue(':last_accepted', $lastAccepted, PDO::PARAM_INT);
                    $stmt->execute();
                } catch (Exception $e) {
                    // Just give up
                }
            }

        }
    }

    public static function getSession()
    {
        if (!isset(self::$persistentSession)) {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager('./pos/lhdepartament')
            );
        }
        return self::$persistentSession;
    }

    private static $persistentSession;

}


?>