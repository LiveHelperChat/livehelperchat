<?php

erLhcoreClassRestAPIHandler::setHeaders();
erTranslationClassLhTranslation::$htmlEscape = false;

if (isset($_POST['document'])) {
    $requestPayload = json_decode($_POST['document'],true);
} else {
    $requestPayload = json_decode(file_get_contents('php://input'),true);
}

$Params['user_parameters_unordered']['department'] = isset($requestPayload['department']) ? $requestPayload['department'] : null;

$chat = new erLhcoreClassModelChat();

$inputData = new stdClass();
$inputData->chatprefill = '';
$inputData->email = '';
$inputData->username = '';
$inputData->phone = '';
$inputData->product_id = '';
$inputData->validate_start_chat = $inputData->validate_start_chat = isset($requestPayload['mode']) && $requestPayload['mode'] == 'popup' ? true : false;
$inputData->ignore_captcha = true;
$inputData->priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false;
$inputData->only_bot_online = isset($_POST['onlyBotOnline']) ? (int)$_POST['onlyBotOnline'] : 0;
$inputData->vid = isset($requestPayload['vid']) && $requestPayload['vid'] != '' ? (string)$requestPayload['vid'] : '';

if (isset($requestPayload['fields']['DepartamentID']) && !empty($requestPayload['fields']['DepartamentID'])) {
    $Params['user_parameters_unordered']['department'] = [$requestPayload['fields']['DepartamentID']];
}

// Choose very first department even if it's `Visible only if online`
if (is_array($Params['user_parameters_unordered']['department']) && count($Params['user_parameters_unordered']['department']) > 1) {
    $Params['user_parameters_unordered']['department'] = [$Params['user_parameters_unordered']['department'][0]];
}

$_POST['URLRefer'] = isset($requestPayload['fields']['URLRefer']) ? $requestPayload['fields']['URLRefer'] : '';

if (is_array($_POST['URLRefer'])) {
    if (isset($_POST['URLRefer']['href'])){
        $_POST['URLRefer'] = (string)$_POST['URLRefer']['href'];
    } else {
        $_POST['URLRefer'] = '';
    }
}

if (is_array($Params['user_parameters_unordered']['department']) && count($Params['user_parameters_unordered']['department']) == 1) {
    $parametersDepartment = erLhcoreClassChat::extractDepartment($Params['user_parameters_unordered']['department']);
    $Params['user_parameters_unordered']['department'] = $parametersDepartment['system'];
    $requestPayload['fields']['DepartamentID'] = $inputData->departament_id = array_shift($Params['user_parameters_unordered']['department']);
} else {
    $inputData->departament_id = 0;
}

if (is_numeric($inputData->departament_id) && $inputData->departament_id > 0 && ($startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('customfilter' => array("((`dep_ids` != '' AND JSON_CONTAINS(`dep_ids`,'" . (int)$inputData->departament_id . "','$')) OR department_id = " . (int)$inputData->departament_id . ")" )))) !== false) {
    $startDataFields = $startDataDepartment->data_array;
} else {
    // Start chat field options
    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $startDataFields = (array)$startData->data;
}

if (isset($requestPayload['theme']) && ($themeId = erLhcoreClassChat::extractTheme($requestPayload['theme'])) !== false) {
    $additionalParams['theme'] = erLhAbstractModelWidgetTheme::fetch($themeId);
}

$additionalParams['payload_data'] = $requestPayload['fields'];
$additionalParams['offline'] = true;

// Validate post data
$Errors = erLhcoreClassChatValidator::validateStartChat($inputData,$startDataFields,$chat, $additionalParams);

if (empty($Errors) && isset($startDataFields['pre_conditions']) && !empty($startDataFields['pre_conditions'])) {
    $preConditions = json_decode($startDataFields['pre_conditions'], true);
    if (
        (isset($preConditions['maintenance_mode']) && $preConditions['maintenance_mode'] == 1) ||
        (isset($preConditions['online']) && !empty($preConditions['online'])) ||
        (isset($preConditions['offline']) && !empty($preConditions['offline'])) ||
        (isset($preConditions['disable']) && !empty($preConditions['disable'])) ) {

        $outcome = erLhcoreClassChatValidator::validatePreconditions($preConditions, ['is_online' => false, 'online_user' => (isset($onlineUser) ? $onlineUser : false)]);

        if ($outcome['mode'] == 'disable' || $outcome['mode'] == 'terminate') {
            $Errors[] = $outcome['message'];
        }
    }
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_chat_started', array('chat' => & $chat, 'errors' => & $Errors, 'offline' => (isset($additionalParams['offline']) && $additionalParams['offline'] == true)));

if (empty($Errors)) {
    $chat->setIP();
    $chat->lsync = time();
    erLhcoreClassModelChat::detectLocation($chat, $inputData->vid);

    $chat->referrer = isset($requestPayload['fields']['URLRefer']) ? $requestPayload['fields']['URLRefer'] : '';
    $chat->session_referrer = isset($requestPayload['fields']['r']) ? $requestPayload['fields']['r'] : '';

    $statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value, $inputData->vid);

    if ($statusGeoAdjustment['status'] == 'hidden') { // This should never happen
        $outputResponse = array (
            'success' => false,
            'errors' => 'Chat not available in your country'
        );

        erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
        exit;
    }

    // Because product can have different department than selected product, we reasign chat to correct department if required
    if ($chat->product_id > 0) {
        $chat->dep_id = $chat->product->departament_id;
    }

    $attributePresend = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_offline_request_presend', array(
        'input_data' => $inputData,
        'chat' => $chat,
        'prefill' => array('chatprefill' => isset($chatPrefill) ? $chatPrefill : false)));

    if (!isset($attributePresend['status']) || $attributePresend['status'] !== erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
        try {
            erLhcoreClassChatMail::sendMailRequest($inputData, $chat, array('chatprefill' => isset($chatPrefill) ? $chatPrefill : false));
        } catch (Exception $e) {
            $optionsJson = JSON_FORCE_OBJECT;
            $outputResponse = array(
                'success' => false,
                'errors' => [erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There was a problem sending your request. Please try again later!')]
            );
            erLhcoreClassRestAPIHandler::outputResponse($outputResponse, 'json', isset($optionsJson) ? $optionsJson : 0);
            exit;
        }
    }

    if (isset($chatPrefill) && ($chatPrefill instanceof erLhcoreClassModelChat)) {
        erLhcoreClassChatValidator::updateInitialChatAttributes($chatPrefill, $chat);
    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_offline_request', array(
        'input_data' => $inputData,
        'chat' => $chat,
        'prefill' => array('chatprefill' => isset($chatPrefill) ? $chatPrefill : false)));

    try {
        $db = ezcDbInstance::get();
        $db->beginTransaction();

        $requestSaved = erLhcoreClassChatValidator::saveOfflineRequest(array('chat' => & $chat, 'input_data' => $inputData, 'question' => (isset($inputData->question) ? $inputData->question : '')));

        // Assign chat to user
        if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 && is_numeric($chat->id)) {
            // To track online users
            $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array(
                'vid' => $inputData->vid,
                'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value,
            ));

            if ($userInstance !== false) {
                $userInstance->chat_id = $chat->id;
                $userInstance->dep_id = $chat->dep_id;
                $userInstance->saveThis();
                $chat->online_user_id = $userInstance->id;
                $chat->saveThis();
            }
        }

        $db->commit();

        // Remove out of transaction scope
        // Same as start chat workflow
        if ($requestSaved === true) {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_offline_request_saved', array(
                'chat' =>  & $chat
            ));
        }

    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }

    $outputResponse = array (
        'success' => true
    );

} else {
    $optionsJson = JSON_FORCE_OBJECT;
    $outputResponse = array (
        'success' => false,
        'errors' => $Errors
    );
}

erLhcoreClassRestAPIHandler::outputResponse($outputResponse, 'json', isset($optionsJson) ? $optionsJson : 0);
exit;

?>