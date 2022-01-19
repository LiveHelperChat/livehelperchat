<?php

$chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

if (($chat->can_edit_chat && ($currentUser->hasAccessTo('lhchat', 'deleteglobalchat') || ($currentUser->hasAccessTo('lhchat', 'deletechat') && $chat->user_id == $currentUser->getUserID())))) {
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.delete', array('chat' => & $chat, 'user' => $currentUser));
    $chat->removeThis();
}

echo "ok";
exit;

?>