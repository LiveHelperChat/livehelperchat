<?php

class erLhcoreClassChatWebhookContinuous {

    public static function processEvent() {

        try {
            $continuousHooks = erLhcoreClassModelChatWebhook::getList(array('filter' => array('type' => 1, 'disabled' => 0)));
        } catch (Exception $e) {
            return;
        }

        $statusValid = array(
            erLhcoreClassModelChat::STATUS_PENDING_CHAT,
            erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
            erLhcoreClassModelChat::STATUS_BOT_CHAT,
        );

        $chats = erLhcoreClassModelChat::getList(array(
            'limit' => false,
            'filterin' => array('status' => $statusValid)));

        $db = ezcDbInstance::get();

        $chatsApplied = array();

        foreach ($continuousHooks as $continuousHook) {
            $configurationParams = $continuousHook->conditions_array;

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

                // We iterate through each chat and check against it
                foreach ($chats as $chat) {

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
                                $paramsMessage = array('limit' => 1, 'sort' => 'id DESC', 'filter' => array('chat_id' => $chat->id), 'filternotin' => array('user_id' => array(-1)));
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
                        $chatsApplied[$continuousHook->id][] = $chat->id;
                        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($continuousHook->trigger_id);
                        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                            $db->beginTransaction();
                            $chat = erLhcoreClassModelChat::fetchAndLock($chat->id);
                            if ($chat instanceof erLhcoreClassModelChat && in_array($chat->status,$statusValid)) {
                                // processTrigger always requires a chat so fake it.
                                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, false, array('args' => array('chat' => $chat)));
                            }
                            $db->commit();
                        }
                    }
                }
            }

            // Execute alternative trigger if to chat was not applied matching trigger
            if ($continuousHook->trigger_id_alt > 0) {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($continuousHook->trigger_id_alt);
                if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                    foreach ($chats as $chat) {
                        if (!isset($chatsApplied[$continuousHook->id]) || !in_array($chat->id,$chatsApplied[$continuousHook->id])) {
                            $db->beginTransaction();
                            $chat = erLhcoreClassModelChat::fetchAndLock($chat->id);
                            if ($chat instanceof erLhcoreClassModelChat && in_array($chat->status,$statusValid)) {
                                // processTrigger always requires a chat so fake it.
                                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, false, array('args' => array('chat' => $chat)));
                            }
                            $db->commit();
                        }
                    }
                }
            }

            if (isset($chatsApplied[$continuousHook->id])) {
                unset($chatsApplied[$continuousHook->id]);
            }
        }
    }
}

?>