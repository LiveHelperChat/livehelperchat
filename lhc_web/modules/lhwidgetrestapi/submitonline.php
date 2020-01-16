<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

$requestPayload = json_decode(file_get_contents('php://input'),true);

$Params['user_parameters_unordered']['department'] = $requestPayload['department'];

$chat = new erLhcoreClassModelChat();

$inputData = new stdClass();
$inputData->chatprefill = '';
$inputData->email = '';
$inputData->username = '';
$inputData->phone = '';
$inputData->product_id = '';
$inputData->bot_id = '';
$inputData->validate_start_chat = false;
$inputData->ignore_captcha = true;
$inputData->priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false;
$inputData->only_bot_online = isset($_POST['onlyBotOnline']) ? (int)$_POST['onlyBotOnline'] : 0;
$inputData->vid = isset($requestPayload['vid']) && $requestPayload['vid'] != '' ? (string)$requestPayload['vid'] : '';

if (is_array($Params['user_parameters_unordered']['department']) && count($Params['user_parameters_unordered']['department']) == 1) {
    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
    $requestPayload['fields']['DepartamentID'] = $inputData->departament_id = array_shift($Params['user_parameters_unordered']['department']);
} else {
    $inputData->departament_id = 0;
}

if (is_numeric($inputData->departament_id) && $inputData->departament_id > 0 && ($startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $inputData->departament_id)))) !== false) {
    $startDataFields = $startDataDepartment->data_array;
} else {
    // Start chat field options
    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $startDataFields = (array)$startData->data;
}

if (isset($requestPayload['theme']) && $requestPayload['theme'] > 0) {
    $additionalParams['theme'] = erLhAbstractModelWidgetTheme::fetch($requestPayload['theme']);
}

$additionalParams['payload_data'] = $requestPayload['fields'];

$Errors = erLhcoreClassChatValidator::validateStartChat($inputData,$startDataFields,$chat, $additionalParams);

if (empty($Errors)) {

    $chat->lsync = time();
    $chat->setIP();

    erLhcoreClassModelChat::detectLocation($chat);

    $statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value, $inputData->vid);

    if ($statusGeoAdjustment['status'] == 'hidden') { // This should never happen
        $outputResponse = array (
            'success' => false,
            'errors' => 'Chat not available in your country'
        );

        erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
        exit;
    }


    $chat->time = $chat->pnd_time = time();
    $chat->status = 0;

    $chat->hash = erLhcoreClassChat::generateHash();
    $chat->referrer = isset($requestPayload['fields']['URLRefer']) ? $requestPayload['fields']['URLRefer'] : '';
    $chat->session_referrer = isset($requestPayload['fields']['r']) ? $requestPayload['fields']['r'] : '';

    $nick = trim($chat->nick);

    if ( empty($nick) ) {
        $chat->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
    }

    try {
        $db = ezcDbInstance::get();
        $db->beginTransaction();

        // Store chat
        $chat->saveThis();

        // Assign chat to user
        if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
            // To track online users
            $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('check_message_operator' => true, 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'vid' => $inputData->vid));

            if ($userInstance !== false) {
                $userInstance->chat_id = $chat->id;
                $userInstance->dep_id = $chat->dep_id;
                $userInstance->message_seen = 1;
                $userInstance->message_seen_ts = time();

                if ($chat->nick != erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor')) {
                    $onlineAttr = $userInstance->online_attr_system_array;
                    if (!isset($onlineAttr['username'])){
                        $onlineAttr['username'] = $chat->nick;
                        $userInstance->online_attr_system = json_encode($onlineAttr);
                    }
                } elseif ($chat->nick == erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor')){
                    if ($userInstance->nick && $userInstance->has_nick) {
                        $chat->nick = $userInstance->nick;
                    }
                }

                $userInstance->saveThis();

                $chat->online_user_id = $userInstance->id;
                $chat->saveThis();

                if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) {
                    erLhcoreClassModelChatOnlineUserFootprint::assignChatToPageviews($userInstance, erLhcoreClassModelChatConfig::fetch('footprint_background')->current_value == 1);
                }
            }
        }

        $messageInitial = false;

        $paramsExecution = array();

        // Store message if required
        if (isset($startDataFields['message_visible_in_page_widget']) && $startDataFields['message_visible_in_page_widget'] == true) {
            if ( $inputData->question != '') {
                // Store question as message
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = trim($inputData->question);
                $msg->chat_id = $chat->id;
                $msg->user_id = 0;
                $msg->time = time();
                erLhcoreClassChat::getSession()->save($msg);

                $paramsExecution['msg'] = $messageInitial = $msg;

                $chat->unanswered_chat = 1;
                $chat->last_msg_id = $msg->id;
                $chat->saveThis();
            }
        }

        if (is_numeric($inputData->bot_id)) {
            $paramsExecution['bot_id'] = (int)$inputData->bot_id;
        }

        // Set bot workflow if required
        erLhcoreClassChatValidator::setBot($chat, $paramsExecution);

        // Auto responder
        $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);

        if ($responder instanceof erLhAbstractModelAutoResponder) {
            $beforeAutoResponderErrors = array();
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered',array('chat' => & $chat, 'errors' => & $beforeAutoResponderErrors));

            if (empty($beforeAutoResponderErrors)) {

                $responderChat = new erLhAbstractModelAutoResponderChat();
                $responderChat->auto_responder_id = $responder->id;
                $responderChat->chat_id = $chat->id;
                $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                $responderChat->saveThis();

                $chat->auto_responder_id = $responderChat->id;

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_message',array('chat' => & $chat, 'responder' => & $responder));

                if ($responder->wait_message != '' && $chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = trim($responder->wait_message);
                    $msg->meta_msg = $responder->getMeta($chat, 'pending');
                    $msg->chat_id = $chat->id;
                    $msg->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
                    $msg->user_id = -2;
                    $msg->time = time() + 5;
                    erLhcoreClassChat::getSession()->save($msg);

                    if ($chat->last_msg_id < $msg->id) {
                        $chat->last_msg_id = $msg->id;
                    }
                }

                $chat->saveThis();

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_responder_triggered', array('chat' => & $chat));
            } else {
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder got error').': '.implode('; ', $beforeAutoResponderErrors);
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;
                $msg->time = time();

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                erLhcoreClassChat::getSession()->save($msg);
            }
        }

        erLhcoreClassChat::updateDepartmentStats($chat->department);

        // Paid chat settings
        if (isset($paidChatSettings)) {
            erLhcoreClassChatPaid::processPaidChatWorkflow(array(
                'chat' => $chat,
                'paid_chat_params' => $paidChatSettings,
            ));
        }

        $db->commit();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_started', array('chat' => & $chat, 'msg' => $messageInitial));

    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }

    $outputResponse = array (
        'success' => true,
        'chatData' => array (
            'id' => $chat->id,
            'hash' => $chat->hash,
        )
    );

} else {
    $outputResponse = array (
        'success' => false,
        'errors' => $Errors
    );
}

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit;

?>