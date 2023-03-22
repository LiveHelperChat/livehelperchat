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
            'conditions' => $this->conditions,
            'active_from' => $this->active_from,
            'active_to' => $this->active_to,
            'repetitiveness' => $this->repetitiveness,
            'days_activity' => $this->days_activity,
            'time_zone' => $this->time_zone,
        );
    }

    public function __get($var)
    {
        switch ($var) {

            case 'days_activity_array':
            case 'conditions_array':
                $varSystem = str_replace('_array','', $var);
                $jsonData = json_decode($this->{$varSystem},true);
                if ($jsonData !== null) {
                    $this->$var = $jsonData;
                } else {
                    $this->$var = $this->$varSystem;
                }

                if (!is_array($this->$var)) {
                    $this->$var = array();
                }

                return $this->$var;

            case 'is_active':
                $this->is_active = false;

                if ($this->repetitiveness == 0) {
                    $this->is_active = true;
                } elseif ($this->repetitiveness == 1) {

                    $dayShort = array(
                        1 => 'mod',
                        2 => 'tud',
                        3 => 'wed',
                        4 => 'thd',
                        5 => 'frd',
                        6 => 'sad',
                        7 => 'sud'
                    );

                    $daysActivity = $this->days_activity_array;

                    $dateTime = new DateTime('now',($this->time_zone != '' ? new DateTimeZone($this->time_zone) : null));

                    if (
                        isset($daysActivity[$dayShort[$dateTime->format('N')]]['start']) &&
                        isset($daysActivity[$dayShort[$dateTime->format('N')]]['end']) &&
                        (int)$daysActivity[$dayShort[$dateTime->format('N')]]['start'] <= (int)$dateTime->format('Hi') &&
                        (int)$daysActivity[$dayShort[$dateTime->format('N')]]['end'] >= (int)$dateTime->format('Hi')
                    ) {
                        $this->is_active = true;
                    }

                } elseif ($this->repetitiveness == 2) {
                    $this->is_active = $this->active_from <= time() && $this->active_to >= time();
                } elseif ($this->repetitiveness == 3) {
                    $dateTime = new DateTime('now',($this->time_zone != '' ? new DateTimeZone($this->time_zone) : null));

                    $dateTime->setTimestamp((int)$this->active_from);
                    $fromCompare = $dateTime->format('mdHi');

                    $dateTime->setTimestamp((int)$this->active_to);
                    $toCompare = $dateTime->format('mdHi');

                    $dateTime->setTimestamp(time());
                    $currentCompare = $dateTime->format('mdHi');

                    $this->is_active = (int)$fromCompare <= (int)$currentCompare && (int)$toCompare >= (int)$currentCompare;
                }

                return $this->is_active;

            case 'active_from_edit':
                $activeFromTS = $this->active_from > 0 ? $this->active_from : time();
                $dateTime = new DateTime('now',($this->time_zone != '' ? new DateTimeZone($this->time_zone) : null));
                $dateTime->setTimestamp((int)$activeFromTS);
                $this->active_from_edit = $dateTime->format('Y-m-d\TH:i');
                return $this->active_from_edit;

            case 'active_to_edit':
                $activeFromTS = $this->active_to > 0 ? $this->active_to : time();
                $dateTime = new DateTime('now',($this->time_zone != '' ? new DateTimeZone($this->time_zone) : null));
                $dateTime->setTimestamp((int)$activeFromTS);
                $this->active_to_edit = $dateTime->format('Y-m-d\TH:i');
                return $this->active_to_edit;

            default:
                break;
        }
    }

    public function getValueReplace($params)
    {
        $value = $this->default;
        $conditionArray = $this->conditions_array;

        uasort($conditionArray, function ($a, $b) {
            return (isset($a['priority']) && isset($b['priority']) && $a['priority'] < $b['priority']) ? 1 : 0;
        });

        if (!isset($params['user'])) {
            $params['user'] = new stdClass();
        }

        foreach ($conditionArray as $condition) {
            // Check if department matches
            if (
                (isset($condition['dep_id']) && $condition['dep_id'] > 0 && $condition['dep_id'] != $params['chat']->dep_id && (!isset($condition['dep_ids']) || empty($condition['dep_ids']))) ||
                (isset($condition['dep_ids']) && !empty($condition['dep_ids']) && !in_array((string)$params['chat']->dep_id,$condition['dep_ids']))
            ) {
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

                        if (!in_array($conditionsCurrent['comparator'],['like','notlike','contains'])) {
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
                        } else if ($conditionsCurrent['comparator'] == 'contains' && strrpos($conditionAttr,$valueAttr) !== false) {
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

            if ($isValid === true) {
                if (isset($condition['cannedRepeatPeriod']) && $condition['cannedRepeatPeriod'] == \erLhcoreClassModelCannedMsg::REP_DAILY) {

                    $isValid = false;

                    $dayShort = array(
                        1 => 'mod',
                        2 => 'tud',
                        3 => 'wed',
                        4 => 'thd',
                        5 => 'frd',
                        6 => 'sad',
                        7 => 'sud'
                    );

                    $dateTime = new DateTime('now', (isset($condition['time_zone']) && $condition['time_zone'] != '' ? new DateTimeZone($condition['time_zone']) : null));

                    if (isset($condition[$dayShort[$dateTime->format('N')].'StartTime']) &&
                        isset($condition[$dayShort[$dateTime->format('N')] . 'EndTime'])) {

                        $dateTimeStart = new DateTime($condition[$dayShort[$dateTime->format('N')].'StartTime'], new DateTimeZone('UTC'));
                        if (isset($condition['time_zone']) && $condition['time_zone'] != '') {
                            $dateTimeStart->setTimezone(new DateTimeZone($condition['time_zone']));
                        }

                        $dateTimeEnd =  new DateTime($condition[$dayShort[$dateTime->format('N')].'EndTime'], new DateTimeZone('UTC'));
                        if (isset($condition['time_zone']) && $condition['time_zone'] != '') {
                            $dateTimeEnd->setTimezone(new DateTimeZone($condition['time_zone']));
                        }

                        $isValid = (int)$dateTimeStart->format('Hi') <= (int)$dateTime->format('Hi') && (int)$dateTimeEnd->format('Hi') >= (int)$dateTime->format('Hi');
                    }

                } elseif (isset($condition['cannedRepeatPeriod']) && $condition['cannedRepeatPeriod'] == \erLhcoreClassModelCannedMsg::REP_PERIOD) {
                    $isValid = false;

                    if (isset($condition['active_from']) &&
                        isset($condition['active_to'])) {

                        $dateTimeStart = new DateTime($condition['active_from'], new DateTimeZone('UTC'));
                        if (isset($condition['time_zone']) && $condition['time_zone'] != '') {
                            $dateTimeStart->setTimezone(new DateTimeZone($condition['time_zone']));
                        }

                        $dateTimeEnd = new DateTime($condition['active_to'],new DateTimeZone('UTC'));
                        if (isset($condition['time_zone']) && $condition['time_zone'] != '') {
                            $dateTimeEnd->setTimezone(new DateTimeZone($condition['time_zone']));
                        }

                        $isValid = $dateTimeStart->getTimestamp() <= time() && $dateTimeEnd->getTimestamp() >= time();
                    }

                } elseif (isset($condition['cannedRepeatPeriod']) && $condition['cannedRepeatPeriod'] == \erLhcoreClassModelCannedMsg::REP_PERIOD_REP) {

                    $dateTime = new DateTime('now', (isset($condition['time_zone']) && $condition['time_zone'] != '' ? new DateTimeZone($condition['time_zone']) : null));

                    $dateTimeStart = new DateTime($condition['active_from'],  new DateTimeZone('UTC'));
                    if (isset($condition['time_zone']) && $condition['time_zone'] != '') {
                        $dateTimeStart->setTimezone(new DateTimeZone($condition['time_zone']));
                    }

                    $fromCompare = $dateTimeStart->format('mdHi');

                    $dateTimeTo = new DateTime($condition['active_to'], new DateTimeZone('UTC') );
                    if (isset($condition['time_zone']) && $condition['time_zone'] != '') {
                        $dateTimeTo->setTimezone(new DateTimeZone($condition['time_zone']));
                    }

                    $toCompare = $dateTimeTo->format('mdHi');

                    $dateTime->setTimestamp(time());
                    $currentCompare = $dateTime->format('mdHi');

                    $isValid = (int)$fromCompare <= (int)$currentCompare && (int)$toCompare >= (int)$currentCompare;
                }
            }

            // Group is valid we can execute bot and trigger against specific chat
            if ($isValid === true) {
                $value = $condition['value'];
                break;
            }
        }

        if (strpos($value,'{') !== false) {
            return erLhcoreClassGenericBotWorkflow::translateMessage($value, array('chat' => $params['chat'], 'args' => $params));
        } else {
            return $value;
        }

    }

    public $id = null;
    public $identifier = '';
    public $default = '';
    public $conditions = '';

    // Activity data
    public $active_from = '';
    public $active_to = '';
    public $repetitiveness = '';
    public $days_activity = '';
    public $time_zone = '';



}

?>