<?php

class erLhcoreClassRoleFunction{
      
   function __construct()
   {
 
   }
      
   public static function getRoleFunctions($role_id)
   {
        $db = ezcDbInstance::get();
                 
        $stmt = $db->prepare('SELECT * FROM lh_rolefunction WHERE role_id = :role_id ORDER BY id ASC');   
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