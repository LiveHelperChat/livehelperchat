<?php

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{   
    // User closed chat    
    $chat->user_status = 1;        
    $chat->support_informed = 0;
    erLhcoreClassChat::getSession()->update($chat);        
}

echo json_encode(array('error' => 'false', 'result' => 'ok'));
exit;

?>