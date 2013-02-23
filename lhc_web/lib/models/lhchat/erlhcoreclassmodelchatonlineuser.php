<?php

class erLhcoreClassModelChatOnlineUser {

   public function getState()
   {
       return array(
               'id'                 => $this->id,
               'ip'                 => $this->ip,
               'vid'                => $this->vid,
               'current_page'       => $this->current_page,
               'chat_id'            => $this->chat_id, // For future
               'last_visit'         => $this->last_visit,
               'user_agent'         => $this->user_agent,
               'user_country_name'  => $this->user_country_name,
               'user_country_code'  => $this->user_country_code
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

   public static function executeRequest($url) {
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5); 
        $content = curl_exec($ch);

        return $content;       
   }
   
   public static function getUserData($service, $ip, $params = array()) {
       
       if ($service == 'mod_geoip2') {
           
           if (isset($_SERVER[$params['country_code']]) && isset($_SERVER[$params['country_name']])){
               $normalizedObject = new stdClass();
               $normalizedObject->country_code = strtolower($_SERVER[$params['country_code']]);
               $normalizedObject->country_name = strtolower($_SERVER[$params['country_name']]);           
               return $normalizedObject;
           } else {
               return false;
           }

       } elseif ($service == 'freegeoip') {
           $response = self::executeRequest('http://freegeoip.net/json/'.$ip);
           if ( !empty($response) ) {
               $responseData = json_decode($response);
               if (is_object($responseData)) {
                   
                   $normalizedObject = new stdClass();
                   $normalizedObject->country_code = strtolower($responseData->country_code);
                   $normalizedObject->country_name = $responseData->country_name;
                   
                   return $normalizedObject;
               }
           }
           
           return false;
       } elseif ($service == 'locatorhq') {
           $response = self::executeRequest("http://api.locatorhq.com/?user={$params['username']}&key={$params['api_key']}&ip={$ip}&format=json");
                      
           if ( !empty($response) ) {               
               $responseData = json_decode($response);               
               if (is_object($responseData)) {
                   
                   $normalizedObject = new stdClass();
                   $normalizedObject->country_code = strtolower($responseData->countryCode);
                   $normalizedObject->country_name = $responseData->countryName;
                   
                   return $normalizedObject;
               }               
               return false;
           }
       }
       
       return false;
   }
   
   public static function detectLocation(erLhcoreClassModelChatOnlineUser & $instance) {
       $geoData = erLhcoreClassModelChatConfig::fetch('geo_data');
       $geo_data = (array)$geoData->data;
       
       if (isset($geo_data['geo_detection_enabled']) && $geo_data['geo_detection_enabled'] == 1) {
           
           $params = array();
                              
           if ($geo_data['geo_service_identifier'] == 'mod_geoip2'){
               $params['country_code'] = $geo_data['mod_geo_ip_country_code'];
               $params['country_name'] = $geo_data['mod_geo_ip_country_name'];
           } elseif ($geo_data['geo_service_identifier'] == 'locatorhq') {
               $params['username'] = $geo_data['locatorhqusername'];
               $params['api_key'] = $geo_data['locatorhq_api_key'];
           }
                      
           $location = self::getUserData($geo_data['geo_service_identifier'],$instance->ip,$params);
           
           if ($location !== false){
               $instance->user_country_code = $location->country_code;
               $instance->user_country_name = $location->country_name;
           }
       }
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
       
       // For DEBUG
       //$cookieData = print_r($_COOKIE,true);
       //$cookieData .= print_r($_SESSION,true);
       
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
                   
                   self::detectLocation($item);
                   
                   // Cleanup database then new user comes
                   self::cleanupOnlineUsers(); 
               }
           } else {
               $item = new erLhcoreClassModelChatOnlineUser();
               $item->ip = $_SERVER['REMOTE_ADDR'];
               $item->vid = erLhcoreClassModelForgotPassword::randomPassword(20);
               setcookie('lhc_vid',$item->vid,time() + (1 * 365 * 24 * 60 * 60),'/');
               
               self::detectLocation($item);
               
               // Cleanup database then new user comes
               self::cleanupOnlineUsers();              
           }

           $item->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
           $item->current_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
           // For DEBUG           
           //$item->current_page = $cookieData;
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
   public $user_country_name = '';
   public $user_country_code = '';

}

?>