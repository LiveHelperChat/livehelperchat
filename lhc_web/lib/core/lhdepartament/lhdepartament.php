<?php

class erLhcoreClassDepartament{



   function __construct()
   {

   }

   public static function getDepartaments()
   {
         $db = ezcDbInstance::get();

         $stmt = $db->prepare('SELECT * FROM lh_departament ORDER BY id ASC');
         $stmt->execute();
         $rows = $stmt->fetchAll();

         return $rows;
   }

   public static function sortByStatus($departments) {

	   	$onlineDep = array();
	   	$offlineDep = array();

	   	foreach ($departments as $dep) {
	   		if ($dep->is_online === true){
	   			$onlineDep[] = $dep;
	   		} else {
	   			$offlineDep[] = $dep;
	   		}
	   	}

	   	return array_merge($onlineDep,$offlineDep);
   }

   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhdepartament' )
            );
        }
        return self::$persistentSession;
   }

   private static $persistentSession;

}


?>