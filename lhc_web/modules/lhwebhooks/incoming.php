<?php

$dummyPayload = null;
$dummyPayload = json_decode('{"contact":{"attributes":null,"createdDatetime":"2023-04-27T10:46:26Z","customDetails":null,"displayName":"+37065272274","firstName":"","href":"","id":"b9b299127dbb4f88bb2dd681e10f6e2d","lastName":"","msisdn":37065272274,"updatedDatetime":"2023-04-27T10:46:26Z"},"conversation":{"contactId":"b9b299127dbb4f88bb2dd681e10f6e2d","createdDatetime":"2023-04-27T10:46:26Z","id":"d159a2b8328e4d36b13817cf4808e608","lastReceivedDatetime":"2023-04-27T10:54:55.207355235Z","lastUsedChannelId":"044a3138-744f-4599-b908-93b035927a47","lastUsedPlatformId":"whatsapp","status":"active","updatedDatetime":"2023-04-27T10:46:26.152718694Z"},"message":{"channelId":"044a3138-744f-4599-b908-93b035927a47","content":{"text":"Just to test something"},"conversationId":"d159a2b8328e4d36b13817cf4808e608","createdDatetime":"2023-04-27T10:54:52Z","direction":"received","from":"+37065272274","id":"396080b1fa0a4e6781bfda4b8b9f00b6","metadata":{"replyTo":{"id":"00000000-0000-0000-0000-000000000000"},"sender":{"displayName":"Remigijus Kiminas"}},"platform":"whatsapp","status":"received","to":"+12057455030","type":"text","updatedDatetime":"2023-04-27T10:54:52Z"},"type":"message.created","workspaceId":11412038}',true);


try {

    $incomingWebhook = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('identifier' => $Params['user_parameters']['identifier'])));

    if (!($incomingWebhook instanceof erLhcoreClassModelChatIncomingWebhook)) {
       throw new Exception('Incoming webhook could not be found!');
    }

    if ($incomingWebhook->disabled == 1) {
        throw new Exception('Incoming webhook is disabled!');
    }

    if (!is_array($dummyPayload)) {
        if (isset($_POST) && is_array($_POST) && !empty($_POST)){
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }
    } else {
        $data = $dummyPayload;
    }

    if ($incomingWebhook->log_incoming == 1) {
        erLhcoreClassLog::write(json_encode($data,JSON_PRETTY_PRINT),
            ezcLog::SUCCESS_AUDIT,
            array(
                'source' => 'lhc',
                'category' => 'incoming_webhook',
                'line' => __LINE__,
                'file' => __FILE__,
                'object_id' => $incomingWebhook->id
            )
        );
    }
    
    if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
        erLhcoreClassLog::write(json_encode($data));
    }

    if (session_id()) session_write_close();

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.webhook_incoming', array(
        'webhook' => & $incomingWebhook,
        'data' => & $data
    ));

    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    erLhcoreClassChatWebhookIncoming::processEvent($incomingWebhook, $data);

} catch (Exception $e) {
    if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true){
        erLhcoreClassLog::write($e->getMessage().' | '. json_encode($data));
    }

    if (isset($data) && isset($incomingWebhook) && is_object($incomingWebhook) && $incomingWebhook->log_failed_parse== 1) {
        erLhcoreClassLog::write(json_encode($data,JSON_PRETTY_PRINT) . print_r($e, true),
            ezcLog::SUCCESS_AUDIT,
            array(
                'source' => 'lhc',
                'category' => 'incoming_webhook_parse',
                'line' => __LINE__,
                'file' => __FILE__,
                'object_id' => $incomingWebhook->id
            )
        );
    }
}

exit;

?>