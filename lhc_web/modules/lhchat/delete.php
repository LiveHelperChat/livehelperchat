<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);


$currentUser = erLhcoreClassUser::instance();

if ($currentUser->hasAccessTo('lhchat','deleteglobalchat') || ($currentUser->hasAccessTo('lhchat','deletechat') && $chat->user_id == $currentUser->getUserID()))
{
	$chat->removeThis();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
return;


?>