<?php

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
	if ($chat->user_status != 1) {

		$db = ezcDbInstance::get();
		$db->beginTransaction();

		    // User closed chat
		    $chat->user_status = 1;
		    $chat->support_informed = 1;
		    $chat->user_typing = time()-5;// Show for shorter period these status messages
		    $chat->is_user_typing = 1;
		    $chat->user_closed_ts = time();
		    $chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','Visitor has left the chat!'),ENT_QUOTES);

		    // From now chat will be closed explicitly	   
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
		    
		    erLhcoreClassChat::getSession()->update($chat);
		    
		    if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
		        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat',array('chat' => & $chat));
		    }

	    $db->commit();
	}


}

echo json_encode(array('error' => 'false', 'result' => 'ok'));
exit;

?>