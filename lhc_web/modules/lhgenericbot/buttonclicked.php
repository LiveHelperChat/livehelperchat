<?php

erLhcoreClassRestAPIHandler::setHeaders();

session_write_close();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$validStatuses = array(
    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
    erLhcoreClassModelChat::STATUS_BOT_CHAT,
);

if (isset($_GET['id']) && isset($_GET['payload'])) {
    $paramsPayload = array('id' => $_GET['id'], 'payload' => $_GET['payload'], 'processed' => (isset($_GET['processed']) && $_GET['processed'] == 'true'));
} else {
    $paramsPayload = json_decode(file_get_contents('php://input'),true);
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

try {
    if ($chat->hash == $Params['user_parameters']['hash'] && (in_array($chat->status,$validStatuses)) && !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) {

        if (!isset($paramsPayload['id']) || !is_numeric($paramsPayload['id'])) {
            throw new Exception('Message not provided!');
        }

        $message = erLhcoreClassModelmsg::fetch($paramsPayload['id']);

        if (!($message instanceof erLhcoreClassModelmsg)) {
            throw new Exception('Message could not be found!');
        }

        if ($message->chat_id != $chat->id) {
            throw new Exception('Invalid message provided');
        }

        if (!isset($paramsPayload['payload']) || $paramsPayload['payload'] == '') {
            throw new Exception('Payload not provided');
        }

        if ($Params['user_parameters_unordered']['type'] == 'valueclicked') {
            erLhcoreClassGenericBotWorkflow::processValueClick($chat, $message, $paramsPayload['payload'], array('processed' => (isset($paramsPayload['processed']) && $paramsPayload['processed'] == true)));
        } elseif ($Params['user_parameters_unordered']['type'] == 'triggerclicked') {
            erLhcoreClassGenericBotWorkflow::processTriggerClick($chat, $message, $paramsPayload['payload'], array('processed' => (isset($paramsPayload['processed']) && $paramsPayload['processed'] == true)));
        } elseif ($Params['user_parameters_unordered']['type'] == 'editgenericstep') {
            erLhcoreClassGenericBotWorkflow::processStepEdit($chat, $message, $paramsPayload['payload'], array('processed' => (isset($paramsPayload['processed']) && $paramsPayload['processed'] == true)));
        } else {
            erLhcoreClassGenericBotWorkflow::processButtonClick($chat, $message, $paramsPayload['payload'], array('processed' => (isset($paramsPayload['processed']) && $paramsPayload['processed'] == true)));
        }

        // On button click also update counter
        if ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
            $chatVariables = $chat->chat_variables_array;
            if (!isset($chatVariables['msg_v'])) {
                $chatVariables['msg_v'] = 1;
            } else {
                $chatVariables['msg_v']++;
            }
            $chat->chat_variables_array = $chatVariables;
            $chat->chat_variables = json_encode($chatVariables);
            $chat->updateThis(array('update' => array('chat_variables')));
        }

        echo json_encode(array('error' => false));

    } else {
        throw new Exception('You do not have permission!');
    }

} catch (Exception $e) {
    echo json_encode(array('error' => true, 'message' => $e->getMessage()));
}

exit;

?>