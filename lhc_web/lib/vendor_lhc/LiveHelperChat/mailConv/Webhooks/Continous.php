<?php

namespace LiveHelperChat\mailConv\Webhooks;

class Continous
{
    public static function processEventMail()
    {
        try {
            $continuousHooks = \erLhcoreClassModelChatWebhook::getList(array('filter' => array('type' => 2, 'disabled' => 0)));
        } catch (\Exception $e) {
            return;
        }

        // Continue here
        $statusValid = array(
            \erLhcoreClassModelMailconvMessage::STATUS_PENDING,
            \erLhcoreClassModelMailconvMessage::STATUS_ACTIVE
        );

        $db = \ezcDbInstance::get();

        $chatsApplied = array();

        $validSQLAttributes = array_keys((new \erLhcoreClassModelMailconvMessage())->getState());

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

                $filterPrepared = [];
                foreach ($groupedConditions as $groupedConditionItems) {
                    $isValidSubItem = false;
                    foreach ($groupedConditionItems as $groupedConditionItem) {
                        $conditionsCurrent = $conditionItems[$groupedConditionItem];
                        if ($conditionsCurrent['type'] == '3') {

                            $SQLLeftConditions = false;
                            $validLeftConditions = true;
                            $conditionAttr = $conditionsCurrent['attr'];
                            if (strpos($conditionAttr,'{args.') !== false) {
                                $validLeftConditions = false;
                                $matchesValues = array();
                                preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $conditionAttr,$matchesValues);
                                if (!empty($matchesValues[0]) && count($matchesValues[0]) == 1) {
                                    $attributeCompare = str_replace('chat.','',$matchesValues[1][0]);
                                    if (in_array($attributeCompare, $validSQLAttributes)) {
                                        $SQLLeftConditions = $validLeftConditions = true;
                                        $conditionAttr = $attributeCompare;
                                    }
                                }
                            }

                            // Not SQL based condition
                            if ($validLeftConditions == false) {
                                continue;
                            }

                            $SQLRightConditions = false;
                            $valueAttr = $conditionsCurrent['value'];
                            if (strpos($valueAttr,'{args.') !== false) {
                                $validLeftConditions = false;
                                $matchesValues = array();
                                preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $valueAttr,$matchesValues);
                                if (!empty($matchesValues[0]) && count($matchesValues[0]) == 1) {
                                    $attributeCompareRight = str_replace('chat.','',$matchesValues[1][0]);
                                    if (in_array($attributeCompareRight, $validSQLAttributes)) {
                                        $validLeftConditions = true;
                                        $SQLRightConditions = true;
                                        $valueAttr = $attributeCompareRight;
                                    }
                                }
                            }

                            // Not SQL based condition
                            if ($validLeftConditions == false) {
                                continue;
                            }

                            $replaceArray = array(
                                '{time}' => time()
                            );

                            // Remove internal variables
                            if ($SQLLeftConditions === false) {
                                $conditionAttr = str_replace(array_keys($replaceArray), array_values($replaceArray), $conditionAttr);
                            }

                            if ($SQLRightConditions === false) {
                                $valueAttr = str_replace(array_keys($replaceArray), array_values($replaceArray),$valueAttr);
                            }

                            // Remove spaces
                            if ($SQLLeftConditions === false) {
                                $conditionAttr = preg_replace('/\s+/', '', $conditionAttr);
                            }

                            if ($SQLRightConditions === false) {
                                $valueAttr = preg_replace('/\s+/', '', $valueAttr);
                            }

                            if ($SQLLeftConditions === false) {
                                $conditionAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $conditionAttr);
                                if ($conditionAttrMath != '' && $conditionAttrMath == $conditionAttr) {
                                    // Evaluate if there is mathematical rules
                                    try {
                                        eval('$conditionAttr = ' . $conditionAttrMath . ";");
                                    } catch (ParseError $e) {
                                        // Do nothing
                                    }
                                }
                            }

                            if ($SQLRightConditions === false) {
                                $valueAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $valueAttr);
                                if ($valueAttrMath != '' && $valueAttrMath == $valueAttr) {
                                    // Evaluate if there is mathematical rules
                                    try {
                                        eval('$valueAttr = ' . $valueAttrMath . ";");
                                    } catch (ParseError $e) {
                                        // Do nothing
                                    }
                                }
                            }

                            if ($conditionsCurrent['condition'] == 'eq') {
                                $filterPrepared['filterfields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'lt') {
                                $filterPrepared['filterltfields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'lte') {
                                $filterPrepared['filterltefields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'neq') {
                                $filterPrepared['filternotfields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'gte') {
                                $filterPrepared['filtergtefields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'gt') {
                                $filterPrepared['filtergtfields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'like') {
                                $filterPrepared['filterlikefields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'notlike' ) {
                                $filterPrepared['filternotlikefields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'contains') {
                                $filterPrepared['filterinfield'][][$conditionAttr] = explode(',',$valueAttr);
                            }
                        }
                    }
                }

                $filterPrepared['limit'] = 5000;
                $filterPrepared['ignore_fields'] = ['body','alt_body','rfc822_body'];
                $filterPrepared['filter']['status'] = [\erLhcoreClassModelMailconvMessage::STATUS_PENDING, \erLhcoreClassModelMailconvMessage::STATUS_ACTIVE];

                $chats = \erLhcoreClassModelMailconvMessage::getList($filterPrepared);

                foreach ($chats as $chat) {

                        // This hook was already applied for specific chat. No point to check again
                        if (isset($chatsApplied[$continuousHook->id]) && in_array($chat->id, $chatsApplied[$continuousHook->id])) {
                            continue;
                        }

                        // We do final check here
                        $isValid = true;
                        foreach ($groupedConditions as $groupedConditionItems) {
                            $isValidSubItem = false;
                            foreach ($groupedConditionItems as $groupedConditionItem) {
                                $conditionsCurrent = $conditionItems[$groupedConditionItem];

                                $conditionItemValid = false;

                                if ($conditionsCurrent['type'] == '1') { // Visitor message contains
                                    // For that visitor should use event based events or contains just options
                                    /*$paramsMessage = array('limit' => 1, 'sort' => 'id DESC', 'filter' => array('chat_id' => $chat->id), 'filternotin' => array('user_id' => array(-1)));
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
                                    }*/
                                } elseif ($conditionsCurrent['type'] == '3') { // No response from operator for n seconds
                                    $conditionAttr = $conditionsCurrent['attr'];
                                    if (strpos($conditionAttr,'{args.') !== false) {
                                        $matchesValues = array();
                                        preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $conditionAttr,$matchesValues);
                                        if (!empty($matchesValues[0])) {
                                            foreach ($matchesValues[0] as $indexElement => $elementValue) {
                                                $valueAttribute = \erLhcoreClassGenericBotActionRestapi::extractAttribute(array('chat' => $chat), $matchesValues[1][$indexElement], '.');
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
                                                $valueAttribute = \erLhcoreClassGenericBotActionRestapi::extractAttribute(array('chat' => $chat), $matchesValues[1][$indexElement], '.');
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
                                    } else if ($conditionsCurrent['condition'] == 'like' && \erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                                            'pattern' => $valueAttr,
                                            'msg' => $conditionAttr,
                                            'words_typo' => 0,
                                        ))['found'] == true) {
                                        $conditionItemValid = true;
                                    } else if ($conditionsCurrent['condition'] == 'notlike' && \erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
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
                            $chatsApplied[$continuousHook->id][] = $chat->id;

                            $db = \ezcDbInstance::get();

                            $stmt = $db->prepare("INSERT IGNORE INTO lh_mail_continous_event (`webhook_id`,`message_id`) VALUES (:webhook_id, :message_id)");
                            $stmt->bindValue(':webhook_id',$continuousHook->id,\PDO::PARAM_INT);
                            $stmt->bindValue(':message_id',$chat->id,\PDO::PARAM_INT);
                            $stmt->execute();
                            $wasSuccess = $stmt->rowCount();

                            /*$trigger = erLhcoreClassModelGenericBotTrigger::fetch($continuousHook->trigger_id);
                            if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                $db->beginTransaction();
                                $chat = erLhcoreClassModelChat::fetchAndLock($chat->id);
                                if ($chat instanceof erLhcoreClassModelChat && in_array($chat->status,$statusValid)) {
                                    $paramsExecution = ['msg_last_id' => $chat->last_msg_id];

                                    // processTrigger always requires a chat so fake it.
                                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, false, array('args' => array('chat' => $chat)));

                                    self::dispatchEvents($chat, $paramsExecution);
                                }
                                $db->commit();
                            }*/
                        }
                    }
            }

            if (isset($chatsApplied[$continuousHook->id])) {
                unset($chatsApplied[$continuousHook->id]);
            }
        }
    }
}
