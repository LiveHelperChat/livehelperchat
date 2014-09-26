<?php

class erLhcoreClassGroupUser{
      
   function __construct()
   {
 
   }
      
   public static function getGroupUsers($group_id)
   {
        $db = ezcDbInstance::get();
                 
        $stmt = $db->prepare('SELECT lh_groupuser.id as assigned_id,lh_users.* FROM lh_groupuser INNER JOIN lh_users ON lh_groupuser.user_id = lh_users.id WHERE group_id = :group_id ORDER BY id ASC');   
        $stmt->bindValue( ':group_id',$group_id);                 
        $stmt->execute();
        $rows = $stmt->fetchAll();
                
        return $rows;  
   } 
      
   public static function getGroupNotAssignedUsers($group_id)
   {
        $db = ezcDbInstance::get();                 
        $stmt = $db->prepare('SELECT lh_users.* FROM lh_users WHERE lh_users.id NOT IN ( SELECT user_id FROM lh_groupuser WHERE group_id = :group_id)  ORDER BY id ASC');   
        $stmt->bindValue( ':group_id',$group_id);                 
        $stmt->execute();
        $rows = $stmt->fetchAll();
                
        return $rows;  
   }
   
   public static function deleteGroupUser($AssigneID)
   {
       $AssignedUser = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelGroupUser', $AssigneID);
       erLhcoreClassUser::getSession()->delete($AssignedUser);
   }

}


?>