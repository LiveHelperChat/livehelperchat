<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

$requestPayload = json_decode(file_get_contents('php://input'),true);

$Params['user_parameters_unordered']['department'] = isset($requestPayload['department']) ? $requestPayload['department'] : null;

$chat = new erLhcoreClassModelChat();

$inputData = new stdClass();
$inputData->chatprefill = '';
$inputData->email = '';
$inputData->username = '';
$inputData->phone = '';
$inputData->product_id = '';
$inputData->bot_id = '';
$inputData->validate_start_chat = $inputData->validate_start_chat = isset($requestPayload['mode']) && $requestPayload['mode'] == 'popup' ? true : false;
$inputData->priority = (isset($requestPayload['fields']['priority']) && is_numeric($requestPayload['fields']['priority'])) ? (int)$requestPayload['fields']['priority'] : false;
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

$additionalParams['payload_data'] = isset($requestPayload['fields']) ? $requestPayload['fields'] : array();

if (isset($additionalParams['payload_data']['phash']) && isset($additionalParams['payload_data']['pvhash']) && (string)$additionalParams['payload_data']['phash'] != '' && (string)$additionalParams['payload_data']['pvhash'] != '') {
    $paidChatSettings = erLhcoreClassChatPaid::paidChatWorkflow(array(
        'uparams' => $additionalParams['payload_data'],
        'mode' => 'chat',
        'output' => 'json'
    ));

    if (isset($paidChatSettings['error'])) {
        $Errors['phash'] = $paidChatSettings['message'];
    }
}

if (isset($restAPI) && $requestPayload['ignore_required'] == true) {
    $additionalParams['ignore_required'] = true;
}

if (isset($restAPI['ignore_captcha']) && $restAPI['ignore_captcha'] === true) {
    $additionalParams['ignore_captcha'] = true;
}

if (isset($restAPI['collect_all']) && $restAPI['collect_all'] === true) {
    $additionalParams['collect_all'] = true;
}

if (!isset($Errors)) {
    $Errors = erLhcoreClassChatValidator::validateStartChat($inputData,$startDataFields,$chat, $additionalParams);
}

if (empty($Errors)) {

    $chat->lsync = time();
    $chat->setIP();

    erLhcoreClassModelChat::detectLocation($chat, $inputData->vid);

    if (!isset($restAPI['ignore_geo']) || $restAPI['ignore_geo'] === false) {
        $statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value, $inputData->vid);

        if ($statusGeoAdjustment['status'] == 'hidden') { // This should never happen
            $outputResponse = array (
                'success' => false,
                'errors' => 'Chat not available in your country'
            );

            erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
            exit;
        }
    }

    $chat->time = $chat->pnd_time = time();
    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;

    $chat->hash = erLhcoreClassChat::generateHash();
    $chat->referrer = isset($requestPayload['fields']['URLRefer']) ? $requestPayload['fields']['URLRefer'] : '';
    $chat->session_referrer = isset($requestPayload['fields']['r']) ? $requestPayload['fields']['r'] : '';

    if (isset($restAPI) && isset($requestPayload['chat_variables']) && is_array($requestPayload['chat_variables'])) {
        $chat_variables_array = $chat->chat_variables_array;
        foreach ($requestPayload['chat_variables'] as $chatVariableKey => $chatVariableName) {
            $chat_variables_array[$chatVariableKey] = $chatVariableName;
        }
        $chat->chat_variables = json_encode($chat_variables_array);
    }

    if (isset($restAPI) && isset($requestPayload['additional_data']) && is_array($requestPayload['additional_data'])) {
        $chat_variables_array = $chat->additional_data_array;
        foreach ($requestPayload['additional_data'] as $chatVariableKey => $chatVariableName) {
            $chat_variables_array[$chatVariableKey] = $chatVariableName;
        }
        $chat->additional_data = json_encode($chat_variables_array);
    }

    $nick = trim($chat->nick);

    if ( empty($nick) ) {
        $chat->nick = 'Visitor';
    }

    try {
        $db = ezcDbInstance::get();
        $db->beginTransaction();

        // Store chat
        $chat->saveThis();

        if (isset($restAPI) && isset($requestPayload['messages']) && is_array($requestPayload['messages'])) {
            erLhcoreClassRestAPIHandler::importMessages($chat, $requestPayload['messages']);
        }

        $paramsExecution = array();

        // Assign chat to user
        if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
            // To track online users
            $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('check_message_operator' => true, 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'vid' => $inputData->vid));

            if ($userInstance !== false) {

                if (isset($requestPayload['invitation_id']) && is_numeric($requestPayload['invitation_id'])) {
                    $chat->invitation_id = (int)$requestPayload['invitation_id'];

                    $onlineAttrSystem = $userInstance->online_attr_system_array;

                    $ignoreResponder = isset($onlineAttrSystem['lhc_ignore_autoresponder']) && $onlineAttrSystem['lhc_ignore_autoresponder'] == 1;

                    if (isset($onlineAttrSystem['lhc_assign_to_me']) && $onlineAttrSystem['lhc_assign_to_me'] == 1 && $userInstance->operator_user_id > 0) {
                        $chat->user_id = $userInstance->operator_user_id;
                        $chat->tslasign = time();
                    }

                    $conversionUser = erLhAbstractModelProactiveChatCampaignConversion::fetch($userInstance->conversion_id);
                    if ($conversionUser instanceof erLhAbstractModelProactiveChatCampaignConversion) {
                        $conversionUser->invitation_status = erLhAbstractModelProactiveChatCampaignConversion::INV_CHAT_STARTED;
                        $conversionUser->chat_id = $chat->id;
                        $conversionUser->department_id = $chat->dep_id;
                        $conversionUser->con_time = time();
                        $conversionUser->saveThis();
                    }

                    $userInstance->conversion_id = 0;

                    // Store Message from operator
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = trim($userInstance->operator_message);

                    if ($msg->msg == '') {
                        $inv = erLhAbstractModelProactiveChatInvitation::fetch($requestPayload['invitation_id']);
                        if ($inv instanceof erLhAbstractModelProactiveChatInvitation){
                            $inv->translateByLocale();
                            $msg->msg = $inv->message;
                        }
                    }

                    $msg->chat_id = $chat->id;
                    $msg->name_support = $userInstance->operator_user !== false ? trim($userInstance->operator_user->name_support) : (!empty($userInstance->operator_user_proactive) ? $userInstance->operator_user_proactive : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support'));
                    $msg->user_id = $userInstance->operator_user_id > 0 ? $userInstance->operator_user_id : -2;
                    $msg->time = time()-7; // Deduct 7 seconds so for user all looks more natural

                    erLhcoreClassChat::getSession()->save($msg);

                    if ($ignoreResponder == false && $userInstance->invitation !== false) {
                        $responder = $userInstance->invitation->autoresponder;
                    }

                    if ($requestPayload['invitation_id'] > 0) {
                        $invitation = erLhAbstractModelProactiveChatInvitation::fetch($requestPayload['invitation_id']);

                        if ($invitation instanceof erLhAbstractModelProactiveChatInvitation && $invitation->bot_id > 0 && $invitation->trigger_id > 0) {

                            if ($invitation->bot_offline == true) {
                                $paramsExecution['bot_only_offline'] = true;
                            }

                            $paramsExecution['bot_id'] = $invitation->bot_id;
                            $paramsExecution['trigger_id'] = $invitation->trigger_id;
                        }
                    }

                    $chat->chat_initiator = erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE;
                }

                $userInstance->chat_id = $chat->id;
                $userInstance->dep_id = $chat->dep_id;
                $userInstance->message_seen = 1;
                $userInstance->message_seen_ts = time();

                if ($chat->nick != 'Visitor') {
                    $onlineAttr = $userInstance->online_attr_system_array;
                    if (!isset($onlineAttr['username'])) {
                        $onlineAttr['username'] = $chat->nick;
                        $userInstance->online_attr_system = json_encode($onlineAttr);
                        $userInstance->online_attr_system_array = $onlineAttr;
                    }
                } elseif ($chat->nick == 'Visitor') {
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

        // Store theme trigger message as first message
        // But only if invitation have not set those
        // And only if it's not button click
        if ((!isset($requestPayload['invitation_id']) || !is_numeric($requestPayload['invitation_id'])) && !isset($msg) && isset($additionalParams['theme']) && isset($additionalParams['theme']->bot_configuration_array['trigger_id'])
            && !empty($additionalParams['theme']->bot_configuration_array['trigger_id'])
            && $additionalParams['theme']->bot_configuration_array['trigger_id'] > 0
            && !isset($requestPayload['bpayload']['payload'])
        ) {
            $trigger = erLhcoreClassModelGenericBotTrigger::fetch($additionalParams['theme']->bot_configuration_array['trigger_id']);
            $paramsExecution['trigger_id_executed'] = $additionalParams['theme']->bot_configuration_array['trigger_id'];
            if (is_object($trigger)) {
                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger);
                $triggerEvent = erLhcoreClassModelGenericBotChatEvent::findOne(array('filter' => array('chat_id' => $chat->id)));
            }
        }

        $messageInitial = false;

        // Store message if required
        if (isset($startDataFields['message_visible_in_page_widget']) && $startDataFields['message_visible_in_page_widget'] == true) {
            if (isset($inputData->question) && $inputData->question != '') {
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

                if (isset($triggerEvent) && $triggerEvent instanceof erLhcoreClassModelGenericBotChatEvent){
                    erLhcoreClassGenericBotWorkflow::userMessageAdded($chat, $msg);
                    $paramsExecution['ignore_default'] = true;
                }
            }
        }

        if (is_numeric($inputData->bot_id) && !isset($paramsExecution['bot_id'])) {
            $paramsExecution['bot_id'] = (int)$inputData->bot_id;
        }

        if (isset($requestPayload['bpayload']['payload']) && isset($requestPayload['bpayload']['type']) && $requestPayload['bpayload']['type'] == 'triggerclicked') {
            $paramsExecution['trigger_id'] = $requestPayload['bpayload']['id'];
            $paramsExecution['trigger_button_id'] = $requestPayload['bpayload']['payload'];
            $paramsExecution['processed'] = $requestPayload['bpayload']['processed'];
        }else if (isset($requestPayload['bpayload']['payload']) && isset($requestPayload['bpayload']['type']) && $requestPayload['bpayload']['type'] == '') {
            $paramsExecution['trigger_id'] = $requestPayload['bpayload']['id'];
            $paramsExecution['trigger_payload_id'] = $requestPayload['bpayload']['payload'];
            $paramsExecution['processed'] = $requestPayload['bpayload']['processed'];
        }

        if (!(isset($requestPayload['ignore_bot']) && $requestPayload['ignore_bot'] == true) && !(isset($additionalParams['payload_data']['ignore_bot']) && $additionalParams['payload_data']['ignore_bot'] == true)) {
            // Set bot workflow if required
            erLhcoreClassChatValidator::setBot($chat, $paramsExecution);
        }

        if (!isset($responder) && (!isset($ignoreResponder) || $ignoreResponder === false)) {
            $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);
        }

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

                if ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT)
                {
                    $messageText = '';

                    if ($responder->offline_message != '' && !erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                            'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                            'ignore_user_status' => false
                        ))) {
                        $messageText = $responder->offline_message;
                    } else {
                        $messageText = $responder->wait_message;
                    }

                    if ($messageText != '') {
                        $msg = new erLhcoreClassModelmsg();
                        $msg->msg = trim($messageText);
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
if (!isset($restAPI)) {
    erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
    exit;
}


?>