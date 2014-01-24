<?php

$timeCurrent = time();
$pollingEnabled = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['long_polling_enabled'];
$pollingServerTimeout = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['connection_timeout'];
$pollingMessageTimeout = (float)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['polling_chat_message_sinterval'];

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
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

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
	
	while (true) {
		
		// Auto responder
		if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && $chat->wait_timeout_send == 0 && $chat->wait_timeout > 0 && !empty($chat->timeout_message) && (time() - $chat->time) > $chat->wait_timeout) {
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
			        	if ($msg['user_id'] > 0) {
			        		$userOwner = 'false';
			        		break;
			        	}
			        }
	
			        $LastMessageIDs = array_pop($Messages);
			        $LastMessageID = $LastMessageIDs['id'];
			        $breakSync = true;
			    }
			}
			
			if ( $chat->is_operator_typing == true && $Params['user_parameters_unordered']['ot'] != 't' ) {
				$ott = ($chat->operator_typing_user !== false) ? $chat->operator_typing_user->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','is typing now...') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...');
				$breakSync = true;
			}  elseif ($Params['user_parameters_unordered']['ot'] == 't' && $chat->is_operator_typing == false) {
				$breakSync = true;
			}
			
		}
		
	    // Closed
	    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
	    	$status = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support staff member has closed this chat');
	    	$blocked = 'true';
	    	$breakSync = true;
	    }

	    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED) {
	    	$checkStatus = 't';
	    	$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
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

} else {
    $content = 'false';
    $status = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','You do not have permission to view this chat, or chat was deleted');
    $blocked = 'true';
}

echo json_encode(array('error' => 'false', 'uw' => $userOwner, 'cs'=> $checkStatus, 'ott' => $ott, 'message_id' => $LastMessageID, 'result' => trim($content) == '' ? 'false' : trim($content), 'status' => $status, 'blocked' => $blocked ));
exit;

?>