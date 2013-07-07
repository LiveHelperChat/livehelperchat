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

		    $msg = new erLhcoreClassModelmsg();
		    $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','User has left the chat!');
		    $msg->chat_id = $chat->id;
		    $msg->user_id = -1; // System messages get's user_id -1

		    $chat->last_user_msg_time = $msg->time = time();

		    erLhcoreClassChat::getSession()->save($msg);

		    // Set last message ID
		    if ($chat->last_msg_id < $msg->id) {
		    	$chat->last_msg_id = $msg->id;
		    }

		    erLhcoreClassChat::getSession()->update($chat);

	    $db->commit();
	}
}

echo json_encode(array('error' => 'false', 'result' => 'ok'));
exit;

?>