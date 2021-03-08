<?php

$dummyPayload = json_decode('{
    "messages": [{
        "id": "false_17472822486@c.us_DF38E6A25B42CC8CCE57EC40F",
        "body": "Ok!",
        "type": "chat",
        "senderName": "Ilya",
        "fromMe": true,
        "author": "17472822486@c.us",
        "time": 1504208593,
        "chatId": "17472822486@c.us",
        "messageNumber": 100
    }]
}', true);

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