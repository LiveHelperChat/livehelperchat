<?php

erLhcoreClassLog::write(file_get_contents('php://input'));

$dummyPayload = null;//;json_decode('{"messages":[{"id":"false_37065272274@c.us_3A5911DB19C785450372","body":"https://s3.eu-central-1.wasabisys.com/incoming-chat-api/2021/3/9/229607/218ea8e2-6ecb-4281-85c4-0b14c8bf7902.jpeg","fromMe":false,"self":0,"isForwarded":0,"author":"37065272274@c.us","time":1615273008,"chatId":"37065272274@c.us","messageNumber":3000,"type":"image","senderName":"Remigijus Kiminas","caption":null,"quotedMsgBody":null,"quotedMsgId":null,"quotedMsgType":null,"chatName":"Remigijus Kiminas"}],"instanceId":"229607"}', true);

try {

    $incomingWebhook = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('identifier' => $Params['user_parameters']['identifier'])));

    if (!($incomingWebhook instanceof erLhcoreClassModelChatIncomingWebhook)) {
       throw new Exception('Incoming webhook could not be found!');
    }

    if ($incomingWebhook->disabled == 1) {
        throw new Exception('Incoming webhook is disabled!');
    }

    if (!is_array($dummyPayload)){
        $data = json_decode(file_get_contents('php://input'), true);
    } else {
        $data = $dummyPayload;
    }

    erLhcoreClassChatWebhookIncoming::processEvent($incomingWebhook, $data);

} catch (Exception $e) {
    print_r($e);
}



exit;

?>