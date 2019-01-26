<?php
header ( 'content-type: application/json; charset=utf-8' );
header ( 'Access-Control-Allow-Origin: *' );
header ( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept' );

$checkHash = true;
$vid = false;
$chatID = null;

if ($Params ['user_parameters_unordered'] ['hash'] != '') {
    list ( $chatID, $hash ) = explode ( '_', $Params ['user_parameters_unordered'] ['hash'] );
} else if ($Params ['user_parameters_unordered'] ['hash_resume'] != '') {
    list ( $chatID, $hash ) = explode ( '_', $Params ['user_parameters_unordered'] ['hash_resume'] );
}

if ($Params ['user_parameters_unordered'] ['vid'] != '') {
    $vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params ['user_parameters_unordered'] ['vid']);

    if ($chatID === null && is_object($vid)) {
        $chatID = $vid->chat_id;
        $checkHash = false;
    }
}

try {

    if ($chatID > 0) {
        $chat = erLhcoreClassChat::getSession ()->load ( 'erLhcoreClassModelChat', $chatID );
    } else {
        $chat = false;
    }

    if (is_object($vid)) {

        $data = $_POST ['data'];
        $jsonData = json_decode ( $data, true );
        erLhcoreClassChatValidator::validateJSVarsVisitor ( $vid, $jsonData);

        if ((($checkHash == true && is_object($chat) && $chat->hash == $hash) || ($checkHash == false && is_object($chat))) && ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) {

            // Update chat variables
            erLhcoreClassChatValidator::validateJSVarsChat ( $chat, $jsonData);

            // Force operators to check for new messages
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed_chat', array(
                'chat' => & $chat
            ));
        }

        echo json_encode(array('stored' => 'true'));
        exit;
    } else {
        echo json_encode(array('stored' => 'false'));
        exit;
    }

} catch ( Exception $e ) {

}
exit ();

?>