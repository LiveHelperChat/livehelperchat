<?php

$dummyPayload = null;



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

    erLhcoreClassLog::write(print_r($data, true));

    erLhcoreClassChatWebhookIncoming::processEvent($incomingWebhook, $data);

} catch (Exception $e) {
    erLhcoreClassLog::write(print_r($e, true));
}

exit;

?>