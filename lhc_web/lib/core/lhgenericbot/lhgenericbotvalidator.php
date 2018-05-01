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

        self::extractPayloadEvents($trigger, $data['actions']);
    }

    public static function validateTriggerEvent(& $data) {
        $triggerEvent = erLhcoreClassModelGenericBotTriggerEvent::fetch($data['id']);
        $triggerEvent->type = $data['type'];
        $triggerEvent->pattern = $data['pattern'];
        $triggerEvent->saveThis();
    }

    public static function extractPayloadEvents($trigger, $actions) {
        $currentTriggerPayloads = erLhcoreClassModelGenericBotPayload::getList(array('filter' => array('trigger_id' => $trigger->id)));

        $triggerPayloads = array();

        foreach ($actions as $action) {
            if (isset($action['content']['quick_replies'])) {
                foreach ($action['content']['quick_replies'] as $quickReply) {
                    if ($quickReply['type'] == 'button') {
                        $triggerPayloads[$quickReply['content']['payload']] = $quickReply['content']['name'];
                    }
                }
            }
        }

        // Reindex for easier usage
        $remap = array();
        foreach ($currentTriggerPayloads as $currentTriggers) {
            $remap[$currentTriggers->payload] = $currentTriggers;
        }

        // Handle payload changes
        foreach ($triggerPayloads as $payload => $name) {
            if (isset($remap[$payload])){
                if ($remap[$payload]->name != $name) {
                    $remap[$payload]->name = $name;
                    $remap[$payload]->saveThis();
                }
            } else {
                $payloadObj = new erLhcoreClassModelGenericBotPayload();
                $payloadObj->trigger_id = $trigger->id;
                $payloadObj->bot_id = $trigger->bot_id;
                $payloadObj->name = $name;
                $payloadObj->payload = trim($payload);
                $payloadObj->saveThis();
            }
        }

        // Remove removed payloads
        $removePayloads = array_diff(array_keys($remap),array_keys($triggerPayloads));
        foreach ($removePayloads as $removePayload) {
            $remap[$removePayload]->removeThis();
        }
    }


    public static function validateAddPayload($payload)
    {
        $payloadObj = erLhcoreClassModelGenericBotPayload::findOne(array('filter' => array('payload' => $payload['value'], 'trigger_id' => $payload['trigger_id'])));

        if (!($payloadObj instanceof erLhcoreClassModelGenericBotPayload)) {
            $trigger = erLhcoreClassModelGenericBotTrigger::fetch($payload['trigger_id']);
            $payloadObj = new erLhcoreClassModelGenericBotPayload();
            $payloadObj->trigger_id = $trigger->id;
            $payloadObj->bot_id = $trigger->bot_id;
        }

        $payloadObj->name = $payload['name'];
        $payloadObj->payload =$payload['value'];
        $payloadObj->saveThis();
    }
}

?>