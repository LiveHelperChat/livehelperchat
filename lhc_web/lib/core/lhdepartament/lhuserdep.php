<?php

class erLhcoreClassUserDep{

   function __construct()
   {

   }

   public static function getUserDepartaments($userID = false)
   {

         if (isset($GLOBALS['lhCacheUserDepartaments_'.$userID])) return $GLOBALS['lhCacheUserDepartaments_'.$userID];
         if (isset($_SESSION['lhCacheUserDepartaments_'.$userID])) return $_SESSION['lhCacheUserDepartaments_'.$userID];


         $db = ezcDbInstance::get();

         if ($userID === false)
         {
             $currentUser = erLhcoreClassUser::instance();
             $userID = $currentUser->getUserID();
         }

         $stmt = $db->prepare('SELECT lh_userdep.dep_id FROM lh_userdep WHERE user_id = :user_id ORDER BY id ASC');
         $stmt->bindValue( ':user_id',$userID);

         $stmt->execute();

         $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

         $idArray = array();

         foreach ($rows as $row)
         {
             $idArray[] = $row['dep_id'];
         }

         $GLOBALS['lhCacheUserDepartaments_'.$userID] = $idArray;
         $_SESSION['lhCacheUserDepartaments_'.$userID] = $idArray;

         return $idArray;
   }

   public static function getUserDepartamentsIndividual($userID = false)
   {
       $db = ezcDbInstance::get();
        
       if ($userID === false)
       {
           $userID = erLhcoreClassUser::instance()->getUserID();
       }
   
       $stmt = $db->prepare('SELECT dep_id FROM lh_userdep WHERE user_id = :user_id AND type = 0 ORDER BY id ASC');
       $stmt->bindValue( ':user_id',$userID);
        
       $stmt->execute();
        
       return $stmt->fetchAll(PDO::FETCH_COLUMN);
   }
   
   public static function parseUserDepartmetnsForFilter($userID) {   	   	
   		$userDepartments = self::getUserDepartaments($userID);
   		
   		if (!empty($userDepartments)) {
   			
   			// Not needed
   			$index = array_search(-1, $userDepartments);
   			if ($index !== false){
   				unset($userDepartments[$index]);
   			}
   		
   			$index = array_search(0, $userDepartments);
   			if ($index !== false){
   				return true; // All departments
   			}
   			
   			if (!empty($userDepartments)){
   				return $userDepartments;
   			} else {
   				return array(-1); // No assigned departments
   			}
   			
   		} else {
   			return array(-1); // No assigned departments
   		}
   }
   
   public static function getDefaultUserDepartment($userID = false) {
        $userDepartments = self::getUserDepartaments($userID);
   		return array_shift($userDepartments);
   }
      
   public static function addUserDepartaments($Departaments, $userID = false, $UserData = false)
   {
       $db = ezcDbInstance::get();
       if ($userID === false)
       {
           $currentUser = erLhcoreClassUser::instance();
           $userID = $currentUser->getUserID();
       }
   
       $stmt = $db->prepare('DELETE FROM lh_userdep WHERE user_id = :user_id AND type = 0');
       $stmt->bindValue( ':user_id',$userID);
       $stmt->execute();
   
       foreach ($Departaments as $DepartamentID)
       {
           $stmt = $db->prepare('INSERT INTO lh_userdep (user_id,dep_id,hide_online,last_activity,last_accepted,active_chats,type,dep_group_id) VALUES (:user_id,:dep_id,:hide_online,0,0,:active_chats,0,0)');
           $stmt->bindValue( ':user_id',$userID);
           $stmt->bindValue( ':dep_id',$DepartamentID);
           $stmt->bindValue( ':hide_online',$UserData->hide_online);
           $stmt->bindValue( ':active_chats',erLhcoreClassChat::getCount(array('filter' => array('user_id' => $UserData->id, 'status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))));
           $stmt->execute();
       }
   
       if (isset($_SESSION['lhCacheUserDepartaments_'.$userID])){
           unset($_SESSION['lhCacheUserDepartaments_'.$userID]);
       }
   
   }

   public static function setHideOnlineStatus($UserData) {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('UPDATE lh_userdep SET hide_online = :hide_online WHERE user_id = :user_id');
       $stmt->bindValue( ':hide_online',$UserData->hide_online);
       $stmt->bindValue( ':user_id',$UserData->id);
       $stmt->execute();
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