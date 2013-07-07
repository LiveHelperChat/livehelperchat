<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ($chat->user_id = $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{
    $chat->status = 2;

    $userData = $currentUser->getUserData(true);

    $msg = new erLhcoreClassModelmsg();
    $msg->msg = (string)$userData.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin','has closed the chat!');
    $msg->chat_id = $chat->id;
    $msg->user_id = -1;
    $chat->last_user_msg_time = $msg->time = time();

    erLhcoreClassChat::getSession()->save($msg);

    erLhcoreClassChat::getSession()->update($chat);
}

echo json_encode(array('error' => 'false', 'result' => 'ok' ));
exit;

?>