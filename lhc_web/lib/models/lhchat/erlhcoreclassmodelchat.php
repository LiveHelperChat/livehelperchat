<?php

class erLhcoreClassModelChat {
        
   public function getState()
   {
       return array(
               'id'              => $this->id,
               'nick'            => $this->nick,
               'status'          => $this->status,
               'time'            => $this->time,
               'user_id'         => $this->user_id,
               'hash'            => $this->hash,
               'ip'              => $this->ip,
               'referrer'        => $this->referrer,
               'dep_id'          => $this->dep_id,
               'email'           => $this->email,
               'user_status'     => $this->user_status,
               'support_informed'=> $this->support_informed,
               'country_code'    => $this->country_code,
               'country_name'    => $this->country_name,
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
       	 $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', (int)$chat_id );
       	 return $chat;
   }
   
   public function setIP()
   {
       $this->ip = $_SERVER['REMOTE_ADDR'];
   }
   
   public function getChatOwner()
   {
       try {
           $user = erLhcoreClassUser::getSession()->load('erLhcoreClassModelUser', $this->user_id);
           return $user;
       } catch (Exception $e) {
           return false;
       }
   }  
   
   public static function detectLocation(erLhcoreClassModelChat & $instance)
   {
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
                      
           $location = erLhcoreClassModelChatOnlineUser::getUserData($geo_data['geo_service_identifier'],$instance->ip,$params);
           
           if ($location !== false){
               $instance->country_code = $location->country_code;
               $instance->country_name = $location->country_name;
           }
       }
   }
   
   public function blockUser() {
       
       if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => $this->ip))) == 0)
       {
           $block = new erLhcoreClassModelChatBlockedUser();
           $block->ip = $this->ip;       
           $block->user_id = erLhcoreClassUser::instance()->getUserID();
           $block->saveThis();
       }
   }

   public $id = null;
   public $nick = '';
   public $status = 0;
   public $time = '';
   public $user_id = '';
   public $hash = '';
   public $ip = '';
   public $referrer = '';
   public $dep_id = '';
   public $email = '';
   public $user_status = '';
   public $support_informed = '';
   public $country_code = '';
   public $country_name = '';
}

?>