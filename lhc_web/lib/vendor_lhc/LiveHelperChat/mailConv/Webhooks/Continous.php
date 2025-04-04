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
                foreach ($conditionItems as $indexCondition => $conditionItem) {
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
                            if (strpos($conditionAttr, '{args.') !== false) {
                                $validLeftConditions = false;
                                $matchesValues = array();
                                preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $conditionAttr, $matchesValues);
                                if (!empty($matchesValues[0]) && count($matchesValues[0]) == 1) {
                                    $attributeCompare = str_replace('chat.', '', $matchesValues[1][0]);
                                    if (in_array($attributeCompare, $validSQLAttributes)) {
                                        $SQLLeftConditions = $validLeftConditions = true;
                                        $conditionAttr = $attributeCompare;
                                    }
                                }
                            } elseif (strpos($conditionAttr,'{condition.') !== false) {
                                $validLeftConditions = false;
                            }

                            // Not SQL based condition
                            if ($validLeftConditions == false) {
                                continue;
                            }

                            $SQLRightConditions = false;
                            $valueAttr = $conditionsCurrent['value'];
                            if (strpos($valueAttr, '{args.') !== false) {
                                $validLeftConditions = false;
                                $matchesValues = array();
                                preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $valueAttr, $matchesValues);
                                if (!empty($matchesValues[0]) && count($matchesValues[0]) == 1) {
                                    $attributeCompareRight = str_replace('chat.', '', $matchesValues[1][0]);
                                    if (in_array($attributeCompareRight, $validSQLAttributes)) {
                                        $validLeftConditions = true;
                                        $SQLRightConditions = true;
                                        $valueAttr = $attributeCompareRight;
                                    }
                                }
                            } elseif (strpos($valueAttr,'{condition.') !== false) {
                                $validLeftConditions = false;
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
                                $valueAttr = str_replace(array_keys($replaceArray), array_values($replaceArray), $valueAttr);
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
                            } else if ($conditionsCurrent['condition'] == 'notlike') {
                                $filterPrepared['filternotlikefields'][][$conditionAttr] = $valueAttr;
                            } else if ($conditionsCurrent['condition'] == 'contains') {
                                $filterPrepared['filterinfields'][][$conditionAttr] = explode(',', $valueAttr);
                            }
                        }
                    }
                }

                $filterPrepared['limit'] = 100;
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
                            } elseif ($conditionsCurrent['type'] == '3') { // No response from operator for n seconds
                                $conditionAttr = $conditionsCurrent['attr'];
                                if (strpos($conditionAttr, '{args.') !== false) {
                                    $matchesValues = array();
                                    preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $conditionAttr, $matchesValues);
                                    if (!empty($matchesValues[0])) {
                                        foreach ($matchesValues[0] as $indexElement => $elementValue) {
                                            $valueAttribute = \erLhcoreClassGenericBotActionRestapi::extractAttribute(array('chat' => $chat), $matchesValues[1][$indexElement], '.');
                                            $conditionAttr = str_replace($elementValue, $valueAttribute['found'] == true ? $valueAttribute['value'] : 0, $conditionAttr);
                                        }
                                    }
                                } elseif (strpos($conditionAttr,'{condition.') !== false) {
                                    $conditionAttr = \erLhcoreClassGenericBotWorkflow::translateMessage($conditionAttr, array('chat' => $chat, 'args' => ['chat' => $chat]));
                                }

                                $valueAttr = $conditionsCurrent['value'];

                                if (strpos($valueAttr, '{args.') !== false) {
                                    $matchesValues = array();
                                    preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $valueAttr, $matchesValues);
                                    if (!empty($matchesValues[0])) {
                                        foreach ($matchesValues[0] as $indexElement => $elementValue) {
                                            $valueAttribute = \erLhcoreClassGenericBotActionRestapi::extractAttribute(array('chat' => $chat), $matchesValues[1][$indexElement], '.');
                                            $valueAttr = str_replace($elementValue, $valueAttribute['found'] == true ? $valueAttribute['value'] : 0, $valueAttr);
                                        }
                                    }
                                } elseif (strpos($valueAttr,'{condition.') !== false) {
                                    $valueAttr = \erLhcoreClassGenericBotWorkflow::translateMessage($valueAttr, array('chat' => $chat, 'args' => ['chat' => $chat]));
                                }

                                $replaceArray = array(
                                    '{time}' => time()
                                );

                                // Remove internal variables
                                $conditionAttr = str_replace(array_keys($replaceArray), array_values($replaceArray), $conditionAttr);
                                $valueAttr = str_replace(array_keys($replaceArray), array_values($replaceArray), $valueAttr);

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

                            if ($conditionItemValid == true) {
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

                        $stmt = $db->prepare("INSERT IGNORE INTO lh_mail_continuous_event (`webhook_id`,`message_id`,`created_at`) VALUES (:webhook_id, :message_id, :created_at)");
                        $stmt->bindValue(':webhook_id', $continuousHook->id, \PDO::PARAM_INT);
                        $stmt->bindValue(':message_id', $chat->id, \PDO::PARAM_INT);
                        $stmt->bindValue(':created_at', time(), \PDO::PARAM_INT);
                        $stmt->execute();
                        $wasSuccess = $stmt->rowCount();

                        if ($wasSuccess === 1) {
                            $trigger = \erLhcoreClassModelGenericBotTrigger::fetch($continuousHook->trigger_id);
                            if ($trigger instanceof \erLhcoreClassModelGenericBotTrigger) {
                                $db->beginTransaction();

                                // processTrigger always requires a chat so fake it.
                                \erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, false, array('args' => array('conversation' => $chat->conversation, 'mail' => $chat,'chat' => $chat)));

                                $db->commit();
                            }
                        }
                    }
                }
            }

            if (isset($chatsApplied[$continuousHook->id])) {
                unset($chatsApplied[$continuousHook->id]);
            }
        }

        // Delete month old records
        $db = \ezcDbInstance::get();
        $db->query("DELETE FROM `lh_mail_continuous_event` WHERE `created_at` < " . (time() - 31 * 24 * 3600));
    }

    public static function getProcessedMailEvents($webhook)
    {
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare("SELECT COUNT(`webhook_id`) FROM `lh_mail_continuous_event` WHERE `webhook_id` = :webhook_id");
        $stmt->bindValue(':webhook_id', $webhook->id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function getLast10Events($webhook)
    {
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare("SELECT * FROM `lh_mail_continuous_event` WHERE `webhook_id` = :webhook_id ORDER BY `created_at` DESC");
        $stmt->bindValue(':webhook_id', $webhook->id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
