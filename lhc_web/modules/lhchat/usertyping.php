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

            $msg = isset($_POST['msg']) ? strip_tags($_POST['msg']) : '';

            if ($msg != '' && strlen($msg) > 50){
            	if ( function_exists('mb_substr') ) {
            		$msg = mb_substr($msg, -50);
            	} else {
            		$msg = substr($msg, -50);
            	}
            }

            $chat->user_typing_txt = $msg;

        } else {
            $chat->user_typing = 0;
        }




        erLhcoreClassChat::getSession()->update($chat);
}

echo json_encode(array());
exit;

?>