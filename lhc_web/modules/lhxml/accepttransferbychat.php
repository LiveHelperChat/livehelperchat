<?php

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$transfer = erLhcoreClassTransfer::getTransferByChat($Params['user_parameters']['chat_id']);

$chatTransfer = erLhcoreClassTransfer::getSession()->load( 'erLhcoreClassModelTransfer', $transfer['id']);
$chat_id = $chatTransfer->chat_id;
erLhcoreClassTransfer::getSession()->delete($chatTransfer);

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chat_id);

// Set new chat owner
$chat->user_id = $currentUser->getUserID();
    
erLhcoreClassChat::getSession()->update($chat);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfer_accepted',array('chat' => & $chat));

echo json_encode(array('error' => 'false'));
exit;

?>