<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (!empty($_GET) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $requestPayload = $_GET;
} else {
    $requestPayload = json_decode(file_get_contents('php://input'),true);
}

try {

    if (!isset($requestPayload['id'])) {
        throw new Exception('Chat ID or message ID not provided!');
    }

    $chat = erLhcoreClassModelChat::fetch($requestPayload['id']);

    if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $requestPayload['hash'])
    {

        $msg = erLhcoreClassModelmsg::findOne(array('limit' => 1, 'sort' => 'id DESC', 'filtergt' => array('user_id' => 0), 'filter' => array('chat_id' => $chat->id)));

        if ($msg instanceof erLhcoreClassModelmsg && $msg->chat_id == $chat->id) {
            $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/getmessagesnippet/getmessagesnippet.tpl.php');
            $tpl->set('messages',array((array)$msg));
            $tpl->set('chat',$chat);
            $tpl->set('react',true);
            $tpl->set('sync_mode','');
            $tpl->set('async_call',true);
            $tpl->set('user', $chat->user);
            $msg = trim($tpl->fetch());

            $msg_body = $tpl->fetch('lhchat/syncuser.tpl.php');

            echo json_encode(array('msg_body' => $msg_body, 'msg' => $msg));
            exit;
        } else {
            http_response_code(400);
            echo json_encode(array('result' => 'Message could not be found!'));
            exit;
        }
    }

} catch ( Exception $e ) {
    http_response_code(400);
    echo json_encode(array('result' => 'Message could not be found!'));
    exit;
}

exit;

?>