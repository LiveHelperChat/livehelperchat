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
            [lhdepartament] => Array
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
       
       $stmt = $db->prepare('SELECT lh_rolefunction.module,lh_rolefunction.function       
       FROM `lh_rolefunction`
       
       INNER JOIN lh_role ON lh_role.id = lh_rolefunction.role_id
       INNER JOIN lh_grouprole ON lh_role.id = lh_grouprole.role_id
       INNER JOIN lh_groupuser ON lh_groupuser.group_id = lh_grouprole.group_id
       
       WHERE lh_groupuser.user_id = :user_id'); 

       $stmt->bindValue( ':user_id',$user_id);   
              
       $stmt->execute();
       $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
       $AccessArray = array() ;
       
       foreach ($rows as $Policy)
       {
           $AccessArray[$Policy['module']][$Policy['function']] = true;
       }
       
       return $AccessArray;      
   }
   
   private static $persistentSession;

}


?>