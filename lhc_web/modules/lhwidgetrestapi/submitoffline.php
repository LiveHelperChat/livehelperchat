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
$inputData->validate_start_chat = $inputData->validate_start_chat = isset($requestPayload['mode']) && $requestPayload['mode'] == 'popup' ? true : false;
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
$additionalParams['offline'] = true;

// Validate post data
$Errors = erLhcoreClassChatValidator::validateStartChat($inputData,$startDataFields,$chat, $additionalParams);

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
        erLhcoreClassChatMail::sendMailRequest($inputData, $chat, array('chatprefill' => isset($chatPrefill) ? $chatPrefill : false));
    }

    if (isset($chatPrefill) && ($chatPrefill instanceof erLhcoreClassModelChat)) {
        erLhcoreClassChatValidator::updateInitialChatAttributes($chatPrefill, $chat);
    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_offline_request', array(
        'input_data' => $inputData,
        'chat' => $chat,
        'prefill' => array('chatprefill' => isset($chatPrefill) ? $chatPrefill : false)));

    erLhcoreClassChatValidator::saveOfflineRequest(array('chat' => & $chat, 'question' => $inputData->question));

    $outputResponse = array (
        'success' => true
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