<?php

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

$timeCurrent = time();
$pollingEnabled = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['long_polling_enabled'];
$pollingServerTimeout = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['connection_timeout'];
$pollingMessageTimeout = (float)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['polling_chat_message_sinterval'];


$content = 'false';
$status = 'true';
$blocked = 'false';
$ott = '';
$LastMessageID = 0;
$userOwner = 'true';
$breakSync = false;

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
	while (true) {
	// Check for new messages only if chat last message id is greater than user last message id
	if ((int)$Params['user_parameters']['message_id'] < $chat->last_msg_id) {
	    $Messages = erLhcoreClassChat::getPendingMessagesChatbox((int)$Params['user_parameters']['chat_id'],(int)$Params['user_parameters']['message_id']);
	    if ( ($Params['user_parameters']['message_id'] == 0 && count($Messages) > 0) || count($Messages) > 1)
	    {
	    	$prevNick = '';
	    	if ($Params['user_parameters']['message_id'] > 0 && count($Messages) > 1){
	    		$lastMessage = array_shift($Messages);
	    		if ($lastMessage['id'] == $Params['user_parameters']['message_id']){
	    			$prevNick = $lastMessage['user_id'] == 0 ? $lastMessage['name_support'] : $chat->nick;
	    		} else {
	    			array_unshift($Messages, $lastMessage);
	    		}
	    	}


	        $tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/syncuser.tpl.php');
	        $tpl->set('messages',$Messages);
	        $tpl->set('chat',$chat);
	        $tpl->set('prev_nick',$prevNick);
	        $tpl->set('sync_mode',isset($Params['user_parameters_unordered']['mode']) ? $Params['user_parameters_unordered']['mode'] : '');
	        $content = $tpl->fetch();

	        $visitorName = erLhcoreClassChatbox::getVisitorName();

	        foreach ($Messages as $msg) {
	        	if ($msg['name_support'] != $visitorName) {
	        		$userOwner = 'false';
	        		break;
	        	}
	        }

	        $LastMessageIDs = array_pop($Messages);
	        $LastMessageID = $LastMessageIDs['id'];
	        $breakSync = true;

	    }
	}

	    if ( $chat->is_operator_typing == true  && $Params['user_parameters_unordered']['ot'] != 't') {
	        $ott = ($chat->operator_typing_user !== false) ? $chat->operator_typing_user->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','is typing now...') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...');
	        $breakSync = true;
	    }  elseif ($Params['user_parameters_unordered']['ot'] == 't' && $chat->is_operator_typing == false) {
			$breakSync = true;
		}

	    // Closed
	    if ($chat->status == 2) {
	    	$status = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support has closed the chat window, but You can leave messages, and the administrator will read them later.');
	    	$breakSync = true;
	    	$blocked = 'true';
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
    $status = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','You do not have permission to view this chat, or the chat was deleted');
    $blocked = 'true';
}

echo json_encode(array('error' => 'false', 'op' => '', 'uw' => $userOwner, 'ott' => $ott, 'message_id' => $LastMessageID, 'result' => trim($content) == '' ? 'false' : trim($content), 'status' => $status, 'blocked' => $blocked ));
exit;

?>