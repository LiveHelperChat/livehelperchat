<?php
header ( 'content-type: application/json; charset=utf-8' );

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat) ) {
    echo erLhcoreClassChat::safe_json_encode(array('nick' => $chat->nick));
} else {
    echo erLhcoreClassChat::safe_json_encode(array('nick' => $Params['user_parameters']['chat_id']));
}

exit();
?>