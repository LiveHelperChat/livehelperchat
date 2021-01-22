<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (!empty($_GET) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $requestPayload = $_GET;
} else {
    $requestPayload = json_decode(file_get_contents('php://input'),true);
}

try {

    if (!isset($requestPayload['id']) || !isset($requestPayload['msg_id'])) {
        throw new Exception('Chat ID or message ID not provided!');
    }

    $chat = erLhcoreClassModelChat::fetch($requestPayload['id']);

    if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $requestPayload['hash'])
    {
        $msg = erLhcoreClassModelmsg::fetch($requestPayload['msg_id']);

        if ($msg instanceof erLhcoreClassModelmsg && $msg->chat_id == $chat->id) {

            $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncuser.tpl.php');
            $tpl->set('messages',array((array)$msg));
            $tpl->set('chat',$chat);
            $tpl->set('react',true);
            $tpl->set('sync_mode','');
            $tpl->set('async_call',true);

            echo json_encode(array('id' => $msg->id, 'msg' => trim($tpl->fetch())));
            exit;
        }
    }

} catch ( Exception $e ) {

}

exit;

?>