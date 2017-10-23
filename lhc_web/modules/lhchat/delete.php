<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);


$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSRF Token');
	exit;
}

if ($currentUser->hasAccessTo('lhchat','deleteglobalchat') || ($currentUser->hasAccessTo('lhchat','deletechat') && $chat->user_id == $currentUser->getUserID()))
{
	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.delete',array('chat' => & $chat,'user' => $currentUser));
	$chat->removeThis();	
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;


?>