<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

$requestData = json_decode(file_get_contents('php://input'),true);

erLhcoreClassGenericBotValidator::validateAddPayload($requestData);

$trigger = erLhcoreClassModelGenericBotTrigger::fetch($requestData['trigger_id']);

echo json_encode(
    array(
        'payloads' => array_values(erLhcoreClassModelGenericBotPayload::getList(array('filter' => array('bot_id' => (int)$trigger->bot_id))))
    )
);

exit;
?>