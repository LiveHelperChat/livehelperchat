<?php

class erLhcoreClassChatWebhookHttp {

    public function processEvent($event, $params) {

        try {
            $webhooks = erLhcoreClassModelChatWebhook::getList(array('filter' => array('event' => $event, 'disabled' => 0)));
        } catch (Exception $e) {
            return;
        }

        if (!empty($webhooks)) {
            foreach ($webhooks as $webhook) {

                // processTrigger always requires a chat so fake it.
                if (!isset($params['chat']) || !($params['chat'] instanceof erLhcoreClassModelChat)) {
                    $params['chat'] = new erLhcoreClassModelChat();
                    $params['chat']->id = -1;
                }

                if (self::isValidConditions($webhook, $params['chat']) === true) {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($webhook->trigger_id);
                    if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                        erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, false, array('args' => $params));
                    }
                } elseif ($webhook->trigger_id_alt > 0) {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($webhook->trigger_id_alt);
                    if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                        erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, false, array('args' => $params));
                    }
                }
            }
        }
    }

    public function isValidConditions($continuousHook, $chat) {

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

                        $valueAttr = $conditionsCurrent['value'];

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

                        // Remove spaces
                        $conditionAttr = preg_replace('/\s+/', '', $conditionAttr);
                        $valueAttr = preg_replace('/\s+/', '', $valueAttr);

                        // Allow only mathematical operators
                        $conditionAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $conditionAttr);
                        $valueAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $valueAttr);

                        if ($conditionAttrMath != '' && $conditionAttrMath == $conditionAttr) {
                            // Evaluate if there is mathematical rules
                            eval('$conditionAttr = ' . $conditionAttrMath . ";");
                        }

                        if ($valueAttrMath != '' && $valueAttrMath == $valueAttr) {
                            // Evaluate if there is mathematical rules
                            eval('$valueAttr = ' . $valueAttrMath . ";");
                        }

                        if ($conditionsCurrent['condition'] == 'eq' && ($conditionAttr == $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'lt' && ($conditionAttr < $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'lte' && ($conditionAttr <= $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['condition'] == 'neq' && ($conditionAttr != $valueAttr)) {
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