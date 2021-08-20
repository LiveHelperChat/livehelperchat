<?php

class erLhcoreClassModelCannedMsgReplace
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_canned_msg_replace';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'identifier' => $this->identifier,
            'default' => $this->default,
            'conditions' => $this->conditions
        );
    }

    public function __get($var)
    {
        switch ($var) {

            case 'conditions_array':
                $jsonData = json_decode($this->conditions, true);
                if ($jsonData !== null) {
                    $this->conditions_array = $jsonData;
                } else {
                    $this->conditions_array = $this->conditions;
                }

                if (!is_array($this->conditions_array)) {
                    $this->conditions_array = array();
                }

                return $this->conditions_array;

            default:
                break;
        }
    }

    public function getValueReplace($params)
    {
        $value = $this->default;
        $conditionArray = $this->conditions_array;

        uasort($conditionArray, function ($a, $b) {
            return isset($a['priority']) && isset($b['priority']) && $a['priority'] < $b['priority'];
        });

        if (!isset($params['user'])) {
            $params['user'] = new stdClass();
        }

        foreach ($conditionArray as $condition) {
            // Check if department matches
            if (isset($condition['dep_id']) && $condition['dep_id'] > 0 && $condition['dep_id'] != $params['chat']->dep_id) {
                continue;
            }
            // We do final check here
            $isValid = true;

            if (isset($condition['conditions'])) {
                $groupedConditions = [];
                $conditionItems = $condition['conditions'];

                foreach ($condition['conditions'] as $indexCondition => $conditionItem) {
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

                foreach ($groupedConditions as $groupedConditionItems) {
                    $isValidSubItem = false;
                    foreach ($groupedConditionItems as $groupedConditionItem) {
                        $conditionsCurrent = $conditionItems[$groupedConditionItem];

                        $conditionItemValid = false;

                        $conditionAttr = $conditionsCurrent['field'];
                        if (strpos($conditionAttr, '{args.') !== false) {
                            $matchesValues = array();
                            preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $conditionAttr, $matchesValues);
                            if (!empty($matchesValues[0])) {
                                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                                    $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array('user' => $params['user'], 'chat' => $params['chat']), $matchesValues[1][$indexElement], '.');
                                    $conditionAttr = str_replace($elementValue, $valueAttribute['found'] == true ? $valueAttribute['value'] : 0, $conditionAttr);
                                }
                            }
                        }

                        $valueAttr = $conditionsCurrent['value'];

                        if (strpos($valueAttr, '{args.') !== false) {
                            $matchesValues = array();
                            preg_match_all('~\{args\.((?:[^\{\}\}]++|(?R))*)\}~', $valueAttr, $matchesValues);
                            if (!empty($matchesValues[0])) {
                                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                                    $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array('user' => $params['user'], 'chat' => $params['chat']), $matchesValues[1][$indexElement], '.');
                                    $valueAttr = str_replace($elementValue, $valueAttribute['found'] == true ? $valueAttribute['value'] : 0, $valueAttr);
                                }
                            }
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
                            eval('$conditionAttr = ' . $conditionAttrMath . ";");
                        }

                        if ($valueAttrMath != '' && $valueAttrMath == $valueAttr) {
                            // Evaluate if there is mathematical rules
                            eval('$valueAttr = ' . $valueAttrMath . ";");
                        }

                        if ($conditionsCurrent['comparator'] == 'eq' && ($conditionAttr == $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['comparator'] == 'lt' && ($conditionAttr < $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['comparator'] == 'lte' && ($conditionAttr <= $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['comparator'] == 'neq' && ($conditionAttr != $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['comparator'] == 'gte' && ($conditionAttr >= $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['comparator'] == 'gt' && ($conditionAttr > $valueAttr)) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['comparator'] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                                'pattern' => $valueAttr,
                                'msg' => $conditionAttr,
                                'words_typo' => 0,
                            ))['found'] == true) {
                            $conditionItemValid = true;
                        } else if ($conditionsCurrent['comparator'] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                                'pattern' => $valueAttr,
                                'msg' => $conditionAttr,
                                'words_typo' => 0,
                            ))['found'] == false) {
                            $conditionItemValid = true;
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
            }

            // Group is valid we can execute bot and trigger against specific chat
            if ($isValid === true) {
                return $condition['value'];
            }
        }

        return $value;
    }

    public $id = null;
    public $identifier = '';
    public $default = '';
    public $conditions = '';
}

?>