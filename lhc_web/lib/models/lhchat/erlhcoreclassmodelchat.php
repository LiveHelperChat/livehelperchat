<?php

class erLhcoreClassModelChat {

   use erLhcoreClassDBTrait;
    
   public static $dbTable = 'lh_chat';
    
   public static $dbTableId = 'id';
   
   public static $dbSessionHandler = 'erLhcoreClassChat::getSession';
    
   public static $dbSortOrder = 'DESC';
    
   public function getState()
   {
       return array(
               'id'              		=> $this->id,
               'nick'            		=> $this->nick,
               'status'          		=> $this->status,
               'status_sub'          	=> $this->status_sub,
               'status_sub_arg'         => $this->status_sub_arg,
               'status_sub_sub'         => $this->status_sub_sub,
               'sender_user_id'         => $this->sender_user_id,
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
               'operator_typing_id' 	=> $this->operator_typing_id,
               'phone'           		=> $this->phone,
               'has_unread_messages'    => $this->has_unread_messages,
               'has_unread_op_messages' => $this->has_unread_op_messages,
               'last_user_msg_time'     => $this->last_user_msg_time,
               'last_op_msg_time'     	=> $this->last_op_msg_time,
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
               'user_tz_identifier'     => $this->user_tz_identifier,
               'user_closed_ts'     	=> $this->user_closed_ts,
               'lsync'     	            => $this->lsync,

       		   'online_user_id'     	=> $this->online_user_id,
       		   'unread_messages_informed' => $this->unread_messages_informed,
       		   'unread_op_messages_informed' => $this->unread_op_messages_informed,
       		   'reinform_timeout'     	=> $this->reinform_timeout,

               // Auto responder
               'auto_responder_id'      => $this->auto_responder_id,
           
       		    // Transfer workflow attributes
               'transfer_timeout_ts'    => $this->transfer_timeout_ts,
               'transfer_if_na'    		=> $this->transfer_if_na,
               'transfer_timeout_ac'    => $this->transfer_timeout_ac,
               'transfer_uid'           => $this->transfer_uid,

       			// Callback status
               'na_cb_executed'    		=> $this->na_cb_executed,
               'fbst'    				=> $this->fbst,
               'nc_cb_executed'    		=> $this->nc_cb_executed,
       		
       		    //
               'remarks'    			=> $this->remarks,
       		   // What operation is pending visitor?
               'operation'    			=> $this->operation,
       		
       		   // What operation is pending operator?
               'operation_admin'    	=> $this->operation_admin,
       		
       		   // Screenshot ID? maps to file
               'screenshot_id'    		=> $this->screenshot_id,
       		
               'tslasign'    			=> $this->tslasign,
           
               // Operator status while he was accepting chat
               'usaccept'    			=> $this->usaccept,
           
               // Visitor language
               'chat_locale'    		=> $this->chat_locale,
           
               // Operator language
               'chat_locale_to'    		=> $this->chat_locale_to,
           
               // Was chat unanswered before user has left a chat
               // Currently there isn’t a statistic that shows the number of users that has left the chat before operator has accepted the chat.
               'unanswered_chat'    	=> $this->unanswered_chat,
           
               // Product ID
               'product_id'    	        => $this->product_id,
       		
               'uagent'    	        	=> $this->uagent,
               'device_type'    	    => $this->device_type,
       );
   }

   public function beforeRemove()
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
        
       // Delete screen sharing
       $q->deleteFrom( 'lh_cobrowse' )->where( $q->expr->eq( 'chat_id', $this->id ) );
       $stmt = $q->prepare();
       $stmt->execute();
        
       // Delete speech settings
       $q->deleteFrom( 'lh_speech_chat_language' )->where( $q->expr->eq( 'chat_id', $this->id ) );
       $stmt = $q->prepare();
       $stmt->execute();
        
       // Survey
       $q->deleteFrom( 'lh_abstract_survey_item' )->where( $q->expr->eq( 'chat_id', $this->id ) );
       $stmt = $q->prepare();
       $stmt->execute();
        
       // Paid chats
       $q->deleteFrom( 'lh_chat_paid' )->where( $q->expr->eq( 'chat_id', $this->id ) );
       $stmt = $q->prepare();
       $stmt->execute();
       
       erLhcoreClassModelChatFile::deleteByChatId($this->id);
   }

   public function afterRemove()
   {
       erLhcoreClassChat::updateActiveChats($this->user_id);
        
       if ($this->department !== false) {
           erLhcoreClassChat::updateDepartmentStats($this->department);
       }
   }
   
   public function setIP()
   {
       $this->ip = erLhcoreClassIPDetect::getIP();
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

       	case 'time_created_front':
       			$this->time_created_front = date('Ymd') == date('Ymd',$this->time) ? date(erLhcoreClassModule::$dateHourFormat,$this->time) : date(erLhcoreClassModule::$dateDateHourFormat,$this->time);
       			return $this->time_created_front;
       		break;

       	case 'user_closed_ts_front':
       			$this->user_closed_ts_front = date('Ymd') == date('Ymd',$this->user_closed_ts) ? date(erLhcoreClassModule::$dateHourFormat,$this->user_closed_ts) : date(erLhcoreClassModule::$dateDateHourFormat,$this->user_closed_ts);
       			return $this->user_closed_ts_front;
       		break;
       	
       	case 'is_operator_typing':
       		   $this->is_operator_typing = $this->operator_typing > (time()-60); // typing is considered if status did not changed for 30 seconds
       		   return $this->is_operator_typing;
       		break;

       	case 'is_user_typing':
       		   $this->is_user_typing = $this->user_typing > (time()-10); // typing is considered if status did not changed for 30 seconds
       		   return $this->is_user_typing;
       		break;

       	case 'wait_time_seconds':
       		   $this->wait_time_seconds = time() - $this->time;
       		   return $this->wait_time_seconds;

       	case 'wait_time_front':
       		   $this->wait_time_front = erLhcoreClassChat::formatSeconds($this->wait_time);
       		   return $this->wait_time_front;
       		break;

       	case 'wait_time_pending':
       		   $this->wait_time_pending = erLhcoreClassChat::formatSeconds(time() - $this->time);
       		   return $this->wait_time_pending;
       		break;

       	case 'chat_duration_front':
       		   $this->chat_duration_front = erLhcoreClassChat::formatSeconds($this->chat_duration);
       		   return $this->chat_duration_front;
       		break;

       	case 'user_name':
       			return $this->user_name = (string)$this->user;
       		break;	

       	case 'plain_user_name':
       	        $this->plain_user_name = false;
       	        
       	        if ($this->user !== false) {
       	            $this->plain_user_name = (string)$this->user->name_support;
       	        }
       	        
       			return $this->plain_user_name;
       		break;	
       		
       	case 'user':
       		   $this->user = false;
       		   if ($this->user_id > 0) {
       		   		try {
       		   			$this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
       		   		} catch (Exception $e) {
       		   			$this->user = false;
       		   		}
       		   }
       		   return $this->user;
       		break;
       	    	
       	case 'operator_typing_user':
       		   $this->operator_typing_user = false;
       		   if ($this->operator_typing_id > 0) {
       		   		try {
       		   			$this->operator_typing_user = erLhcoreClassModelUser::fetch($this->operator_typing_id);
       		   		} catch (Exception $e) {
       		   			$this->operator_typing_user = false;
       		   		}
       		   }
       		   return $this->operator_typing_user;
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
       		
       	case 'auto_responder':
           	    $this->auto_responder = false;
           	    if ($this->auto_responder_id > 0) {
           	        try {
           	            $this->auto_responder = erLhAbstractModelAutoResponderChat::fetch($this->auto_responder_id);
           	        } catch (Exception $e) {
           	    
           	        }
           	    }
           	    return $this->auto_responder;
       	    break;

       	case 'product':
       			$this->product = false;
       			if ($this->product_id > 0) {
       				try {
       					$this->product = erLhAbstractModelProduct::fetch($this->product_id,true);
       				} catch (Exception $e) {
                        
       				}
       			}
       			return $this->product;
       		break;

       	case 'product_name':
       			$this->product_name = (string)$this->product;
       			return $this->product_name;
       		break;

       	case 'department_name':
       			return $this->department_name = (string)$this->department;
       		break;
       		
       	case 'number_in_queue':
       	        $this->number_in_queue = 1;
       	        if ($this->status == self::STATUS_PENDING_CHAT) {
       	           $this->number_in_queue = erLhcoreClassChat::getCount(array('filterlt' => array('id' => $this->id),'filter' => array('dep_id' => $this->dep_id,'status' => self::STATUS_PENDING_CHAT))) + 1;
       	        }
       	        return $this->number_in_queue;
       	    break;
       	    	
       	case 'screenshot':
       			$this->screenshot = false;
       			if ($this->screenshot_id > 0) {
       				try {
       					$this->screenshot = erLhcoreClassModelChatFile::fetch($this->screenshot_id);
       				} catch (Exception $e) {
       			
       				}
       			}
       			
       			return $this->screenshot;
       		break;	
       		
       	case 'unread_time':
       		
	       		$diff = time()-$this->last_user_msg_time;
	       		$hours = floor($diff/3600);
	       		$minits = floor(($diff - ($hours * 3600))/60);
	       		$seconds = ($diff - ($hours * 3600) - ($minits * 60));
	       		
       			$this->unread_time = array(
       				'hours' => $hours,
       				'minits' => $minits,
       				'seconds' => $seconds,
       			); 
       			 
       			return $this->unread_time;
       		break;
       		
       	case 'user_tz_identifier_time':
       			$date = new DateTime(null, new DateTimeZone($this->user_tz_identifier));
       			$this->user_tz_identifier_time = $date->format(erLhcoreClassModule::$dateHourFormat);       			
       			return $this->user_tz_identifier_time;
       		break;
       		
       	case 'additional_data_array':
       			$jsonData = json_decode($this->additional_data);
       			if ($jsonData !== null) {
       				$this->additional_data_array = $jsonData;
       			} else {
       				$this->additional_data_array = $this->additional_data;
       			}
       			return $this->additional_data_array;
       		break;
       		
       	case 'chat_variables_array':
       	        if (!empty($this->chat_variables)){
           			$jsonData = json_decode($this->chat_variables,true);
           			if ($jsonData !== null) {
           				$this->chat_variables_array = $jsonData;
           			} else {
           				$this->chat_variables_array = $this->chat_variables;
           			}
       	        } else {
       	            $this->chat_variables_array = $this->chat_variables;
       	        }
       			return $this->chat_variables_array;
       		break;
       		
       	case 'user_status_front':

       	    if ($this->lsync > 0) {

       	        // Because mobile devices freezes background tabs we need to have bigger timeout
       	        $timeout = 60;

       	        if ($this->device_type != 0 && (strpos($this->uagent,'iPhone') !== false || strpos($this->uagent,'iPad') !== false)) {
                    $timeout = 240;
                }

       	        $this->user_status_front =  (time() - $timeout > $this->lsync || in_array($this->status_sub,array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT))) ? 1 : 0;

       	    } elseif ($this->online_user !== false) {
       		    $this->user_status_front = erLhcoreClassChat::setActivityByChatAndOnlineUser($this, $this->online_user);
       		} else {
       		    $this->user_status_front = $this->user_status == self::USER_STATUS_JOINED_CHAT ? 0 : 1;
       		}

       		return $this->user_status_front;
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
               $params['mod_geo_ip_city_name'] = $geo_data['mod_geo_ip_city_name'];
               $params['mod_geo_ip_latitude'] = $geo_data['mod_geo_ip_latitude'];
               $params['mod_geo_ip_longitude'] = $geo_data['mod_geo_ip_longitude'];
           } elseif ($geo_data['geo_service_identifier'] == 'locatorhq') {
               $params['username'] = $geo_data['locatorhqusername'];
               $params['api_key'] = $geo_data['locatorhq_api_key'];
           } elseif ($geo_data['geo_service_identifier'] == 'ipinfodbcom') {             
               $params['api_key'] = $geo_data['ipinfodbcom_api_key'];
           } elseif ($geo_data['geo_service_identifier'] == 'max_mind') {             
               $params['detection_type'] = $geo_data['max_mind_detection_type'];         
               $params['city_file'] = isset($geo_data['max_mind_city_location']) ? $geo_data['max_mind_city_location'] : '';
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

   const STATUS_SUB_DEFAULT = 0;
   const STATUS_SUB_OWNER_CHANGED = 1;
   const STATUS_SUB_CONTACT_FORM = 2;
   const STATUS_SUB_USER_CLOSED_CHAT = 3;
   const STATUS_SUB_START_ON_KEY_UP = 4;
   const STATUS_SUB_SURVEY_SHOW = 5;
   const STATUS_SUB_SURVEY_COLLECTED = 6;
   const STATUS_SUB_OFFLINE_REQUEST = 7;
   const STATUS_SUB_ON_HOLD = 8;

   const STATUS_SUB_SUB_DEFAULT = 0;
   const STATUS_SUB_SUB_TRANSFERED = 1;
      
   const USER_STATUS_JOINED_CHAT = 0;
   const USER_STATUS_CLOSED_CHAT = 1;
   const USER_STATUS_PENDING_REOPEN = 2;
   
   const OP_STATUS_ACCEPT_ONLINE = 0;
   const OP_STATUS_ACCEPT_OFFLINE = 1;
   
   public $id = null;
   public $nick = '';
   
   // General chat statusses
   public $status = self::STATUS_PENDING_CHAT;
   
   // Used for visitors
   public $status_sub = self::STATUS_SUB_DEFAULT;
   
   // Used for operators
   public $status_sub_sub = self::STATUS_SUB_SUB_DEFAULT;
   
   public $time = '';
   public $user_id = 0;
   public $sender_user_id = 0;
   public $hash = '';
   public $ip = '';
   public $referrer = '';
   public $dep_id = '';
   public $email = '';
   public $user_status = self::USER_STATUS_JOINED_CHAT;
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
   public $lsync = 0;

   // Transfer attributes
   public $transfer_if_na = 0;
   public $transfer_timeout_ts = 0;
   public $transfer_timeout_ac = 0;
   public $transfer_uid = 0;

   // Wait timeout attributes
   //public $wait_timeout = 0;
   //public $wait_timeout_send = 0;
   //public $timeout_message = '';
   //public $wait_timeout_repeat = 0;
   
   public $auto_responder_id = 0;
   
   // User timezone identifier
   public $user_tz_identifier = '';

   // Unanswered chat callback executed
   public $na_cb_executed = 0;
   
   // New chat callback executed
   public $nc_cb_executed = 0;

   // Feedback status
   public $fbst = 0;
   
   // What operator is typing now.
   public $operator_typing_id = 0;

   public $chat_initiator = self::CHAT_INITIATOR_DEFAULT;
   public $chat_variables = '';
   
   public $remarks = '';
   
   // Pending operations from user side
   public $operation = '';
   
   public $operation_admin = '';
   
   public $screenshot_id = 0;
   
   public $unread_messages_informed = 0;
   
   public $reinform_timeout = 0;
   
   // Last operator message time
   public $last_op_msg_time = 0;
   
   // Does chat has unread messages from operator
   public $has_unread_op_messages = 0; 
   
   // Was visitor informed about unread message
   public $unread_op_messages_informed = 0;
   
   // Time when user closed a chat window
   public $user_closed_ts = 0;
   
   public $unanswered_chat = 0;
   
   public $product_id = 0;
   
   public $usaccept = self::OP_STATUS_ACCEPT_ONLINE;
   
   public $status_sub_arg = '';
   
   // Time since last assignment
   public $tslasign = 0;
   
   public $chat_locale = '';
   
   public $chat_locale_to = '';
   
   public $uagent = '';
   
   // 0 - PC, 1 - mobile, 2 - tablet
   public $device_type = 0;

   public $updateIgnoreColumns = array();
}

?>