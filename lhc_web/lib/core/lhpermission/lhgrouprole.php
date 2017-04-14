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

   public static function assignGroupMembers($group, $members)
   {             
       $currentGroups = erLhcoreClassModelGroupWork::getList(array('filter' => array('group_id' => $group->id)));
       
       $currentGroupsIds = array();
       
       foreach ($currentGroups as $currentGroup) {
           $currentGroupsIds[] = $currentGroup->group_work_id;
       }
       
       $groupsToAssign = array_diff($members, $currentGroupsIds);
       
       foreach ($groupsToAssign as $groupToAssign) {
           $groupWork = new erLhcoreClassModelGroupWork();
           $groupWork->group_id = $group->id;
           $groupWork->group_work_id = $groupToAssign;
           $groupWork->saveThis();
       }
       
       $groupsToRemoveId = array_diff($currentGroupsIds, $members);
       
       if (!empty($groupsToRemoveId)) {
            $groupsToRemove = erLhcoreClassModelGroupWork::getList(array('filterin' => array('group_work_id' => $groupsToRemoveId),'filter' => array('group_id' => $group->id)));
            foreach ($groupsToRemove as $groupToRemove) {
                $groupToRemove->removeThis();
            }
       }
   }
   
   public static function getGroupsAccessedByUser($userEditing)
   {
       $groups = $userEditing->user_groups_id;
       $groupsAccessed = erLhcoreClassModelGroupWork::getList(array('filterin' => array('group_id' => $groups)));
       
       foreach ($groupsAccessed as $groupAccessed) {
           $groups[] = $groupAccessed->group_work_id;
       }
       
       return array_unique($groups);
   }
   
   public static function canEditUserGroups($userEditing, $userToEdit)
   {
       $accessArray = erLhcoreClassRole::accessArrayByUserID( $userEditing->id );
       $canGlobalEdit = erLhcoreClassRole::canUseByModuleAndFunction($accessArray, 'lhuser', 'editusergroupall');
       
       if ($canGlobalEdit == true) {
           return true;
       }
       
       // Returns list of agroups user can work with
       $groups = self::getGroupsAccessedByUser($userEditing);
       
       $groupsDifferences = array_diff($userToEdit->user_groups_id, $groups);
       
       // If user can access all groups
       if (empty($groupsDifferences)){
           return true;
       }
       
       return false;
   }

}


?>