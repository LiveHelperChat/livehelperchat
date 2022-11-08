<?php

class erLhcoreClassRoleFunction{
      
   function __construct()
   {
 
   }
      
   public static function getRoleFunctions($role_id, $sort = '`id` ASC')
   {
        $db = ezcDbInstance::get();
                 
        $stmt = $db->prepare('SELECT * FROM lh_rolefunction WHERE role_id = :role_id ORDER BY '.$sort);
        $stmt->bindValue( ':role_id',$role_id);                 
        $stmt->execute();
        $rows = $stmt->fetchAll();
                
        return $rows;  
   }
   
   public static function deleteRolePolicy($PolicyID)
   {
       $RoleFunction = erLhcoreClassRole::getSession()->load( 'erLhcoreClassModelRoleFunction', $PolicyID);
       erLhcoreClassRole::getSession()->delete($RoleFunction);
   }

}


?>