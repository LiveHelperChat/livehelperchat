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
               'user_typing_txt'     	=> $this->user_typing_txt,
               'operator_typing' 		=> $this->operator_typing,
               'phone'           		=> $this->phone,
               'has_unread_messages'    => $this->has_unread_messages,
               'last_user_msg_time'     => $this->last_user_msg_time,
               'last_msg_id'     		=> $this->last_msg_id,
               'mail_send'     			=> $this->mail_send,
               'lat'     				=> $this->lat,
               'lon'     				=> $this->lon,
               'city'     				=> $this->city,
               'additional_data'     	=> $this->additional_data,
               'session_referrer'     	=> $this->session_referrer,
               'wait_time'     			=> $this->wait_time,
               'chat_duration'     		=> $this->chat_duration,
               'chat_variables'     	=> $this->chat_variables,
               'priority'     			=> $this->priority,
               'chat_initiator'     	=> $this->chat_initiator,

       		   'online_user_id'     	=> $this->online_user_id,

       		   // Wait timeout attribute
               'wait_timeout'     		=> $this->wait_timeout,
               'wait_timeout_send'     	=> $this->wait_timeout_send,
               'timeout_message'     	=> $this->timeout_message,

       		    // Transfer workflow attributes
               'transfer_timeout_ts'    => $this->transfer_timeout_ts,
               'transfer_if_na'    		=> $this->transfer_if_na,
               'transfer_timeout_ac'    => $this->transfer_timeout_ac,

       			// Callback status
               'na_cb_executed'    		=> $this->na_cb_executed
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public function removeThis()
   {
	   	$q = ezcDbInstance::get()->createDeleteQuery();

	   	// Messages
	   	$q->deleteFrom( 'lh_msg' )->where( $q->expr->eq( 'chat_id', $this->id ) );
	   	$stmt = $q->prepare();
	   	$stmt->execute();

	   	// Transfered chats
	   	$q->deleteFrom( 'lh_transfer' )->where( $q->expr->eq( 'chat_id', $this->id ) );
	   	$stmt = $q->prepare();
	   	$stmt->execute();

	   	// Delete user footprint
	   	$q->deleteFrom( 'lh_chat_online_user_footprint' )->where( $q->expr->eq( 'chat_id', $this->id ) );
	   	$stmt = $q->prepare();
	   	$stmt->execute();

	   	erLhcoreClassChat::getSession()->delete($this);
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
       		   $this->is_operator_typing = $this->operator_typing > (time()-10); // typing is considered if status did not changed for 30 seconds
       		   return $this->is_operator_typing;
       		break;

       	case 'is_user_typing':
       		   $this->is_user_typing = $this->user_typing > (time()-10); // typing is considered if status did not changed for 30 seconds
       		   return $this->is_user_typing;
       		break;

       	case 'wait_time_front':
       		   $this->wait_time_front = erLhcoreClassChat::formatSeconds($this->wait_time);
       		   return $this->wait_time_front;
       		break;

       	case 'chat_duration_front':
       		   $this->chat_duration_front = erLhcoreClassChat::formatSeconds($this->chat_duration);
       		   return $this->chat_duration_front;
       		break;

       	case 'user':
       		   $this->user = false;
       		   if ($this->user_id > 0) {
       		   		try {
       		   			$this->user = erLhcoreClassModelUser::fetch($this->user_id);
       		   		} catch (Exception $e) {
       		   			$this->user = false;
       		   		}
       		   }
       		   return $this->user;
       		break;

       	case 'online_user':
       			$this->online_user = false;
       			if ($this->online_user_id > 0){
       				try {
       					$this->online_user = erLhcoreClassModelChatOnlineUser::fetch($this->online_user_id);
       				} catch (Exception $e) {
       					$this->online_user = false;
       				}
       			}
       			return $this->online_user;
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
               $instance->lat = $location->lat;
               $instance->lon = $location->lon;
               $instance->city = $location->city;
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

   const STATUS_PENDING_CHAT = 0;
   const STATUS_ACTIVE_CHAT = 1;
   const STATUS_CLOSED_CHAT = 2;
   const STATUS_CHATBOX_CHAT = 3;
   const STATUS_OPERATORS_CHAT = 4;

   const CHAT_INITIATOR_DEFAULT = 0;
   const CHAT_INITIATOR_PROACTIVE = 1;

   public $id = null;
   public $nick = '';
   public $status = self::STATUS_PENDING_CHAT;
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
   public $user_typing_txt = '';
   public $operator_typing = 0;
   public $has_unread_messages = 0;
   public $last_user_msg_time = 0;
   public $last_msg_id = 0;
   public $mail_send = 0;
   public $lat = 0;
   public $lon = 0;
   public $city = '';
   public $additional_data = '';
   public $session_referrer = '';
   public $wait_time = 0;
   public $chat_duration = 0;
   public $priority = 0;
   public $online_user_id = 0;

   // Transfer attributes
   public $transfer_if_na = 0;
   public $transfer_timeout_ts = 0;
   public $transfer_timeout_ac = 0;

   // Wait timeout attributes
   public $wait_timeout = 0;
   public $wait_timeout_send = 0;
   public $timeout_message = '';


   public $na_cb_executed = 0;




   public $chat_initiator = self::CHAT_INITIATOR_DEFAULT;
   public $chat_variables = '';

}

?>