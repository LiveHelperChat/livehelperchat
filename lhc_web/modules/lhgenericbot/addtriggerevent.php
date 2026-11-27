<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

$trigger = erLhcoreClassModelGenericBotTrigger::fetch($Params['user_parameters']['id']);
$triggerGroup = erLhcoreClassModelGenericBotGroup::fetch($trigger->group_id);

$triggerEvent = new erLhcoreClassModelGenericBotTriggerEvent();
$triggerEvent->trigger_id = (int)$Params['user_parameters']['id'];
$triggerEvent->bot_id = (int)$triggerGroup->bot_id;
$triggerEvent->saveThis();

echo json_encode(
    $triggerEvent->getState()
);

exit;
?>