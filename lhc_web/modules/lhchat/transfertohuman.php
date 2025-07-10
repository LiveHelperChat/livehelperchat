<?php

erLhcoreClassRestAPIHandler::setHeaders();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$validStatuses = array(
    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
    erLhcoreClassModelChat::STATUS_BOT_CHAT,
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

if ($chat->hash == $Params['user_parameters']['hash'] && (in_array($chat->status,$validStatuses))) {

    // Store system message that chat was transferred to pending state by visitor
    $msg = new erLhcoreClassModelmsg();
    $msg->msg = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser','Visitor requested to speak with a human agent by clicking Switch To Human button'),ENT_QUOTES);
    $msg->chat_id = $chat->id;
    $msg->user_id = -1;
    $msg->time = time();
    erLhcoreClassChat::getSession()->save($msg);

    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
    $chat->status_sub_sub = erLhcoreClassModelChat::STATUS_SUB_SUB_CLOSED; // Will be used to indicate that we have to show notification for this chat if it appears on list
    $chat->pnd_time = time();
    $chat->last_msg_id = $msg->id;
    $chat->saveThis();

    if ($chat->auto_responder instanceof erLhAbstractModelAutoResponderChat) {
        $chat->auto_responder->wait_timeout_send = 0;
        $chat->auto_responder->pending_send_status = 0;
        $chat->auto_responder->active_send_status = 0;
        $chat->auto_responder->updateThis();
    }

    // If chat is transferred to pending state we don't want to process any old events
    erLhcoreClassGenericBotWorkflow::removePreviousEvents($chat->id);

    // Because we want that mobile app would receive notification
    // By default these listeners are not set if visitors sends a message and chat is not active
    if (erLhcoreClassChatEventDispatcher::getInstance()->disableMobile == true && erLhcoreClassChatEventDispatcher::getInstance()->globalListenersSet == true) {
        erLhcoreClassChatEventDispatcher::getInstance()->disableMobile = false;
        erLhcoreClassChatEventDispatcher::getInstance()->globalListenersSet = false;
        erLhcoreClassChatEventDispatcher::getInstance()->setGlobalListeners();
    }

    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
        'action' => array(
            'content' => array('command' => 'stopchat'),
        ),
        'chat' => & $chat,
        'is_online' => true
    ));

    echo json_encode(array('error' => false));
} else {
    echo json_encode(array('error' => true));
}

exit;

?>