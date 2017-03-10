<?php

// Check is there online user instance and user has messsages from operator in that case he have seen message from operator
if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {

    $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'vid' => $_GET['vid']));

    if ($userInstance !== false && $userInstance->has_message_from_operator == true) {
        $userInstance->message_seen = 1;
        $userInstance->message_seen_ts = time();
        $userInstance->saveThis();
    }
}

if ($_GET['hash'] != '') {
    
    $chatID = $_GET['chat_id'];
    $hash = $_GET['hash'];
    
    try {
	        $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);
	        if ($chat->hash == $hash && $chat->user_status != 1) {
	        	       	
		        	$db = ezcDbInstance::get();
		        	$db->beginTransaction();
	
				        // User closed chat
				        $chat->user_status = erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT;
				        $chat->support_informed = 1;
				        $chat->user_closed_ts = time();

				        if ($chat->user_typing < (time()-12)) {
				        	$chat->user_typing = time()-5;// Show for shorter period these status messages
				        	$chat->is_user_typing = 1;
				        	$chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','Visitor has left the chat!'),ENT_QUOTES);
				        }
				        
				        // User Closed Chat
				        if (isset($_GET['eclose']) && $_GET['eclose'] == true) {
				            $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT;
				            
				            $msg = new erLhcoreClassModelmsg();
				            $msg->msg = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','Visitor has closed the chat explicitly!'),ENT_QUOTES);;
				            $msg->chat_id = $chat->id;
				            $msg->user_id = -1;
				            $msg->time = time();
				            
				            erLhcoreClassChat::getSession()->save($msg);
		          				            
				            $chat->last_user_msg_time = $msg->time;
				            
				            // Set last message ID
				            if ($chat->last_msg_id < $msg->id) {
				               $chat->last_msg_id = $msg->id;
				            }
				        }
				        
				        if ( ($onlineuser = $chat->online_user) !== false) {
				        	$onlineuser->reopen_chat = 0;
				        	$onlineuser->saveThis();
				        }
				        
				        erLhcoreClassChat::getSession()->update($chat);
				        				        
				        if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
				            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat',array('chat' => & $chat));
				        }
				        
			        $db->commit();
			        
			        echo erLhcoreClassRestAPIHandler::outputResponse(array(
			        		'error' => false,
			        		'result' => true
			        ));
	        } else {
	        	echo erLhcoreClassRestAPIHandler::outputResponse(array(
	        			'error' => false,
	        			'result' => true
	        	));
	        }
	        
    } catch (Exception $e) {
       echo erLhcoreClassRestAPIHandler::outputResponse(array(
	        'error' => true,
	        'result' => $e->getMessage()
	    ));
    }
}

exit;

?>