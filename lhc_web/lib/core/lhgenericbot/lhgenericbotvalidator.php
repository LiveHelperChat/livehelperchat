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
        $triggerEvent->pattern_exc = $data['pattern_exc'];
        $triggerEvent->configuration_array = is_array($data['configuration_array']) ? $data['configuration_array'] : new stdClass();
        $triggerEvent->configuration = json_encode($triggerEvent->configuration_array);
        $triggerEvent->on_start_type = (int)$data['on_start_type'];
        $triggerEvent->priority = (int)$data['priority'];
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
            
            if (isset($action['content']['buttons'])) {
                foreach ($action['content']['buttons'] as $quickReply) {
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
        $payloadObj->payload = $payload['value'];
        $payloadObj->saveThis();
    }

    public static function formatExceptionList($exceptionGroup, $exceptions)
    {
        if ($exceptionGroup->id > 0) {
            $exceptionsMessages = erLhcoreClassModelGenericBotExceptionMessage::getList(array('filter' => array('exception_group_id' => $exceptionGroup->id)));
        } else {
            $exceptionsMessages = array();
        }

        $remappedExceptions = array();
        foreach ($exceptionsMessages as $exceptionsMessage) {
            $remappedExceptions[$exceptionsMessage->code] = $exceptionsMessage;
        }

        $formatedExceptions = array();
        foreach ($exceptions as $exception) {
            if (isset($remappedExceptions[$exception['code']])) {
                $formatedExceptions[$exception['code']] = $remappedExceptions[$exception['code']];
                $formatedExceptions[$exception['code']]->default_message = $exception['message'];
            } else {
                $formatedExceptions[$exception['code']] = new erLhcoreClassModelGenericBotExceptionMessage();
                $formatedExceptions[$exception['code']]->code = $exception['code'];
                $formatedExceptions[$exception['code']]->default_message = $exception['message'];
            }
        }

        return $formatedExceptions;
    }

    public static function getUseCasesTrigger(erLhcoreClassModelGenericBotTrigger $trigger) {

        $useCases = array();

        $triggersId = array();

        $actions = $trigger->actions_front;

        foreach ($actions as $action) {
            if ($action['type'] == 'command') {

                if (isset($action['content']['payload']) && is_numeric($action['content']['payload']) && $action['content']['payload'] > 0) {
                    $triggersId[] = (int)$action['content']['payload'];
                }

                if (isset($action['content']['payload_online']) && is_numeric($action['content']['payload_online']) && $action['content']['payload_online'] > 0) {
                    $triggersId[] = (int)$action['content']['payload_online'];
                }

            } else if ($action['type'] == 'predefined') {

                if (isset($action['content']['payload']) && is_numeric($action['content']['payload']) && $action['content']['payload'] > 0) {
                    $triggersId[] = (int)$action['content']['payload'];
                }

            } else if ($action['type'] == 'actions') {

                if (isset($action['content']['attr_options']['collection_callback_pattern']) && is_numeric($action['content']['attr_options']['collection_callback_pattern']) && $action['content']['attr_options']['collection_callback_pattern'] > 0) {
                    $triggersId[] = (int)$action['content']['attr_options']['collection_callback_pattern'];
                }

                if (isset($action['content']['attr_options']['collection_callback_cancel']) && is_numeric($action['content']['attr_options']['collection_callback_cancel']) && $action['content']['attr_options']['collection_callback_cancel'] > 0) {
                    $triggersId[] = (int)$action['content']['attr_options']['collection_callback_cancel'];
                }

                if (isset($action['content']['attr_options']['collection_callback_alternative']) && is_numeric($action['content']['attr_options']['collection_callback_alternative']) && $action['content']['attr_options']['collection_callback_alternative'] > 0) {
                    $triggersId[] = (int)$action['content']['attr_options']['collection_callback_alternative'];
                }

            } else if ($action['type'] == 'intent') {

                if (isset($action['content']['intents']) && is_array($action['content']['intents'])) {
                    foreach ($action['content']['intents'] as $item) {
                        if (isset($item['content']['trigger_id']) && is_numeric($item['content']['trigger_id']) && $item['content']['trigger_id'] > 0) {
                            $triggersId[] = (int)$item['content']['trigger_id'];
                        }
                    }
                }
            } else if ($action['type'] == 'conditions') {

                if (isset($action['content']['attr_options']['callback_match']) && is_numeric($action['content']['attr_options']['callback_match']) && $action['content']['attr_options']['callback_match'] > 0) {
                    $triggersId[] = (int)$action['content']['attr_options']['callback_match'];
                }

                if (isset($action['content']['attr_options']['callback_reschedule']) && is_numeric($action['content']['attr_options']['callback_reschedule']) && $action['content']['attr_options']['callback_reschedule'] > 0) {
                    $triggersId[] = (int)$action['content']['attr_options']['callback_reschedule'];
                }
            }
        }

        if (!empty($triggersId)){
            return erLhcoreClassModelGenericBotTrigger::getList(array('filterin' => array('id' => $triggersId)));
        }

        return $useCases;
    }

    public static function getUseCases(erLhcoreClassModelGenericBotTrigger $trigger)
    {
        $useCases = array();

        $triggers = erLhcoreClassModelGenericBotTrigger::getList(array('filter' => array('bot_id' => $trigger->bot_id)));

        foreach ($triggers as $triggerCompare) {
            if ($triggerCompare->id != $trigger->id) {
                $triggersUses = self::getUseCasesTrigger($triggerCompare);
                if (key_exists($trigger->id,$triggersUses)) {
                    $useCases[] = array(
                        'id' => $triggerCompare->id,
                        'name' => $triggerCompare->name,
                    );
                }
            }
        }

        return $useCases;
    }
}

?>