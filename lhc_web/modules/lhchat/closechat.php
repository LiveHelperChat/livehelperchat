<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

erLhcoreClassChat::lockDepartment($chat->dep_id, $db);

// Chat can be closed only by owner
if ($chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{
	$userData = $currentUser->getUserData(true);
	
	erLhcoreClassChatHelper::closeChat(array(
	   'user' => $userData,
	   'chat' => $chat,
	));
}

$db->commit();

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>