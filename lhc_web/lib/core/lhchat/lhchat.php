<?php

/**
 * Status - 
 * 0 - Pending
 * 1 - Active
 * 2 - Closed
 * 3 - Blocked
 * */

class erLhcoreClassChat {
        
    /**
     * Gets pending chats
     */
    public static function getPendingChats($limit = 50, $offset = 0)
    {
         $db = ezcDbInstance::get();
            
         $currentUser = erLhcoreClassUser::instance();         
         $LimitationDepartament = '';
         $userData = $currentUser->getUserData(true);
         
         if ( $userData->all_departments == 0 )
         {
             $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());  
               
             if (count($userDepartaments) == 0) return array();
                      
             $LimitationDepartament = ' AND (lh_chat.dep_id IN ('.implode(',',$userDepartaments). ') OR user_id = '.$currentUser->getUserID() . ')';
         }
         
         $stmt = $db->prepare('SELECT lh_chat.*,lh_departament.name FROM lh_chat LEFT JOIN lh_departament ON lh_chat.dep_id = lh_departament.id WHERE status = 0'.$LimitationDepartament." ORDER BY lh_chat.id DESC LIMIT {$offset},{$limit}");   
               
         $stmt->setFetchMode(PDO::FETCH_ASSOC);        
         $stmt->execute();
         $rows = $stmt->fetchAll();
                
         return $rows;
    }

    public static function getPendingChatsCount()
    {
         $db = ezcDbInstance::get();
            
         $currentUser = erLhcoreClassUser::instance();         
         $userData = $currentUser->getUserData(true);
                  
         $LimitationDepartament = '';
         if ($userData->all_departments == 0)
         {
             $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());  
               
             if (count($userDepartaments) == 0) return array();
                      
             $LimitationDepartament = ' AND (lh_chat.dep_id IN ('.implode(',',$userDepartaments). ') OR user_id = '.$currentUser->getUserID() . ')';
         }
              
         $stmt = $db->prepare('SELECT count(lh_chat.id) as found FROM lh_chat LEFT JOIN lh_departament ON lh_chat.dep_id = lh_departament.id WHERE status = 0'.$LimitationDepartament);   
         $stmt->setFetchMode(PDO::FETCH_ASSOC);                    
         $stmt->execute();
         $rows = $stmt->fetchAll();
                
         return $rows[0]['found'];
    }  
    
    public static function getActiveChats($limit = 50, $offset = 0)
    {
         $db = ezcDbInstance::get();
         
         $currentUser = erLhcoreClassUser::instance();    
         $userData = $currentUser->getUserData(true);
         
         $LimitationDepartament = '';
         if ( $userData->all_departments == 0 )
         {
             $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());   
             
             if (count($userDepartaments) == 0) return array();
                       
             $LimitationDepartament = ' AND (lh_chat.dep_id IN ('.implode(',',$userDepartaments).') OR user_id = '.$currentUser->getUserID() . ')';
         }
         
         $stmt = $db->prepare('SELECT lh_chat.*,lh_departament.name FROM lh_chat LEFT JOIN lh_departament ON lh_chat.dep_id = lh_departament.id WHERE status = 1'.$LimitationDepartament." ORDER BY lh_chat.id DESC LIMIT {$offset},{$limit}");           
         $stmt->setFetchMode(PDO::FETCH_ASSOC);
         $stmt->execute();
         $rows = $stmt->fetchAll();
                        
         return $rows;
    } 
    
    public static function getActiveChatsCount()
    {
         $db = ezcDbInstance::get();
         
         $currentUser = erLhcoreClassUser::instance(); 
         $userData = $currentUser->getUserData(true);        
         $LimitationDepartament = '';
         if ( $userData->all_departments == 0 )
         {
             $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());   
             
             if (count($userDepartaments) == 0) return array();
                       
             $LimitationDepartament = ' AND (lh_chat.dep_id IN ('.implode(',',$userDepartaments).') OR user_id = '.$currentUser->getUserID() . ')';
         }
         
         $stmt = $db->prepare('SELECT count(lh_chat.id) AS found FROM lh_chat LEFT JOIN lh_departament ON lh_chat.dep_id = lh_departament.id WHERE status = 1'.$LimitationDepartament);           
         $stmt->setFetchMode(PDO::FETCH_ASSOC);
         $stmt->execute();
         $rows = $stmt->fetchAll();
                        
         return $rows[0]['found'];
    }    
    
    public static function getClosedChats($limit = 50, $offset = 0)
    {
         $db = ezcDbInstance::get();
         
         $currentUser = erLhcoreClassUser::instance();         
         $LimitationDepartament = '';
         $userData = $currentUser->getUserData(true);
                  
         if ( $userData->all_departments == 0 )
         {
             $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());   
             
             if (count($userDepartaments) == 0) return array();
                       
             $LimitationDepartament = ' AND (lh_chat.dep_id IN ('.implode(',',$userDepartaments).') OR user_id = '.$currentUser->getUserID() . ')';
         }
         
         $stmt = $db->prepare('SELECT lh_chat.*,lh_departament.name FROM lh_chat LEFT JOIN lh_departament ON lh_chat.dep_id = lh_departament.id WHERE status = 2'.$LimitationDepartament." ORDER BY lh_chat.id DESC LIMIT {$offset},{$limit} ");           
         
         $stmt->setFetchMode(PDO::FETCH_ASSOC);
         $stmt->execute();
         $rows = $stmt->fetchAll();
                
         return $rows;
    }
    
    public static function getClosedChatsCount()
    {
         $db = ezcDbInstance::get();
         
         $currentUser = erLhcoreClassUser::instance();         
         $LimitationDepartament = '';
         $userData = $currentUser->getUserData(true);
                   
         if ( $userData->all_departments == 0 )
         {
             $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());   
             
             if (count($userDepartaments) == 0) return array();
                       
             $LimitationDepartament = ' AND (lh_chat.dep_id IN ('.implode(',',$userDepartaments).') OR user_id = '.$currentUser->getUserID() . ')';
         }
              
         $stmt = $db->prepare('SELECT count(lh_chat.id) as found FROM lh_chat LEFT JOIN lh_departament ON lh_chat.dep_id = lh_departament.id WHERE status = 2'.$LimitationDepartament);           
         $stmt->execute();
         $rows = $stmt->fetchAll();
                
         return $rows[0]['found'];
    }
    
    
    public static function isOnline($dep_id = false)
    {
       $isOnlineUser = (int)erConfigClassLhConfig::getInstance()->getSetting('chat','online_timeout');
        
       $db = ezcDbInstance::get();
       
       if ($dep_id !== false){
           $stmt = $db->prepare('SELECT COUNT(id) AS found FROM lh_userdep WHERE (last_activity > :last_activity AND hide_online = 0) AND (dep_id = :dep_id OR dep_id = 0)');
           $stmt->bindValue(':dep_id',$dep_id);
           $stmt->bindValue(':last_activity',(time()-$isOnlineUser));
       } else {
           $stmt = $db->prepare('SELECT COUNT(id) AS found FROM lh_userdep WHERE last_activity > :last_activity AND hide_online = 0');
           $stmt->bindValue(':last_activity',(time()-$isOnlineUser));
       }
              
       $stmt->execute();
       $rows = $stmt->fetchAll();  
       
       return $rows[0]['found'] >= 1;
    }
    
    public static function getOnlineUsers($UserID = array())
    {
       $isOnlineUser = (int)erConfigClassLhConfig::getInstance()->getSetting('chat','online_timeout');
        
       $db = ezcDbInstance::get();
       $NotUser = '';
       
       if (count($UserID) > 0)
       {
           $NotUser = ' AND lh_users.id NOT IN ('.implode(',',$UserID).')';
       }
       
       $stmt = $db->prepare('SELECT lh_users.* FROM lh_users INNER JOIN lh_userdep ON lh_userdep.user_id = lh_users.id WHERE lh_userdep.last_activity > :last_activity '.$NotUser.' GROUP BY lh_users.id');
       $stmt->bindValue(':last_activity',(time()-$isOnlineUser));
           
       $stmt->execute();
       $rows = $stmt->fetchAll(); 
       return $rows;
    }
    
   /**
    * All messages, with should get user, with status Pending
    * 
    * */
   public static function getPendingUserMessages($chat_id)
   {
        $db = ezcDbInstance::get();
                        
        $stmt = $db->prepare('SELECT * FROM lh_msg WHERE chat_id = :chat_id AND user_id != 0 AND status = 0'); 
        $stmt->bindValue( ':chat_id',$chat_id);  
        $stmt->setFetchMode(PDO::FETCH_ASSOC);         
        $stmt->execute();
        $rows = $stmt->fetchAll(); 
                
        // Change messages status        
        $stmt = $db->prepare("UPDATE lh_msg SET status = 1 WHERE chat_id = :chat_id AND user_id != 0 AND status = 0"); 
        $stmt->bindValue( ':chat_id',$chat_id); 
        $stmt->execute();
               
        return $rows;
   }   
   
   /**
    * All messages, with should get administrator, with status Pending
    * 
    * */
   public static function getPendingAdminMessages($chat_id,$message_id)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg WHERE id > :message_id AND chat_id = :chat_id ORDER BY id ASC');
       $stmt->bindValue( ':chat_id',$chat_id);       
       $stmt->bindValue( ':message_id',$message_id);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);        
       $stmt->execute();
       $rows = $stmt->fetchAll();
              
       // Change messages status
       $stmt = $db->prepare("UPDATE lh_msg SET status = 1 WHERE user_id = 0 AND status = 0 AND chat_id = :chat_id");
       $stmt->bindValue( ':chat_id',$chat_id); 
       $stmt->execute();
               
       return $rows;
   }
   
   /**
    * Gets chats messages, used to review chat etc.
    * */
   public static function getChatMessages($chat_id)
   {
       
       $db = ezcDbInstance::get();         
       
       $stmt = $db->prepare('SELECT lh_msg.* FROM lh_msg WHERE status = 1 AND chat_id = :chat_id ORDER BY id ASC'); 
       $stmt->bindValue( ':chat_id',$chat_id);  
       $stmt->setFetchMode(PDO::FETCH_ASSOC);         
       $stmt->execute();
       $rows = $stmt->fetchAll();         
        
       return $rows;
   }
   
   
   public static function hasAccessToRead($chat)
   {
       $currentUser = erLhcoreClassUser::instance();
       
       $userData = $currentUser->getUserData(true);
                   
       if ( $userData->all_departments == 0 ) {      
            if ($chat->user_id == $currentUser->getUserID()) return true;
                          
            $userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());   
            
            if (count($userDepartaments) == 0) return false;
             
            if (in_array($chat->dep_id,$userDepartaments)) return true;
            
            return false;
       }
       
       return true;
   }
   
   /**
    * Is chat activated and user can send messages.
    * 
    * */ 
   public static function isChatActive($chat_id,$hash)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT COUNT(*) AS found FROM lh_chat WHERE id = :chat_id AND hash = :hash AND status = 1');
       $stmt->bindValue( ':chat_id',$chat_id); 
       $stmt->bindValue( ':hash',$hash); 
       
       $stmt->execute();
       $rows = $stmt->fetchAll();       
       return $rows[0]['found'] == 1;
   }
   
   public static function generateHash()
   {
       return sha1(mt_rand().time());
   }
   
   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {            
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhchat' )
            );
        }
        return self::$persistentSession;
   }
   
   private static $persistentSession;
}

?>