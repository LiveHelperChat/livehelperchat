<?php

header ( 'content-type: application/json; charset=utf-8' );

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