<?php

class erLhcoreClassGenericBotValidator {

    public static function validateGroup(& $data) {
        $group = erLhcoreClassModelGenericBotGroup::fetch($data['id']);
        $group->name = $data['name'];
        $group->saveThis();
    }

    public static function validateTrigger(& $data) {
        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($data['id']);
        $trigger->name = $data['name'];
        $trigger->saveThis();
    }

    public static function validateTriggerSave(& $data) {
        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($data['id']);
        $trigger->actions = json_encode($data['actions']);
        $trigger->name = $data['name'];
        $trigger->saveThis();
    }

    public static function validateTriggerEvent(& $data) {
        $triggerEvent = erLhcoreClassModelGenericBotTriggerEvent::fetch($data['id']);
        $triggerEvent->type = $data['type'];
        $triggerEvent->pattern = $data['pattern'];
        $triggerEvent->saveThis();
    }
}

?>