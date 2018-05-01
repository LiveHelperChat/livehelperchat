<?php

header ( 'content-type: application/json; charset=utf-8' );

$group = erLhcoreClassModelGenericBotGroup::fetch((int)$Params['user_parameters']['id']);

$trigger = new erLhcoreClassModelGenericBotTrigger();
$trigger->name = "New trigger";
$trigger->bot_id = $group->bot_id;
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