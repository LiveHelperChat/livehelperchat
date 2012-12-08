<?php

class erLhcoreClassGroupRole{
      
   function __construct()
   {
 
   }
      
   public static function getGroupRoles($group_id)
   {
        $db = ezcDbInstance::get();
                 
        $stmt = $db->prepare('SELECT lh_grouprole.id as assigned_id,lh_role.* FROM lh_role INNER JOIN lh_grouprole ON lh_grouprole.role_id = lh_role.id WHERE lh_grouprole.group_id = :group_id ORDER BY id ASC');   
        $stmt->bindValue( ':group_id',$group_id);                 
        $stmt->execute();
        $rows = $stmt->fetchAll();
                
        return $rows;  
   }   
     
   public static function getRoleGroups($role_id)
   {
        $db = ezcDbInstance::get();
                 
        $stmt = $db->prepare('SELECT lh_grouprole.id as assigned_id,lh_group.* FROM lh_group INNER JOIN lh_grouprole ON lh_grouprole.group_id = lh_group.id WHERE lh_grouprole.role_id = :role_id ORDER BY id ASC');   
        $stmt->bindValue( ':role_id',$role_id);                 
        $stmt->execute();
        $rows = $stmt->fetchAll();
                
        return $rows;  
   } 
      
   public static function getGroupNotAssignedRoles($group_id)
   {
        $db = ezcDbInstance::get();                 
        $stmt = $db->prepare('SELECT lh_role.* FROM lh_role WHERE lh_role.id NOT IN ( SELECT role_id FROM lh_grouprole WHERE group_id = :group_id)  ORDER BY id ASC');   
        $stmt->bindValue( ':group_id',$group_id);                 
        $stmt->execute();
        $rows = $stmt->fetchAll();
                
        return $rows;  
   } 
       
   public static function getRoleNotAssignedGroups($role_id)
   {
        $db = ezcDbInstance::get();                 
        $stmt = $db->prepare('SELECT lh_group.* FROM lh_group WHERE lh_group.id NOT IN ( SELECT group_id FROM lh_grouprole WHERE role_id = :role_id)  ORDER BY id ASC');   
        $stmt->bindValue( ':role_id',$role_id);                 
        $stmt->execute();
        $rows = $stmt->fetchAll();
                
        return $rows;  
   }
   
   public static function deleteGroupRole($AssigneID)
   {
       $AssignedUser = erLhcoreClassRole::getSession()->load( 'erLhcoreClassModelGroupRole', $AssigneID);
       erLhcoreClassRole::getSession()->delete($AssignedUser);
   }


}


?>