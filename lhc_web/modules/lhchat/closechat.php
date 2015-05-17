<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ($chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{

	if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
		die('Invalid CSRF Token');
		exit;
	}

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

	    $chat->updateThis();
	    
	    erLhcoreClassChat::updateActiveChats($chat->user_id);
	    
	    if ($chat->department !== false) {
	        erLhcoreClassChat::updateDepartmentStats($chat->department);
	    }
	    
	    // Execute callback for close chat
	    erLhcoreClassChat::closeChatCallback($chat,$userData);
	}
}

CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', (int)$Params['user_parameters']['chat_id']);

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>