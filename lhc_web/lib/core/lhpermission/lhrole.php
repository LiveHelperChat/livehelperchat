<?php

class erLhcoreClassRole{
      
   function __construct()
   {
 
   }
   
   public static function getRoleList()
   {
        $db = ezcDbInstance::get();
                 
        $stmt = $db->prepare('SELECT * FROM lh_role ORDER BY id ASC');           
        $stmt->execute();
        $rows = $stmt->fetchAll();
                
        return $rows;  
   }
   
   public static function canDeleteRole($roleID) {
	   	$db = ezcDbInstance::get();
	   	 
	   	$stmt = $db->prepare('SELECT count(*) FROM lh_grouprole WHERE role_id = :role_id');
	   	$stmt->bindValue(':role_id',$roleID,PDO::PARAM_INT);
	   	$stmt->execute();
	   	   	
	   	return $stmt->fetchColumn() == 0;
   }
   
   public static function canUseByModuleAndFunction($AccessArray, $module, $functions) {
       // Global rights
       if (isset($AccessArray['*']['*']) || isset($AccessArray[$module]['*']))
       {
           return true;
       }
        
       // Provided rights have to be set
       if (is_array($functions))
       {
           foreach ($functions as $function)
           {
               // Missing one of provided right
               if (!isset($AccessArray[$module][$function])) return false;
           }
       } else {
           if (!isset($AccessArray[$module][$functions])) return false;
       }
        
       return true;
   }
   
   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {            
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhpermission' )
            );
        }
        return self::$persistentSession;
   }
      
   /**
    * Returns something like that
    * Array
        (
            [lhdepartment] => Array
                (
                    [alldepartaments] => 1
                )
        
            [*] => Array
                (
                    [*] => 1
                )
        
        )
    * 
    * */  
   public static function accessArrayByUserID($user_id)
   {
       $db = ezcDbInstance::get();
       
       $stmt = $db->prepare('SELECT lh_rolefunction.module,lh_rolefunction.function,lh_rolefunction.limitation     

       FROM lh_rolefunction
       
       INNER JOIN lh_role ON lh_role.id = lh_rolefunction.role_id
       INNER JOIN lh_grouprole ON lh_role.id = lh_grouprole.role_id
       INNER JOIN lh_groupuser ON lh_groupuser.group_id = lh_grouprole.group_id       
       INNER JOIN lh_group ON lh_grouprole.group_id = lh_group.id
           
       WHERE lh_groupuser.user_id = :user_id AND lh_group.disabled = 0');
        
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $AccessArray = array();
        
        foreach ($rows as $Policy) {
            $AccessArray[$Policy['module']][$Policy['function']] = $Policy['limitation'] != '' ? $Policy['limitation'] : true;
        }
        
        return $AccessArray;
    }

    private static $persistentSession;
}

?>