<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

$requestData = json_decode(file_get_contents('php://input'),true);

if ($Params['user_parameters_unordered']['method'] == 'template') {
    erLhcoreClassGenericBotValidator::validateTemplateSave($requestData);
    echo json_encode(array('templates' => array_values(erLhcoreClassModelGenericBotTriggerTemplate::getList(['limit' => false, 'ignore_fields' => array('actions')]))));
    exit;
} elseif ($Params['user_parameters_unordered']['method'] == 'eventtemplate') {
    erLhcoreClassGenericBotValidator::validateEventTemplateSave($requestData);
    echo json_encode(array('templates' => array_values(erLhcoreClassModelGenericBotTriggerEventTemplate::getList(['limit' => false, 'ignore_fields' => array('configuration')]))));
    exit;
} elseif ($Params['user_parameters_unordered']['method'] == 'deleteeventtemplate') {
    erLhcoreClassModelGenericBotTriggerEventTemplate::fetch($requestData['id'])->removeThis();
    echo json_encode(array('templates' => array_values(erLhcoreClassModelGenericBotTriggerEventTemplate::getList(['limit' => false, 'ignore_fields' => array('configuration')]))));
    exit;
} elseif ($Params['user_parameters_unordered']['method'] == 'deletetemplate') {
    erLhcoreClassModelGenericBotTriggerTemplate::fetch($requestData['id'])->removeThis();
    echo json_encode(array('templates' => array_values(erLhcoreClassModelGenericBotTriggerTemplate::getList(['limit' => false, 'ignore_fields' => array('actions')]))));
    exit;
} elseif ($Params['user_parameters_unordered']['method'] == 'loadeventtemplate') {
    erLhcoreClassGenericBotValidator::loadEventTemplate($requestData);

    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($requestData['trigger_id']);
    $events = array_values($trigger->events);

    erLhcoreClassChat::prefillGetAttributes($events, array('bot_id','id','pattern','pattern_exc','trigger_id','type','configuration_array'),array('configuration'),array('do_not_clean' => true));

    echo json_encode(array('events' => $events));
    exit;
} elseif ($Params['user_parameters_unordered']['method'] == 'loadtemplate') {
    echo json_encode(array('result' => erLhcoreClassModelGenericBotTriggerTemplate::fetch($requestData['id'])->actions_front));
    exit;
} else {
    erLhcoreClassGenericBotValidator::validateTriggerSave($requestData);
}

echo json_encode(array('error' => false));
exit;

?>