<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ($chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{
	if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT){
	    $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
	    $chat->chat_duration = time() - ($chat->time + $chat->wait_time);
	    erLhcoreClassChat::getSession()->update($chat);
	}
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>