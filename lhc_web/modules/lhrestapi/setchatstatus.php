<?php

try {

    erLhcoreClassRestAPIHandler::validateRequest();

    if (isset($_POST['chat_id'])) {
        $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $_POST['chat_id']);
    } else {
        throw new Exception('chat_id has to be provided!');
    }

    $validStatus = array(
        erLhcoreClassModelChat::STATUS_PENDING_CHAT,
        erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
        erLhcoreClassModelChat::STATUS_CLOSED_CHAT,
        erLhcoreClassModelChat::STATUS_CHATBOX_CHAT,
        erLhcoreClassModelChat::STATUS_OPERATORS_CHAT,
        erLhcoreClassModelChat::STATUS_BOT_CHAT,
    );

    if (isset($_POST['status']) && in_array($_POST['status'], $validStatus)) {

        $userData = erLhcoreClassRestAPIHandler::getUser();

        $changeStatus = (int) $_POST['status'];

        erLhcoreClassChatHelper::changeStatus(array(
            'user' => $userData,
            'chat' => $chat,
            'status' => $changeStatus,
            'allow_close_remote' => erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'allowcloseremote')
        ));

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed',array('chat' => & $chat, 'user_data' => $userData));

        echo erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => false,
            'result' => 'Status changed!'
        ));

    } else {
        throw new Exception('Unknown status!');
    }

} catch ( Exception $e ) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}
exit ();

?>