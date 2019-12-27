<?php

erLhcoreClassRestAPIHandler::setHeaders();

session_write_close();

$db = ezcDbInstance::get();

$db->beginTransaction();

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

$validStatuses = array(
    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
    erLhcoreClassModelChat::STATUS_BOT_CHAT,
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

if (isset($_GET['id']) && isset($_GET['payload'])) {
    $paramsPayload = array('id' => $_GET['id'], 'payload' => $_GET['payload'], 'processed' => (isset($_GET['processed']) && $_GET['processed'] == 'true'));
} else {
    $paramsPayload = json_decode(file_get_contents('php://input'),true);
}

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

        if (!isset($paramsPayload['payload']) || empty($paramsPayload['payload'])) {
            throw new Exception('Payload not provided');
        }

        erLhcoreClassGenericBotWorkflow::processUpdateClick($chat, $message, $paramsPayload['payload']);

        echo json_encode(array('error' => false));

        $db->commit();

    } else {
        throw new Exception('You do not have permission!');
    }

} catch (Exception $e) {
    $db->rollback();
    echo json_encode(array('error' => true, 'message' => $e->getMessage()));
}




exit;

?>