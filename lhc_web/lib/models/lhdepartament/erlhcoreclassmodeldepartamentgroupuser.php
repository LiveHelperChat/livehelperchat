<?php

class erLhcoreClassModelDepartamentGroupUser
{
    
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_departament_group_user';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_group_id' => $this->dep_group_id,
            'user_id' => $this->user_id,
            'read_only' => $this->read_only,
            'exc_indv_autoasign' => $this->exc_indv_autoasign,
            'assign_priority' => $this->assign_priority,
            'chat_min_priority' => $this->chat_min_priority,
            'chat_max_priority' => $this->chat_max_priority,
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function getUserGroupsIds($user_id, $read_only = false)
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('SELECT dep_group_id FROM lh_departament_group_user WHERE user_id = :user_id AND read_only = :read_only');
        $stmt->bindValue( ':user_id',$user_id);
        $stmt->bindValue( ':read_only',$read_only === false ? 0 : 1);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getUserGroupsExcAutoassignIds($user_id)
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('SELECT dep_group_id FROM lh_departament_group_user WHERE user_id = :user_id AND exc_indv_autoasign = 1');
        $stmt->bindValue( ':user_id',$user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getUserGroupsParams($user_id)
    {
        $itemsRemap = [];

        foreach (self::getList(['limit' => false, 'filter' => ['user_id' => $user_id]]) as $item) {
            $itemsRemap[$item->dep_group_id] = $item->getState();
        }

        return $itemsRemap;
    }

    public static function addUserDepartmentGroups($userData, $groupsIds, $readOnly = false, $excludeAutoAssign = array(), $paramsAssignment = array())
    {
        $groups = self::getList(array('filter' => array('user_id' => $userData->id, 'read_only' => ($readOnly === false ? 0 : 1))));
        
        $oldMembers = array();
        
        foreach ($groups as $group) {
            if (!in_array($group->id, $groupsIds)) {
                $group->removeThis();
            } else {
                $oldMembers[] = $group->id;
            }
        }
        
        // Save new assignments
        foreach ($groupsIds as $groupId) {
            if (!in_array($groupId, $oldMembers)) {
                $member = new self();
                $member->dep_group = erLhcoreClassModelDepartamentGroup::fetch($groupId);
                if ($member->dep_group instanceof erLhcoreClassModelDepartamentGroup) {
                    $member->user_id = $userData->id;
                    $member->dep_group_id = $groupId;
                    $member->read_only = ($readOnly === false ? 0 : 1);
                    $member->exc_indv_autoasign = $readOnly == false && in_array($groupId, $excludeAutoAssign) ? 1 : 0;
                    $member->assign_priority = isset($paramsAssignment['assign_priority'][$groupId]) ? (int)$paramsAssignment['assign_priority'][$groupId] : 0;
                    $member->chat_min_priority = isset($paramsAssignment['chat_min_priority'][$groupId]) ? (int)$paramsAssignment['chat_min_priority'][$groupId] : 0;
                    $member->chat_max_priority = isset($paramsAssignment['chat_max_priority'][$groupId]) ? (int)$paramsAssignment['chat_max_priority'][$groupId] : 0;
                    $member->saveThis();
                }
            }
        }

        if (isset($_SESSION['lhCacheUserDepartaments_'.$userData->id])) {
            unset($_SESSION['lhCacheUserDepartaments_'.$userData->id]);
        }
    }
    
    public function afterSave()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM lh_userdep WHERE dep_group_id = :dep_group_id AND user_id = :user_id ');//AND ro = :ro
        $stmt->bindValue( ':dep_group_id', $this->dep_group_id);
        $stmt->bindValue( ':user_id', $this->user_id);
        //$stmt->bindValue( ':ro', $this->read_only);
        $stmt->execute();
        
        foreach ($this->__get('dep_group')->departments_ids as $depId)
        {
            $stmt = $db->prepare('INSERT INTO lh_userdep (user_id,dep_id,hide_online,last_activity,last_accepted,active_chats,type,dep_group_id,max_chats,exclude_autoasign,always_on,ro,exc_indv_autoasign,assign_priority,chat_max_priority,chat_min_priority) VALUES 
            (:user_id,:dep_id,:hide_online,0,0,:active_chats,1,:dep_group_id,:max_chats,:exclude_autoasign,:always_on,:ro,:exc_indv_autoasign,:assign_priority,:chat_max_priority,:chat_min_priority)');
            $stmt->bindValue(':user_id',$this->user_id);
            $stmt->bindValue(':dep_id',$depId);
            $stmt->bindValue(':hide_online',$this->user->hide_online);
            $stmt->bindValue(':dep_group_id',$this->dep_group_id);
            $stmt->bindValue(':ro',$this->read_only);
            $stmt->bindValue(':max_chats',$this->user->max_active_chats);
            $stmt->bindValue(':exclude_autoasign', $this->user->exclude_autoasign);
            $stmt->bindValue(':exc_indv_autoasign', $this->exc_indv_autoasign);
            $stmt->bindValue(':always_on', $this->user->always_on);
            $stmt->bindValue(':active_chats',erLhcoreClassChat::getCount(array('filter' => array('user_id' => $this->user_id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))));
            $stmt->bindValue(':assign_priority', $this->assign_priority);
            $stmt->bindValue(':chat_max_priority', $this->chat_max_priority);
            $stmt->bindValue(':chat_min_priority', $this->chat_min_priority);
            $stmt->execute();
        }
        
        erLhcoreClassModelDepartamentGroupMember::updateUserDepartmentsIds($this->user_id);
    }
    
    public function afterRemove()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM lh_userdep WHERE dep_group_id = :dep_group_id AND user_id = :user_id AND ro =:ro');
        $stmt->bindValue( ':dep_group_id', $this->dep_group_id);
        $stmt->bindValue( ':user_id', $this->user_id);
        $stmt->bindValue( ':ro', $this->read_only);
        $stmt->execute();
        
        erLhcoreClassModelDepartamentGroupMember::updateUserDepartmentsIds($this->user_id);
    }
    
    public function __get($var)
    {
        switch ($var) {
            case 'dep_group':
                $this->dep_group = erLhcoreClassModelDepartamentGroup::fetch($this->dep_group_id);
                return $this->dep_group;
                break;
                
            case 'user':
                $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                return $this->user;
                break;
            
            default:
                break;
        }
    }

    public $id = null;

    public $dep_group_id = 0;
    public $user_id = 0;
    public $read_only = 0;
    public $exc_indv_autoasign = 0;
    public $assign_priority = 0;
    public $chat_min_priority = 0;
    public $chat_max_priority = 0;
}

?>