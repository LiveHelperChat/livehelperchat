<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	exit;
}

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ($chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{
	if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {

	    $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
	    $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat->id);

	    $userData = $currentUser->getUserData(true);

	    $msg = new erLhcoreClassModelmsg();
	    $msg->msg = (string)$userData.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin','has closed the chat!');
	    $msg->chat_id = $chat->id;
	    $msg->user_id = -1;
	    $chat->last_user_msg_time = $msg->time = time();

	    erLhcoreClassChat::getSession()->save($msg);

	    erLhcoreClassChat::getSession()->update($chat);
	    
	    erLhcoreClassChat::updateActiveChats($chat->user_id);
	    
	    if ($chat->department !== false) {
	        erLhcoreClassChat::updateDepartmentStats($chat->department);
	    }
	    
	    // Execute callback for close chat
	    erLhcoreClassChat::closeChatCallback($chat,$userData);	   
	}
}

CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', (int)$Params['user_parameters']['chat_id']);

echo json_encode(array('error' => 'false', 'result' => 'ok' ));
exit;

?>