<?php

$group = erLhcoreClassModelGenericBotGroup::fetch($Params['user_parameters']['id']);

$exportData = array('group' => array('name' => $group->name));

$groups = array($group);

foreach ($groups as $group) {
    $groupVars = get_object_vars($group);
    unset($groupVars['id']);
    unset($groupVars['bot_id']);

    $item = array(
        'group' => $groupVars,
        'triggers' => array()
    );

    $triggers = erLhcoreClassModelGenericBotTrigger::getList(array('sort' => 'id ASC', 'filter' => array('bot_id' => $group->bot_id,'group_id' => $group->id)));
    foreach ($triggers as $trigger) {

        $triggerVars = get_object_vars($trigger);

        unset($triggerVars['group_id']);
        unset($triggerVars['bot_id']);

        $events = erLhcoreClassModelGenericBotTriggerEvent::getList(array('sort' => 'id ASC', 'filter' => array('trigger_id' => $trigger->id, 'bot_id' => $trigger->bot_id)));

        $eventsVars = array();
        foreach ($events as $event) {
            $eventVar = get_object_vars($event);
            unset($eventVar['id']);
            unset($eventVar['trigger_id']);
            unset($eventVar['bot_id']);
            $eventsVars[] = $eventVar;
        }

        $itemTrigger = array(
            'trigger' => $triggerVars,
            'events' => $eventsVars
        );

        $item['triggers'][] = $itemTrigger;
    }

    $exportData['groups'][] = $item;
}

header('Content-Disposition: attachment; filename="lhc-bot-group-'.$group->id.'.json"');
header('Content-Type: application/json');
echo json_encode($exportData);

exit;
?>