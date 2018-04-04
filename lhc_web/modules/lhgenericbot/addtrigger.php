<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = new erLhcoreClassModelGenericBotTrigger();
$trigger->name = "New trigger";
$trigger->group_id = (int)$Params['user_parameters']['id'];
$trigger->saveThis();

echo json_encode(
    array(
        'name' => $trigger->name,
        'id' => $trigger->id,
        'group_id' => $trigger->group_id,
        'actions' => [],
    )
);

exit;
?>