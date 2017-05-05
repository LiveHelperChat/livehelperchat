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

// Lock chat record for update untill we finish this procedure
$stmt = $db->prepare('SELECT 1 FROM lh_userdep WHERE dep_id = :dep_id FOR UPDATE;');
$stmt->bindValue(':dep_id',$chat->dep_id);
$stmt->execute();

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

CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', (int)$Params['user_parameters']['chat_id']);

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>