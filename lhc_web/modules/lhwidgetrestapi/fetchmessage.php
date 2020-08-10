<?php

erLhcoreClassRestAPIHandler::setHeaders();

$requestPayload = json_decode(file_get_contents('php://input'),true);

try {
    $chat = erLhcoreClassModelChat::fetch($requestPayload['id']);

    if ($chat->hash == $requestPayload['hash'])
    {
        $msg = erLhcoreClassModelmsg::fetch($requestPayload['msg_id']);

        if ($msg->chat_id == $chat->id) {

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