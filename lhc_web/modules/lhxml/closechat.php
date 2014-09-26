<?php

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}


$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
// Chat can be closed only by owner
if ($chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{
    $chat->status = 2;
    erLhcoreClassChat::getSession()->update($chat);
           
    erLhcoreClassChat::updateActiveChats($chat->user_id);
    
    $userData = $currentUser->getUserData(true);
    
    // Execute callback for close chat
    erLhcoreClassChat::closeChatCallback($chat,$userData);
    
}

exit;
?>