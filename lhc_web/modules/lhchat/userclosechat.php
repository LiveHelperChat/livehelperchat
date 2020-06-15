<?php
// Happens then user closes browser.
ignore_user_abort(true);

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    $chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
	if ($chat->user_status != 1) {

		    // User closed chat
		    $chat->user_status = erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT;
		    $chat->support_informed = 1;
		    $chat->user_typing = time()-5;// Show for shorter period these status messages
		    $chat->is_user_typing = 1;
		    $chat->user_closed_ts = time();
		    $chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','Visitor has left the chat!'),ENT_QUOTES);

		    $explicitClosed = false;
		    
		    if ($Params['user_parameters_unordered']['eclose'] == 't') {

                if ($chat->wait_time == 0) {
                    if ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                        $chat->pnd_time = time();
                        $chat->wait_time = 2;
                    } else {
                        $chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
                    }
                }

                if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) {
                    erLhcoreClassChat::lockDepartment($chat->dep_id, $db);

                    // From now chat will be closed explicitly
                    $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT;

                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'Visitor has closed the chat explicitly!'), ENT_QUOTES);;
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

    	        $explicitClosed = true;
		    }

            $chat->updateThis(array('update' => array(
                'last_msg_id',
                'last_user_msg_time',
                'wait_time',
                'status_sub',
                'user_typing_txt',
                'user_closed_ts',
                'is_user_typing',
                'user_typing',
                'support_informed',
                'user_status'
            )));
		    
		    if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
		        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat',array('chat' => & $chat));
		    }
		    
		    if ($explicitClosed == true) {

                if ($chat->user_id > 0) {
                    erLhcoreClassChat::updateActiveChats($chat->user_id);
                }

		        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.explicitly_closed',array('chat' => & $chat));
		    }

	    $db->commit();
	}
}

erLhcoreClassRestAPIHandler::setHeaders();
echo json_encode(array('error' => 'false', 'result' => 'ok'));
exit;

?>