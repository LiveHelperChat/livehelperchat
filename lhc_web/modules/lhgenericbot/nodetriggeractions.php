<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = erLhcoreClassModelGenericBotTrigger::fetch($Params['user_parameters']['id']);

$events = array_values($trigger->events);

erLhcoreClassChat::prefillGetAttributes($events,array('bot_id','id','pattern','pattern_exc','trigger_id','type','configuration_array'),array('configuration'),array('do_not_clean' => true));

echo json_encode(
    array(
        'name' => $trigger->name,
        'id' => $trigger->id,
        'group_id' => $trigger->group_id,
        'actions' => $trigger->actions_front,
        'events' => $events
    )
);

exit;
?>