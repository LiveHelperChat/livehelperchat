<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.blockuser', array());

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	exit;
}

if (($currentUser->hasAccessTo('lhchat','allowblockusers') || $chat->user_id == $currentUser->getUserID()))
{
    $chat->blockUser();
    echo json_encode(array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','User was blocked!')));
} else {
    echo json_encode(array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','User blocking failed, perhaps you do not have permission to block users?')));
}

exit;

?>