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

   public static function getConditionalUserFilter($userID = false, $groupsLimit = false, $column = 'id')
   {
       if ($userID === false) {
           $userID = erLhcoreClassUser::instance()->getUserID();
       }

       if (erLhcoreClassRole::hasAccessTo($userID, 'lhuser', 'see_all') === true) {
           return array();
       }

       // User should be able to see users from
       // He is a member of group
       $user = erLhcoreClassModelUser::fetch($userID);

       $groups = erLhcoreClassGroupRole::getGroupsAccessedByUser($user)['groups'];

       if ($groupsLimit === true) {
           return array('filterin' => array($column => $groups));
       }

       if ($groupsLimit === false && !erLhcoreClassRole::hasAccessTo($userID, 'lhuser', 'see_all_group_users') === true) {
           return array('filterin' => array($column => [$userID]));
       }

       $userID = erLhcoreClassModelGroupUser::getCount(array('filterin' => array('group_id' => $groups)), '', false, '`user_id`', false, true, true);

       return array('filterin' => array($column => $userID));
   }


}


?>