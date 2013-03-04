<?php

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
        if ( $Params['user_parameters']['status'] == 'true' ) {
            $chat->user_typing = time();
        } else {
            $chat->user_typing = 0;
        }

        erLhcoreClassChat::getSession()->update($chat);
}

echo json_encode(array());
exit;

?>