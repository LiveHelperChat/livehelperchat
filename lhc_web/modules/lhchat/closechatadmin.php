<?php

header('content-type: application/json; charset=utf-8');

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

erLhcoreClassChat::lockDepartment($chat->dep_id, $db);

// Chat can be closed only by owner
if ($chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{
	if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
	    
	    $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
	    $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat->id);
	    $chat->has_unread_messages = 0;
	    
	    $userData = $currentUser->getUserData(true);

	    $msg = new erLhcoreClassModelmsg();
	    $msg->msg = (string)$userData.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin','has closed the chat!');
	    $msg->chat_id = $chat->id;
	    $msg->user_id = -1;
	    $chat->last_user_msg_time = $msg->time = time();

	    erLhcoreClassChat::getSession()->save($msg);

	    if ($chat->wait_time == 0) {
	        $chat->wait_time = time() - $chat->time;
	    }
	    
	    erLhcoreClassChat::getSession()->update($chat);
	    
	    erLhcoreClassChat::updateActiveChats($chat->user_id);
	    
	    if ($chat->department !== false) {
	        erLhcoreClassChat::updateDepartmentStats($chat->department);
	    }
	    
	    // Execute callback for close chat
	    erLhcoreClassChat::closeChatCallback($chat,$userData);
	}
}

$db->commit();

echo json_encode(array('error' => 'false', 'result' => 'ok' ));
exit;

?>