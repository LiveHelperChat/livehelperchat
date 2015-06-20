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
	
	$userData = $currentUser->getUserData(true);
	
	erLhcoreClassChatHelper::closeChat(array(
	   'user' => $userData,
	   'chat' => $chat,
	));
}

CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', (int)$Params['user_parameters']['chat_id']);

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>