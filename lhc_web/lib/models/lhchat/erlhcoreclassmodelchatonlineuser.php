<?php

class erLhcoreClassModelChatOnlineUser {

   public function getState()
   {
       return array(
               'id'             => $this->id,
               'ip'             => $this->ip,
               'vid'            => $this->vid,
               'current_page'   => $this->current_page,
               'chat_id'        => $this->chat_id, // For future
               'last_visit'     => $this->last_visit,
               'user_agent'     => $this->user_agent,
               'user_location'  => $this->user_location // For future
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   } 

   public static function fetch($chat_id) {
       	 $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatOnlineUser', (int)$chat_id );
       	 return $chat;
   }
   
   public function removeThis() {
       erLhcoreClassChat::getSession()->delete($this);
   }
   
   public function __get($var) {
       switch ($var) {
       	case 'last_visit_front':
       		  return date('Y-m-d H:i:s',$this->last_visit);
       		break;
       		
       	case 'lastactivity_ago':
       		   $this->lastactivity_ago = '';
       		   
       		   if ( $this->last_visit > 0 ) {
       		               		       
                    $periods         = array("s.", "m.", "h.", "d.", "w.", "m.", "y.", "dec.");
                    $lengths         = array("60","60","24","7","4.35","12","10");
                                                                                                 
                    $difference     = time() - $this->last_visit;
                                         
                    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
                        $difference /= $lengths[$j];
                    }
                 
                    $difference = round($difference);
                    
                   $this->lastactivity_ago = "$difference $periods[$j]";       		       
       		   };
       		   
       		   return $this->lastactivity_ago;
       		break;
       		       
       	default:
       		break;
       }
   }
   
   public static function getCount($params = array())
   {
       $session = erLhcoreClassChat::getSession();
       $q = $session->database->createSelectQuery();  
       $q->select( "COUNT(id)" )->from( "lh_chat_online_user" );   
         
       if (isset($params['filter']) && count($params['filter']) > 0)
       {
           $conditions = array();
           
           foreach ($params['filter'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
           }
           
           $q->where( 
                 $conditions   
           );
      }  
             
      $stmt = $q->prepare();       
      $stmt->execute();  
      $result = $stmt->fetchColumn(); 
            
      return $result; 
   }
   
   public static function getList($paramsSearch = array())
   {             
       $paramsDefault = array('limit' => 32, 'offset' => 0);
       
       $params = array_merge($paramsDefault,$paramsSearch);
       
       $session = erLhcoreClassChat::getSession();
       $q = $session->createFindQuery( 'erLhcoreClassModelChatOnlineUser' );  
       
       $conditions = array(); 
                    
      if (isset($params['filter']) && count($params['filter']) > 0)
      {                     
           foreach ($params['filter'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
           }
      } 
      
      if (isset($params['filterin']) && count($params['filterin']) > 0)
      {
           foreach ($params['filterin'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->in( $field, $fieldValue );
           } 
      }
      
      if (isset($params['filterlt']) && count($params['filterlt']) > 0)
      {
           foreach ($params['filterlt'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->lt( $field, $q->bindValue($fieldValue) );
           } 
      }
      
      if (isset($params['filtergt']) && count($params['filtergt']) > 0)
      {
           foreach ($params['filtergt'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->gt( $field,$q->bindValue( $fieldValue ));
           } 
      }      
      
      if (count($conditions) > 0)
      {
          $q->where( 
                     $conditions   
          );
      } 
      
      $q->limit($params['limit'],$params['offset']);
                
      $q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' ); 
              
      $objects = $session->find( $q );
         
      return $objects; 
   }

   public static function cleanupOnlineUsers() {
         $db = ezcDbInstance::get();
         $stmt = $db->prepare('DELETE FROM lh_chat_online_user WHERE last_visit < :last_activity');   
         $stmt->bindValue(':last_activity',(int)(time()-erLhcoreClassModelChatConfig::fetch('tracked_users_cleanup')->current_value * 24*3600)); 
         $stmt->execute();
   }
   
   public static function cleanAllRecords() {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('DELETE FROM lh_chat_online_user');   
       $stmt->execute();
   }
   
   public static function handleRequest() {      
       // Track only not logged users 
       if ( erLhcoreClassUser::instance()->isLogged() == false )
       {
           if ( isset($_COOKIE['lhc_vid']) ) {
               $items = erLhcoreClassModelChatOnlineUser::getList(array('filter' => array('vid' => $_COOKIE['lhc_vid'])));
               if (!empty($items)) {
                   $item = array_shift($items);
               } else {
                   $item = new erLhcoreClassModelChatOnlineUser();
                   $item->ip = $_SERVER['REMOTE_ADDR'];
                   $item->vid = erLhcoreClassModelForgotPassword::randomPassword(20);
                   setcookie('lhc_vid',$item->vid,time() + (1 * 365 * 24 * 60 * 60),'/');
               }
           } else {
               $item = new erLhcoreClassModelChatOnlineUser();
               $item->ip = $_SERVER['REMOTE_ADDR'];
               $item->vid = erLhcoreClassModelForgotPassword::randomPassword(20);
               setcookie('lhc_vid',$item->vid,time() + (1 * 365 * 24 * 60 * 60),'/');
               
               // Cleanup database then new user comes
               self::cleanupOnlineUsers();              
           }

           $item->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
           $item->current_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
           $item->last_visit = time();   
           $item->saveThis();           
       }       
   }

   public function saveThis() {
        erLhcoreClassChat::getSession()->saveOrUpdate( $this );
   }

   public $id = null;
   public $ip = '';
   public $vid = '';
   public $current_page = '';
   public $user_agent = '';
   public $chat_id = 0;
   public $last_visit = 0;
   public $user_location = '';

}

?>