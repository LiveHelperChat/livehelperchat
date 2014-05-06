<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);


$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSRF Token');
	exit;
}

CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', $chat->id);

if ($currentUser->hasAccessTo('lhchat','deleteglobalchat') || ($currentUser->hasAccessTo('lhchat','deletechat') && $chat->user_id == $currentUser->getUserID()))
{
	$chat->removeThis();	
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;


?>