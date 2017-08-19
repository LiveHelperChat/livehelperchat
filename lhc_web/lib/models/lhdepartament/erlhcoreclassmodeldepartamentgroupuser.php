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
            'user_id' => $this->user_id
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function getUserGroupsIds($user_id)
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('SELECT dep_group_id FROM lh_departament_group_user WHERE user_id = :user_id');
        $stmt->bindValue( ':user_id',$user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public static function addUserDepartmentGroups($userData, $groupsIds)
    {
        $groups = self::getList(array('filter' => array('user_id' => $userData->id)));
        
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
                $member->user_id = $userData->id;
                $member->dep_group_id = $groupId;
                $member->saveThis();
            }
        }

        if (isset($_SESSION['lhCacheUserDepartaments_'.$userData->id])) {
            unset($_SESSION['lhCacheUserDepartaments_'.$userData->id]);
        }
    }
    
    public function afterSave()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM lh_userdep WHERE dep_group_id = :dep_group_id AND user_id = :user_id');
        $stmt->bindValue( ':dep_group_id', $this->dep_group_id);
        $stmt->bindValue( ':user_id', $this->user_id);
        $stmt->execute();
        
        foreach ($this->dep_group->departments_ids as $depId) 
        {
            $stmt = $db->prepare('INSERT INTO lh_userdep (user_id,dep_id,hide_online,last_activity,last_accepted,active_chats,type,dep_group_id,max_chats) VALUES (:user_id,:dep_id,:hide_online,0,0,:active_chats,1,:dep_group_id,:max_chats)');
            $stmt->bindValue( ':user_id',$this->user_id);
            $stmt->bindValue( ':dep_id',$depId);
            $stmt->bindValue( ':hide_online',$this->user->hide_online);
            $stmt->bindValue( ':dep_group_id',$this->dep_group_id);
            $stmt->bindValue( ':max_chats',$this->user->max_active_chats);
            $stmt->bindValue( ':active_chats',erLhcoreClassChat::getCount(array('filter' => array('user_id' => $this->user_id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))));
            $stmt->execute();
        }
        
        erLhcoreClassModelDepartamentGroupMember::updateUserDepartmentsIds($this->user_id);
    }
    
    public function afterRemove()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM lh_userdep WHERE dep_group_id = :dep_group_id AND user_id = :user_id');
        $stmt->bindValue( ':dep_group_id', $this->dep_group_id);
        $stmt->bindValue( ':user_id', $this->user_id);
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
}

?>