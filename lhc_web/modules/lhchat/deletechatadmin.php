<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	exit;
}

if ($currentUser->hasAccessTo('lhchat','deleteglobalchat') || ($currentUser->hasAccessTo('lhchat','deletechat') && $chat->user_id == $currentUser->getUserID()))
{
	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.delete',array('chat' => & $chat,'user' => $currentUser));
	$chat->removeThis();
    echo json_encode(array('error' => 'false', 'result' => 'ok' ));
} else {
   echo json_encode(array('error' => 'true', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/deletechatadmin','You do not have rights to delete a chat') ));
}

exit;

?>