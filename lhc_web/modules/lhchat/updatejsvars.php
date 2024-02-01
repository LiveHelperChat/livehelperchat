<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-Type: application/json', (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : (isset($_POST['host']) && $_POST['host'] != '' ? $_POST['host'] : "*")));

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

        if ($Params['user_parameters_unordered']['userinit'] !== 'true') {
            erLhcoreClassChatValidator::validateJSVarsVisitor ( $vid, $jsonData);
        }

        if (
            (($checkHash == true && is_object($chat) && $chat->hash == $hash) || ($checkHash == false && is_object($chat))) &&
            ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) &&
            (!in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED, erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT, erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW, erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM)))
        ) {

            // Event for extensions to listen
            if ($Params['user_parameters_unordered']['userinit'] === 'true') {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.update_chat_vars', array(
                    'chat' => & $chat,
                    'data' => $jsonData
                ));
                echo json_encode(array('userinit' => 'true'));
                exit;
            }

            // Update chat variables
            erLhcoreClassChatValidator::validateJSVarsChat ($chat, $jsonData);

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