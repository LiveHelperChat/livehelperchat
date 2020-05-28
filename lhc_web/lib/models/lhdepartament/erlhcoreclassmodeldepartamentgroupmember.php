<?php

class erLhcoreClassModelDepartamentGroupMember
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_departament_group_member';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'dep_group_id' => $this->dep_group_id
        );
    }

    public function __toString()
    {
        return $this->dep;
    }
    
    public function afterSave()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('SELECT user_id FROM lh_departament_group_user WHERE dep_group_id = :dep_group_id');
        $stmt->bindValue( ':dep_group_id', $this->dep_group_id);
        $stmt->execute();

        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($userIds as $userId) {
            
            $stmt = $db->prepare('SELECT `hide_online`,`max_active_chats`,`exclude_autoasign`,`always_on` FROM lh_users WHERE id = :user_id');
            $stmt->bindValue( ':user_id', $userId);
            $stmt->execute();
            $dataUser = $stmt->fetch(PDO::FETCH_ASSOC);
            $hide_online = $dataUser['hide_online'];
            $maxChats = $dataUser['max_active_chats'];
            $excludeAutoasign = $dataUser['exclude_autoasign'];
            $alwaysOn = $dataUser['always_on'];

            $stmt = $db->prepare('INSERT INTO lh_userdep (user_id,dep_id,hide_online,last_activity,last_accepted,active_chats,type,dep_group_id,max_chats,exclude_autoasign,always_on) VALUES (:user_id,:dep_id,:hide_online,0,0,:active_chats,1,:dep_group_id,:max_chats,:exclude_autoasign,:always_on)');
            $stmt->bindValue( ':user_id', $userId);
            $stmt->bindValue( ':dep_id', $this->dep_id);
            $stmt->bindValue( ':hide_online', $hide_online);
            $stmt->bindValue( ':dep_group_id',$this->dep_group_id);
            $stmt->bindValue( ':active_chats',erLhcoreClassChat::getCount(array('filter' => array('user_id' => $userId, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))));
            $stmt->bindValue( ':max_chats',$maxChats);
            $stmt->bindValue( ':always_on',$alwaysOn);
            $stmt->bindValue( ':exclude_autoasign',$excludeAutoasign);
            $stmt->execute();

            self::updateUserDepartmentsIds($userId);
        }
    }
    
    public static function updateUserDepartmentsIds($userId)
    {
        $db = ezcDbInstance::get();
        
        $stmt = $db->prepare('SELECT dep_id FROM lh_userdep WHERE user_id = :user_id');
        $stmt->bindValue( ':user_id', $userId);
        $stmt->execute();
        $departments_ids_array = $stmt->fetchAll(PDO::FETCH_COLUMN);
         
        $stmt = $db->prepare('UPDATE lh_users SET departments_ids = :departments_ids WHERE id = :user_id');
        $stmt->bindValue( ':user_id', $userId);
        $stmt->bindValue( ':departments_ids', is_array($departments_ids_array) ? implode(',', array_unique($departments_ids_array)) : '');
        $stmt->execute();
    }
    
    public function afterRemove()
    {
        $db = ezcDbInstance::get();

        $stmt = $db->prepare("SELECT user_id FROM lh_userdep WHERE dep_group_id = :dep_group_id AND dep_id = :dep_id");
        $stmt->bindValue( ':dep_group_id', $this->dep_group_id);
        $stmt->bindValue( ':dep_id', $this->dep_id);
        $stmt->execute();

        $user_ids = array_unique($stmt->fetchAll(PDO::FETCH_COLUMN));
        
        $stmt = $db->prepare('DELETE FROM lh_userdep WHERE dep_group_id = :dep_group_id AND dep_id = :dep_id');
        $stmt->bindValue( ':dep_group_id', $this->dep_group_id);
        $stmt->bindValue( ':dep_id', $this->dep_id);
        $stmt->execute();
        
        if (!empty($user_ids)) {            
            foreach ($user_ids as $userId) {
                self::updateUserDepartmentsIds($userId);
            }
        }
    }
    
    public function __get($var)
    {
        switch ($var) {
            case 'dep':
                $this->dep = null;
                return $this->dep;
                break;
            
            default:
                break;
        }
    }

    public $id = null;

    public $dep_id = 0;

    public $dep_group_id = 0;
}

?>