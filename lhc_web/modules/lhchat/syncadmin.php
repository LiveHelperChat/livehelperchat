<?php

// We do not need a session anymore
session_write_close();

$timeCurrent = time();
$pollingEnabled = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['long_polling_enabled'];
$pollingServerTimeout = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['connection_timeout'];
$pollingMessageTimeout = (float)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['polling_chat_message_sinterval'];
$breakSync = false;

$typingChats = (isset($_POST['typing']) && is_array($_POST['typing'])) ? $_POST['typing'] : array();
$typingChatsTl = (isset($_POST['typingtl']) && is_array($_POST['typingtl'])) ? $_POST['typingtl'] : array();
$typingChatsCombined = array_combine($typingChats, $typingChatsTl);

$content = 'false';
$content_status = 'false';
$userOwner = 'true';


$hasAccessToReadArray = array();

if (isset($_POST['chats']) && is_array($_POST['chats']) && count($_POST['chats']) > 0)
{
    $ReturnMessages = array();
    $ReturnStatuses = array();

    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncadmin.tpl.php');
    $currentUser = erLhcoreClassUser::instance();

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
    	exit;
    }
    
    while (true) {    	
	    foreach ($_POST['chats'] as $chat_id_list)
	    {
	        list($chat_id,$MessageID) = explode(',',$chat_id_list);
	
	        $Chat = erLhcoreClassModelChat::fetch($chat_id);
	
	        if ( isset($hasAccessToReadArray[$chat_id]) || erLhcoreClassChat::hasAccessToRead($Chat) )
	        {
	        	$hasAccessToReadArray[$chat_id] = true;
	        	
	            if ( ($Chat->last_msg_id > (int)$MessageID) && count($Messages = erLhcoreClassChat::getPendingMessages($chat_id,$MessageID)) > 0)
	            {
	            	// If chat had flag that it contains unread messages set to 0
	            	if ( $Chat->has_unread_messages == 1 ) {
	            		 $Chat->has_unread_messages = 0;
	            		 $Chat->saveThis();
	            	}
	
	            	$newMessagesNumber = count($Messages);
	
	                $tpl->set('messages',$Messages);
	                $tpl->set('chat',$Chat);
	
	                if ($userOwner == 'true') {
	                	foreach ($Messages as $msg) {
	                		if ($msg['user_id'] != $currentUser->getUserID()) {
	                			$userOwner = 'false';
	                			break;
	                		}
	                	}
	                }
	
	                $LastMessageIDs = array_pop($Messages);
	
	                $templateResult = $tpl->fetch();
	
	                $ReturnMessages[] = array('chat_id' => $chat_id, 'mn' => $newMessagesNumber, 'content' => $templateResult, 'message_id' => $LastMessageIDs['id']);
	            }
	
	          
	            if ($Chat->is_user_typing == true && (!in_array($chat_id, $typingChats) || (abs($typingChatsCombined[$chat_id] - strlen($Chat->user_typing_txt)) > 6))) {
	                $ReturnStatuses[] = array('chat_id' => $chat_id, 'us' => $Chat->user_status, 'tp' => 'true','tx' => htmlspecialchars($Chat->user_typing_txt));
	            } elseif ($Chat->is_user_typing == false && in_array($chat_id, $typingChats)) { // Inform only if necessary
	                $ReturnStatuses[] = array('chat_id' => $chat_id, 'us' => $Chat->user_status, 'tp' => 'false');
	            }
	        }
	
	    }
	
	    if (count($ReturnMessages) > 0) {
	    	$content = $ReturnMessages;
	    	$breakSync = true;
	    }
	
	    if (count($ReturnStatuses) > 0) {
	    	$content_status = $ReturnStatuses;
	    	$breakSync = true;
	    }

	    if ($pollingEnabled == false || $breakSync == true || ($pollingServerTimeout + $timeCurrent) < time() ) {
	    	break;
	    } else {
	    	usleep($pollingMessageTimeout * 1000000);
	    }    
    }
    
}



echo json_encode(array('error' => 'false','uw' => $userOwner, 'result_status' => $content_status, 'result' => $content ));
exit;
?>