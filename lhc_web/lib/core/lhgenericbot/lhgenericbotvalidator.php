<?php

class erLhcoreClassGenericBotValidator {

    public static function validateGroup(& $data) {
        $group = erLhcoreClassModelGenericBotGroup::fetch($data['id']);
        $group->name = $data['name'];
        $group->is_collapsed = isset($data['is_collapsed']) && $data['is_collapsed'] == true ? 1 : 0;
        $group->pos = isset($data['pos']) ? (int)$data['pos']: 0;
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

    public static function loadEventTemplate($data) {
        $eventTemplate = erLhcoreClassModelGenericBotTriggerEventTemplate::fetch($data['id']);

        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($data['trigger_id']);

        foreach (erLhcoreClassModelGenericBotTriggerEvent::getList(['filter' => ['trigger_id' => $trigger->id]]) as $triggerEvent) {
            $triggerEvent->removeThis();
        }

        // Save new trigger events
        foreach ($eventTemplate->configuration_array as $triggerEventPayload) {
            $triggerEvent = new erLhcoreClassModelGenericBotTriggerEvent();
            $triggerEvent->setState($triggerEventPayload);
            $triggerEvent->trigger_id = $trigger->id;
            $triggerEvent->bot_id = $trigger->bot_id;
            $triggerEvent->id = null;
            $triggerEvent->configuration = json_encode($triggerEventPayload['configuration_array']);
            $triggerEvent->saveThis();
        }
    }

    public static function validateEventTemplateSave(& $data) {
        $trigger = erLhcoreClassModelGenericBotTriggerEventTemplate::findOne(['filter' => ['name' => $data['name']]]);

        if (!($trigger instanceof erLhcoreClassModelGenericBotTriggerEventTemplate)){
            $trigger = new erLhcoreClassModelGenericBotTriggerEventTemplate();
        }

        $trigger->configuration = json_encode($data['actions']);
        $trigger->name = $data['name'];
        $trigger->saveThis();
    }

    public static function validateTemplateSave(& $data) {

        $trigger = erLhcoreClassModelGenericBotTriggerTemplate::findOne(['filter' => ['name' => $data['name']]]);

        if (!($trigger instanceof erLhcoreClassModelGenericBotTriggerTemplate)){
            $trigger = new erLhcoreClassModelGenericBotTriggerTemplate();
        }

        $trigger->actions = json_encode($data['actions']);

        $matches = [];
        preg_match_all('/"_id":"([A-Za-z_\-0-9]+)"/s',$trigger->actions,$matches);

        // Save _id's as new always to avoid cache collisions
        if (isset($matches[1]) && !empty($matches[0])) {
            foreach ($matches[1] as $index => $match) {
                $trigger->actions = str_replace($matches[0][$index], str_replace($matches[1][$index],  erLhcoreClassChat::generateHash(10), $matches[0][$index]), $trigger->actions);
            }
        }

        $trigger->name = $data['name'];
        $trigger->saveThis();
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
        $triggerEvent->skip = (int)$data['skip'];
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

    public static function exportBot($bot)
    {
        $exportData = array('bot' => array('name' => $bot->name));

        $groups = erLhcoreClassModelGenericBotGroup::getList(array('sort' => 'id ASC', 'filter' => array('bot_id' => $bot->id)));

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

                // Payloads
                $payloads = erLhcoreClassModelGenericBotPayload::getList(array('sort' => 'id ASC', 'filter' => array('trigger_id' => $trigger->id, 'bot_id' => $bot->id)));
                $payloadsVars = array();

                foreach ($payloads as $payload) {
                    $payloadVar = get_object_vars($payload);
                    unset($payloadVar['id']);
                    unset($payloadVar['trigger_id']);
                    unset($payloadVar['bot_id']);
                    $payloadsVars[] = $payloadVar;
                }

                $itemTrigger = array(
                    'trigger' => $triggerVars,
                    'events' => $eventsVars,
                    'payloads' => $payloadsVars
                );

                $item['triggers'][] = $itemTrigger;
            }

            $exportData['groups'][] = $item;
        }

        return $exportData;
    }

    public static function importBot($data)
    {
        $bot = new erLhcoreClassModelGenericBotBot();
        $bot->name = $data['bot']['name'] . ' - ' . date('Y-m-d H:i:s');
        $bot->saveThis();

        $replaceTriggerIds = array();
        $triggersArray = array();
        $pregMatchTemporary = array();


        $replaceArraySearch = array();
        $replaceArrayReplace = array();

        foreach ($data['groups'] as $group) {
            $groupObj = new erLhcoreClassModelGenericBotGroup();
            $groupObj->bot_id = $bot->id;
            $groupObj->name = $group['group']['name'];
            $groupObj->is_collapsed = isset($group['group']['is_collapsed']) && is_numeric($group['group']['is_collapsed']) ? $group['group']['is_collapsed'] : 0;
            $groupObj->pos = isset($group['group']['pos']) && is_numeric($group['group']['pos']) ? (int)$group['group']['pos'] : 0;
            $groupObj->saveThis();

            foreach ($group['triggers'] as $trigger) {

                $triggerObj = new erLhcoreClassModelGenericBotTrigger();
                $triggerObj->bot_id = $bot->id;
                $triggerObj->group_id = $groupObj->id;
                $triggerObj->name = $trigger['trigger']['name'];
                $triggerObj->default = $trigger['trigger']['default'];
                $triggerObj->default_unknown = $trigger['trigger']['default_unknown'];
                $triggerObj->default_unknown_btn = isset($trigger['trigger']['default_unknown_btn']) ? $trigger['trigger']['default_unknown_btn'] : 0;
                $triggerObj->actions = $trigger['trigger']['actions'];
                $triggerObj->saveThis();

                $matchesTemp = array();
                preg_match_all('/"(temp[0-9]+)":"([0-9]+)"/is', $triggerObj->actions,$matchesTemp);
                if (isset($matchesTemp[0]) && !empty($matchesTemp[0])) {
                    foreach ($matchesTemp[0] as $matchIndex => $matchValue) {
                        $pregMatchTemporary[] = '"'.$matchesTemp[1][$matchIndex].'":"{replace_trigger_id}"';
                    }
                }

                $triggersArray[] = $triggerObj;
                $replaceTriggerIds[$trigger['trigger']['id']] = $triggerObj->id;

                foreach ($trigger['events'] as $event) {
                    $eventObj = new erLhcoreClassModelGenericBotTriggerEvent();
                    $eventObj->trigger_id = $triggerObj->id;
                    $eventObj->bot_id = $bot->id;
                    $eventObj->pattern = $event['pattern'];
                    $eventObj->pattern_exc = $event['pattern_exc'];
                    $eventObj->configuration = $event['configuration'];
                    $eventObj->type = $event['type'];

                    if (isset($event['on_start_type'])) {
                        $eventObj->on_start_type = $event['on_start_type'];
                    }

                    if (isset($event['skip'])) {
                        $eventObj->skip = (int)$event['skip'];
                    }

                    if (isset($event['priority'])) {
                        $eventObj->priority = $event['priority'];
                    }

                    $eventObj->saveThis();
                }

                // Import payloads
                if (isset($trigger['payloads'])) {
                    foreach ($trigger['payloads'] as $payloadVar) {
                        $payloadObj = new erLhcoreClassModelGenericBotPayload();
                        $payloadObj->name = $payloadVar['name'];
                        $payloadObj->payload = $payloadVar['payload'];
                        $payloadObj->bot_id = $bot->id;
                        $payloadObj->trigger_id = $triggerObj->id;
                        $payloadObj->saveThis();
                    }
                }
            }
        }

        // Preg match all
        foreach ($replaceTriggerIds as $oldTriggerId => $newTriggerId){

            $replaceArraySearch[] = '"payload":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"payload":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"collection_callback_pattern":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"collection_callback_pattern":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"collection_callback_fail":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"collection_callback_fail":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"alternative_callback":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"alternative_callback":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"collection_callback_alternative":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"collection_callback_alternative":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"collection_callback_format":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"collection_callback_format":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"collection_callback_match":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"collection_callback_match":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"collection_callback_cancel":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"collection_callback_cancel":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"payload_online":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"payload_online":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"callback_reschedule":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"callback_reschedule":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"callback_match":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"callback_match":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"trigger_id":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"trigger_id":"' . $newTriggerId . '"';

            $replaceArraySearch[] = '"default_trigger":"' . $oldTriggerId . '"';
            $replaceArrayReplace[] = '"default_trigger":"' . $newTriggerId . '"';

            foreach ($pregMatchTemporary as $tempReplace) {
                $replaceArraySearch[] = str_replace('{replace_trigger_id}', $oldTriggerId, $tempReplace);
                $replaceArrayReplace[] = str_replace('{replace_trigger_id}', $newTriggerId, $tempReplace);
            }
        }

        foreach ($triggersArray as $trigger) {
            $trigger->actions = str_replace($replaceArraySearch,$replaceArrayReplace,$trigger->actions);
            $trigger->saveThis();
        }

        return [
            'bot' => $bot,
            'triggers' => $triggersArray
        ];
    }
}

?>