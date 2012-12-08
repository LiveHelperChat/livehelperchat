<?php

$chatTransfer = erLhcoreClassTransfer::getSession()->load( 'erLhcoreClassModelTransfer', $Params['user_parameters']['transfer_id']);
$chat_id = $chatTransfer->chat_id;
erLhcoreClassTransfer::getSession()->delete($chatTransfer);

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chat_id);

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();    
$chat->user_id = $currentUser->getUserID();
    
erLhcoreClassChat::getSession()->update($chat);

echo json_encode(array('error' => 'false'));
exit;
?>