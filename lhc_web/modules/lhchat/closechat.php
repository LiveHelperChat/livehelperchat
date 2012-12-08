<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();    
$currentUser->getUserID();

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ($chat->user_id = $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{
    $chat->status = 2;
    erLhcoreClassChat::getSession()->update($chat);    
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
return;

?>