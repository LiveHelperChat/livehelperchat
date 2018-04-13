<?php

header ( 'content-type: application/json; charset=utf-8' );

$triggerEvent = new erLhcoreClassModelGenericBotTriggerEvent();
$triggerEvent->trigger_id = (int)$Params['user_parameters']['id'];
$triggerEvent->saveThis();

echo json_encode(
    $triggerEvent->getState()
);

exit;
?>