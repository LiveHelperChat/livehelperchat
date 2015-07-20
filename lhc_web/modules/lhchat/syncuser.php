<?php

$timeCurrent = time();
$pollingEnabled = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['long_polling_enabled'];
$pollingServerTimeout = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['connection_timeout'];
$pollingMessageTimeout = (float)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['polling_chat_message_sinterval'];

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    $chat->updateIgnoreColumns = array('last_msg_id');
    erLhcoreClassChat::setTimeZoneByChat($chat);
} catch (Exception $e) {
    $chat = false;
}

$content = 'false';
$status = 'true';
$blocked = 'false';
$ott = '';
$LastMessageID = 0;
$userOwner = 'true';
$checkStatus = 'f';
$breakSync = false;
$saveChat = false;
$operation = '';
$operatorId = 0;

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
       
    
	$db = ezcDbInstance::get();
	$db->beginTransaction();
	try {
		while (true) {
			
			// Auto responder
			if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && $chat->wait_timeout_send <= 0 && $chat->wait_timeout > 0 && !empty($chat->timeout_message) && (time() - $chat->time) > ($chat->wait_timeout*($chat->wait_timeout_repeat-(abs($chat->wait_timeout_send))))) {
				erLhcoreClassChatWorkflow::timeoutWorkflow($chat);
			}
		
			if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && $chat->transfer_if_na == 1 && $chat->transfer_timeout_ts < (time()-$chat->transfer_timeout_ac) ) {
		
				$canExecuteWorkflow = true;
		
				if (erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value >= 0) {
					if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
						$canExecuteWorkflow = erLhcoreClassChat::getPendingChatsCountPublic($chat->department->department_transfer_id) <= erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value;
					}
				}
		
				if ($canExecuteWorkflow == true) {
					erLhcoreClassChatWorkflow::transferWorkflow($chat);
				}
			}
		
			if ($chat->reinform_timeout > 0 && $chat->unread_messages_informed == 0 && $chat->has_unread_messages == 1 && (time()-$chat->last_user_msg_time) > $chat->reinform_timeout) {			
				$department = $chat->department;
				if ($department !== false) {
					$options = $department->inform_options_array;			
					erLhcoreClassChatWorkflow::unreadInformWorkflow(array('department' => $department,'options' => $options),$chat);				
				}			
			}
			
			// Sync only if chat is pending or active
			if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
				// Check for new messages only if chat last message id is greater than user last message id
				if ((int)$Params['user_parameters']['message_id'] < $chat->last_msg_id) {
				    $Messages = erLhcoreClassChat::getPendingMessages((int)$Params['user_parameters']['chat_id'],(int)$Params['user_parameters']['message_id']);
				    if (count($Messages) > 0)
				    {
				        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncuser.tpl.php');
				        $tpl->set('messages',$Messages);
				        $tpl->set('chat',$chat);
				        $tpl->set('sync_mode',isset($Params['user_parameters_unordered']['mode']) ? $Params['user_parameters_unordered']['mode'] : '');
				        $content = $tpl->fetch();
		
				        foreach ($Messages as $msg) {
				        	if ($msg['user_id'] > 0 || $msg['user_id'] == -2) {
				        		$userOwner = 'false';
				        		break;
				        	}
				        }
		
				        // Get first message opertor id
				        reset($Messages);
				        $firstNewMessage = current($Messages);
				        $operatorId = $firstNewMessage['user_id'];
				        
				        // Get Last message ID
				        end($Messages);
				        $LastMessageIDs = current($Messages);
				        $LastMessageID = $LastMessageIDs['id'];
				        
				        
				        
				        $breakSync = true;
				    }
				}
				
				if ( $chat->is_operator_typing == true && $Params['user_parameters_unordered']['ot'] != 't' ) {
					$ott = ($chat->operator_typing_user !== false) ? $chat->operator_typing_user->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','is typing now...') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...');
					$breakSync = true;
				}  elseif ($Params['user_parameters_unordered']['ot'] == 't' && $chat->is_operator_typing == false) {
					$breakSync = true;
					$ott = 'f';
				}
			}
			
		    // Closed
		    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
		        
		        $theme = false;
		        
		        if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
		            try {
		                $theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
		            } catch (Exception $e) {
		        
		            }
		        } else {
		            $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
		            if ($defaultTheme > 0) {
		                try {
		                    $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
		                } catch (Exception $e) {
		                     
		                }
		            }
		        }

		        if ($theme !== false && $theme->support_closed != ''){
		            $status = '<h4>'.htmlspecialchars($theme->support_closed).'</h4>';
		        } else {
		            $status = '<h4>'.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support staff member has closed this chat').'</h4>';
		        }
		        
		        $status .= '<input type="button" class="btn btn-default mb10" value="'.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close').'" onclick="lhinst.userclosedchatembed();" />';
		        
		    	$blocked = 'true';
		    	$breakSync = true;
		    }
	
		    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED) {
		    	$checkStatus = 't';
		    	$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
		    	$saveChat = true;
		    }
	
		    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM) {
		    	$checkStatus = 't';
		    }
	
		    if ($chat->operation != '') {	    	
		    	$operation = explode("\n", trim($chat->operation));
		    	$chat->operation = '';
		    	$saveChat = true;
		    }
		    
		    if ($chat->user_status != 0) {
		    	$chat->user_status = 0;
		    	$saveChat = true;
		    }
		    
		    
		    if ($saveChat === true) {
		    	$chat->updateThis();
		    }
		    
		    if ($pollingEnabled == false || $breakSync == true || ($pollingServerTimeout + $timeCurrent) < time() ) {	    	
		    	break;
		    } else {
		    	try {
		    		usleep($pollingMessageTimeout * 1000000);
		    		$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
		    	} catch (Exception $e) {
		    		break;
		    	}
		    }
		}
		$db->commit();
	} catch (Exception $e) {
	   $db->rollback();
	}  

} else {
    $content = 'false';
    $theme = false;
    
    if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
        try {
            $theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
        } catch (Exception $e) {
    
        }
    } else {
        $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
        if ($defaultTheme > 0) {
            try {
                $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
            } catch (Exception $e) {
                 
            }
        }
    }
    
    if ($theme !== false && $theme->support_closed != ''){
        $status = '<h4>'.htmlspecialchars($theme->support_closed).'</h4>';
    } else {
        $status = '<h4>'.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support staff member has closed this chat').'</h4>';
    }

    if ($Params['user_parameters_unordered']['modeembed'] == 'embed') {
        $status .= '<input type="button" class="btn btn-default mb10" value="'.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Close and Start a new one').'" onclick="lhinst.userclosedchatembed();" />';
    }
    
    $blocked = 'true';
}

echo json_encode(array('error' => 'false','op' => $operation, 'uw' => $userOwner, 'msop' => $operatorId, 'cs'=> $checkStatus, 'ott' => $ott, 'message_id' => $LastMessageID, 'result' => trim($content) == '' ? 'false' : trim($content), 'status' => $status, 'blocked' => $blocked ));
exit;

?>