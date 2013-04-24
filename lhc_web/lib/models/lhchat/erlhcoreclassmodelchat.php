<?php

class erLhcoreClassModelChat {

   public function getState()
   {
       return array(
               'id'              		=> $this->id,
               'nick'            		=> $this->nick,
               'status'          		=> $this->status,
               'time'            		=> $this->time,
               'user_id'         		=> $this->user_id,
               'hash'            		=> $this->hash,
               'ip'              		=> $this->ip,
               'referrer'        		=> $this->referrer,
               'dep_id'          		=> $this->dep_id,
               'email'           		=> $this->email,
               'user_status'     		=> $this->user_status,
               'support_informed'		=> $this->support_informed,
               'country_code'    		=> $this->country_code,
               'country_name'    		=> $this->country_name,
               'user_typing'     		=> $this->user_typing,
               'operator_typing' 		=> $this->operator_typing,
               'phone'           		=> $this->phone,
               'has_unread_messages'    => $this->has_unread_messages,
               'last_user_msg_time'     => $this->last_user_msg_time,
               'last_msg_id'     		=> $this->last_msg_id,
               'mail_send'     			=> $this->mail_send,
               'additional_data'     	=> $this->additional_data
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

   public function saveThis() {
       	 erLhcoreClassChat::getSession()->saveOrUpdate($this);
   }

   public function updateThis() {
       	 erLhcoreClassChat::getSession()->update($this);
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

   public function __get($var) {

       switch ($var) {

       	case 'is_operator_typing':
       		   $this->is_operator_typing = $this->operator_typing > (time()-6); // typing is considered if status did not changed for 10 seconds
       		   return $this->is_operator_typing;
       		break;

       	case 'is_user_typing':
       		   $this->is_user_typing = $this->user_typing > (time()-6); // typing is considered if status did not changed for 10 seconds
       		   return $this->is_user_typing;
       		break;

       	case 'department':
       			$this->department = false;
       			if ($this->dep_id > 0) {
       				try {
       					$this->department = erLhcoreClassModelDepartament::fetch($this->dep_id,true);
       				} catch (Exception $e) {

       				}
       			}

       			return $this->department;
       		break;

       	default:
       		break;
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
   public $user_status = 0;
   public $support_informed = '';
   public $country_code = '';
   public $country_name = '';
   public $phone = '';
   public $user_typing = 0;
   public $operator_typing = 0;
   public $has_unread_messages = 0;
   public $last_user_msg_time = 0;
   public $last_msg_id = 0;
   public $mail_send = 0;
   public $additional_data = '';

}

?>