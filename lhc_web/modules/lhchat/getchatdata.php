<?php
header ( 'content-type: application/json; charset=utf-8' );

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat) ) {
    echo erLhcoreClassChat::safe_json_encode(array('nick' => $chat->nick));
} else {
    $chatArchive = erLhcoreClassChatArcive::fetchChatById((int)trim($Params['user_parameters']['chat_id']));
    if (is_array($chatArchive)) {
        echo erLhcoreClassChat::safe_json_encode(array('r' => 'chatarchive/viewarchivedchat/' . $chatArchive['archive']->id .'/' . $chatArchive['chat']->id));
    } else {
        echo erLhcoreClassChat::safe_json_encode(array('nick' => $Params['user_parameters']['chat_id']));
    }
}

exit();
?>