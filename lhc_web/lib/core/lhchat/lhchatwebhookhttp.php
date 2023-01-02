<?php

class erLhcoreClassChatWebhookHttp {

    public function processEvent($event, $params) {

        try {
            $webhooks = erLhcoreClassModelChatWebhook::getList(array('filter' => array('event' => $event, 'disabled' => 0)));
        } catch (Exception $e) {
            return;
        }

        if (!empty($webhooks)) {

            $db = ezcDbInstance::get();

            foreach ($webhooks as $webhook) {

                // processTrigger always requires a chat so fake it.
                if (!isset($params['chat']) || !($params['chat'] instanceof erLhcoreClassModelChat)) {
                    $params['chat'] = new erLhcoreClassModelChat();
                    $params['chat']->id = -1;
                }

                if (self::isValidConditions($webhook, $params['chat']) === true) {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($webhook->trigger_id);
                    if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {

                        $db->beginTransaction();

                        // Not always passed argument is synced to update
                        // So we can't just refresh object
                        // $params['chat'] = erLhcoreClassModelChat::fetchAndLock($params['chat']->id);

                        $paramsExecution = ['msg_last_id' => $params['chat']->last_msg_id];

                        erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, true, array('args' => $params));

                        if (!isset($params['no_auto_events']) || $params['no_auto_events'] === false) {
                            erLhcoreClassChatWebhookContinuous::dispatchEvents($params['chat'], $paramsExecution);
                        }

                        $db->commit();
                    }
                } elseif ($webhook->trigger_id_alt > 0) {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($webhook->trigger_id_alt);
                    if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {

                        $db->beginTransaction();

                        // Not always passed argument is synced to update
                        // So we can't just refresh object
                        // $params['chat'] = erLhcoreClassModelChat::fetchAndLock($params['chat']->id);

                        $paramsExecution = ['msg_last_id' => $params['chat']->last_msg_id];

                        erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, true, array('args' => $params));

                        if (!isset($params['no_auto_events']) || $params['no_auto_events'] === false) {
                            erLhcoreClassChatWebhookContinuous::dispatchEvents($params['chat'], $paramsExecution);
                        }

                        $db->commit();
                    }
                }
            }
        }
    }


    public static function isValidConditions($continuousHook, $chat) {

        $configurationParams = $continuousHook->conditions_array;

        if (empty($configurationParams)) {
            return true;
        }

        $mainGroups = [];
        $orGroupItems = [];

        foreach ($configurationParams as $configurationParam) {
            if ($configurationParam['type'] == '4') {
                if (!empty($orGroupItems)) {
                    $mainGroups[] = $orGroupItems;
                }
                $orGroupItems = array();
            } else {
                $orGroupItems[] = $configurationParam;
            }
        }

        if (!empty($orGroupItems)) {
            $mainGroups[] = $orGroupItems;
        }

        foreach ($mainGroups as $conditionItems) {
            $groupedConditions = [];
            foreach ($conditionItems as $indexCondition => $conditionItem){
                $subItems[] = $indexCondition;
                $allItems[] = $indexCondition;

                if (isset($conditionItem['logic']) && $conditionItem['logic'] == 'or') {
                    $nextConditionChild = true;
                } else {
                    $nextConditionChild = false;
                }

                if ($nextConditionChild === false) {
                    $groupedConditions[] = $subItems;
                    $subItems = array();
                }
            }

            if (!empty($subItems)) {
                $groupedConditions[] = $subItems;
            }

            // This hook was already applied for specific chat. No point to check again
            if (isset($chatsApplied[$continuousHook->id]) && in_array($chat->id, $chatsApplied[$continuousHook->id])) {
                continue;
            }

            $previousMessageId = 0;

            // We do final check here
            $isValid = true;
            foreach ($groupedConditions as $groupedConditionItems) {
                $isValidSubItem = false;
                foreach ($groupedConditionItems as $groupedConditionItem) {
                    $conditionsCurrent = $conditionItems[$groupedConditionItem];

                    $conditionItemValid = false;

                    if ($conditionsCurrent['type'] == '1') { // Visitor message contains
                        $paramsMessage = array('limit' => 1, 'sort' => 'id DESC', 'filternotin' => array('user_id' => array(-1)), 'filter' => array('chat_id' => $chat->id));
                        if ($previousMessageId > 0) {
                            $paramsMessage['filterlt']['id'] = $previousMessageId;
                        }
                        $messageLast = erLhcoreClassModelmsg::findOne($paramsMessage);
                        if ($messageLast instanceof erLhcoreClassModelmsg) {
                            $previousMessageId = $messageLast->id;
                            if ($messageLast->user_id == 0) {
                                $conditionItemValid = erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                                    'pattern' => $conditionsCurrent['message_contains'],
                                    'msg' => $messageLast->msg,
                                    'words_typo' => 0,
                                ))['found'];
                            }
                        }
                    } elseif ($conditionsCurrent['type'] == '3') { // No response from operator for n seconds
                        $conditionAttr = $conditionsCurrent['attr'];
                        if (strpos($conditionAttr,'{args.') !== false) {
                            $matchesValues = array();
                            preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $conditionAttr,$matchesValues);
                            if (!empty($matchesValues[0])) {
                                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                                    $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array('chat' => $chat), $matchesValues[1][$indexElement], '.');
                                    $conditionAttr = str_replace($elementValue,  $valueAttribute['found'] == true ? $valueAttribute['value'] : 0, $conditionAttr);
                                }
                            }
                        }

                        $valueAttr = isset($conditionsCurrent['value']) ? $conditionsCurrent['value'] : '';

                        if (strpos($valueAttr,'{args.') !== false) {
                            $matchesValues = array();
                            preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $valueAttr,$matchesValues);
                            if (!empty($matchesValues[0])) {
                                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                                    $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array('chat' => $chat), $matchesValues[1][$indexElement], '.');
                                    $valueAttr = str_replace($elementValue,  $valueAttribute['found'] == true ? $valueAttribute['value'] : 0, $valueAttr);
                                }
                            }
                        }

                        $replaceArray = array(
                            '{time}' => time()
                        );

                        // Remove internal variables
                        $conditionAttr = str_replace(array_keys($replaceArray), array_values($replaceArray),$conditionAttr);
                        $valueAttr = str_replace(array_keys($replaceArray), array_values($replaceArray),$valueAttr);

                        if (!in_array($conditionsCurrent['condition'],['like','notlike','contains'])) {
                            // Remove spaces
                            $conditionAttr = preg_replace('/\s+/', '', $conditionAttr);
                            $valueAttr = preg_replace('/\s+/', '', $valueAttr);

                            // Allow only mathematical operators
                            $conditionAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $conditionAttr);
                            $valueAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $valueAttr);

                            if ($conditionAttrMath != '' && $conditionAttrMath == $conditionAttr) {
                                // Evaluate if there is mathematical rules
                                try {
                                    eval('$conditionAttr = ' . $conditionAttrMath . ";");
                                } catch (ParseError $e) {
                                    // Do nothing
                                }
                            }

                            if ($valueAttrMath != '' && $valueAttrMath == $valueAttr) {
                                // Evaluate if there is mathematical rules
                                try {
                                    eval('$valueAttr = ' . $valueAttrMath . ";");
                                } catch (ParseError $e) {
                                    // Do nothing
                                }
                            }
                        }

                        // For these operations we want numbers
                        if (in_array($conditionsCurrent['condition'],['lt','lte','gt','gte'])) {
                            $conditionAttr = round((float)$conditionAttr,3);
                            $valueAttr = round((float)$valueAttr,3);
                        } elseif ((is_string($conditionAttr) || is_numeric($conditionAttr)) && (is_string($valueAttr) || is_numeric($valueAttr))) {
                            $conditionAttr = (string)$conditionAttr;
                            $valueAttr = (string)$valueAttr;
                        }

                        if ($conditionsCurrent['condition'] == 'eq' && ($conditionAttr == $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'lt' && ($conditionAttr < $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'lte' && ($conditionAttr <= $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'neq' && ($conditionAttr != $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'notempty' && !empty(trim($conditionAttr))) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'empty' && empty(trim($conditionAttr))) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'gte' && ($conditionAttr >= $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'gt' && ($conditionAttr > $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                                'pattern' => $valueAttr,
                                'msg' => $conditionAttr,
                                'words_typo' => 0,
                            ))['found'] == true) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                                'pattern' => $valueAttr,
                                'msg' => $conditionAttr,
                                'words_typo' => 0,
                            ))['found'] == false) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'contains' && strrpos($conditionAttr, $valueAttr) !== false) {
                            $conditionItemValid = true;
                        }
                    }

                    if ($conditionItemValid == true){
                        $isValidSubItem = true;
                    }
                }
                if ($isValidSubItem == false) {
                    $isValid = false;
                    break; // No point to check anything else
                }
            }

            // Group is valid we can execute bot and trigger against specific chat
            if ($isValid === true) {
                return true;
            }
        }

        return false;
    }
}

?>