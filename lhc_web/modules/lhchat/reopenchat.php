<?php

$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	exit;
}

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed/reopened only by owner
if ($chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowreopenremote'))
{
	if ($chat->status != erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {

	    $chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;

	    $userData = $currentUser->getUserData(true);

	    $msg = new erLhcoreClassModelmsg();
	    $msg->msg = (string)$userData.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/reopenchat','has reopened the chat!');
	    $msg->chat_id = $chat->id;
	    $msg->user_id = -1;
	    $chat->last_user_msg_time = $msg->time = time();

	    erLhcoreClassChat::getSession()->save($msg);

	    if ($chat->last_msg_id < $msg->id) {
	    	$chat->last_msg_id = $msg->id;
	    }

	    $chat->updateThis();

	    echo json_encode(array('status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Active chat'), 'error' => 'false', 'result' => 'ok' ));
	    exit;
	}
}

echo json_encode(array('error' => 'true', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/reopenchat','No permission to reopen the chat!')));
exit;

?>