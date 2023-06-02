<?php

class erLhcoreClassGenericBotActionConditions {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['conditions']) && is_array($action['content']['conditions']) && !empty($action['content']['conditions']) ) {

            $params['current_trigger'] = $trigger;

            if (!isset($params['first_trigger'])) {
                $params['first_trigger'] = $params['current_trigger'];
            }
            
            $conditionsMet = true;

            $chatVariables = $chat->chat_variables_array;

            if (!empty($chat->additional_data)){
                $chatAttributes = (array)json_decode($chat->additional_data,true);
            } else {
                $chatAttributes = array();
            }

            $chatAttributesFrontend = array();
            foreach ($chatAttributes as $attr) {
                if (isset($attr['identifier'])) {
                    $chatAttributesFrontend[$attr['identifier']] = $attr['value'];
                }
            }

            $conditionsDebug = [];

            foreach ($action['content']['conditions'] as $condition) {

                if (isset($multiAttr)) {
                    unset($multiAttr);
                }

                if (isset($condition['content']['attr']) && $condition['content']['attr'] != '' &&
                    isset($condition['content']['comp']) && $condition['content']['comp'] != '')
                {
                    $attr = null;
                    $valAttr = isset($condition['content']['val']) ? $condition['content']['val'] : null;

                    $paramsConditions = explode('.',$condition['content']['attr']);

                    if ($paramsConditions[0] == 'lhc') {
                        $attr = $chat->{$paramsConditions[1]};
                    } elseif (in_array($paramsConditions[0],['chat_files','operator_files','user_files'])) {

                        $multiAttr = [];
                        $filter = ['filter' => ['chat_id' => $chat->id]];

                        if ($paramsConditions[0] == 'operator_files') {
                            $filter['filtergt']['user_id'] = 0;
                        }

                        if ($paramsConditions[0] == 'user_files') {
                            $filter['filter']['user_id'] = 0;
                        }

                        foreach (erLhcoreClassModelChatFile::getList($filter) as $file) {
                            $multiAttr[] = $file->extension;
                        }

                        $attr = implode(',',$multiAttr);

                    } elseif ($paramsConditions[0] == 'siteaccess') {
                        $attr = erLhcoreClassSystem::instance()->SiteAccess;
                    } elseif ($paramsConditions[0] == 'online_department_hours') {
                        $attr = erLhcoreClassChat::isOnline($chat->dep_id,false, array(
                            'exclude_bot' => true,
                            'exclude_online_hours' => false,
                            'ignore_user_status' => true
                        )) == true ? 1 : 0;
                        $valAttr = (int)$valAttr;
                    } elseif ($paramsConditions[0] == 'online_department') {
                        $attr = erLhcoreClassChat::isOnline($chat->dep_id,false, array(
                            'exclude_bot' => true,
                            'exclude_online_hours' => false
                        )) == true ? 1 : 0;
                        $valAttr = (int)$valAttr;
                    } elseif ($paramsConditions[0] == 'online_op_department') {
                        $attr = erLhcoreClassChat::isOnline($chat->dep_id,false, array(
                            'exclude_bot' => true,
                            'exclude_online_hours' => true
                        )) == true ? 1 : 0;
                        $valAttr = (int)$valAttr;
                    } elseif (isset($chatVariables[$condition['content']['attr']])) {
                        $attr = $chatVariables[$condition['content']['attr']];
                    } elseif (isset($chatAttributesFrontend[$condition['content']['attr']])) {
                        $attr = $chatAttributesFrontend[$condition['content']['attr']];
                    } elseif (strpos($condition['content']['attr'],'{validation_event__') !== false) {
                        $attr = str_replace(['{validation_event__','}'],'',$condition['content']['attr']);
                        $result = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_event_handler', array_merge($params,array('render' => $attr, 'chat' => $chat)));
                        $attr = isset($result['validation_result']) ? $result['validation_result'] : null;
                    } elseif (strpos($condition['content']['attr'],'{args.') !== false) {
                        $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array_merge($params,array('chat' => $chat)), str_replace(array('{args.','{','}'),'',$condition['content']['attr']), '.');
                        $attr = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
                    } elseif ($paramsConditions[0] == '{condition') {
                        $attr = erLhcoreClassGenericBotWorkflow::translateMessage($condition['content']['attr'], array('chat' => $chat, 'args' => ['chat' => $chat]));
                    } else {
                        $attrData = erLhcoreClassGenericBotActionRestapi::extractAttribute($chatVariables, $condition['content']['attr']);
                        if ($attrData['found'] == true) {
                            $attr = $attrData['value'];
                        } else {
                            $attr = '';
                        }
                    }


                    if ($attr === null) {
                       $conditionsMet = false;
                       break;
                    }

                    if (empty($attr) && isset($params['replace_array']) && !empty($params['replace_array'])) {
                        $attr = str_replace(array_keys($params['replace_array']),array_values($params['replace_array']),$condition['content']['attr']);
                    }

                    // Replace right side of the attribute
                    if (strpos($valAttr,'{args.') !== false) {
                        $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array_merge($params,array('chat' => $chat)), str_replace(array('{args.','{','}'),'',$valAttr), '.');
                        $valAttr = $valueAttribute['found'] == true ? $valueAttribute['value'] : $valAttr;
                    }

                    $replaceArray = array(
                        '{time}' => time()
                    );

                    // Remove internal variables
                    $attr = str_replace(array_keys($replaceArray), array_values($replaceArray),$attr);
                    $valAttr = str_replace(array_keys($replaceArray), array_values($replaceArray),$valAttr);

                    if (!in_array($condition['content']['comp'],['like','notlike','contains'])) {
                        // Remove spaces only if it's not like operator
                        $attr = preg_replace('/\s+/', '', $attr);
                        $valAttr = preg_replace('/\s+/', '', $valAttr);
                    }

                    // Allow only mathematical operators
                    $conditionAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $attr);
                    $valueAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $valAttr);

                    if ($conditionAttrMath != '' && $conditionAttrMath == $attr) {
                        // Evaluate if there is mathematical rules
                        try {
                            eval('$attr = ' . $conditionAttrMath . ";");
                        } catch (ParseError $e) {
                            // Do nothing
                        }
                    }

                    if ($valueAttrMath != '' && $valueAttrMath == $valAttr) {
                        // Evaluate if there is mathematical rules
                        try {
                            eval('$valAttr = ' . $valueAttrMath . ";");
                        } catch (ParseError $e) {
                            // Do nothing
                        }
                    }

                    // For these operations we want numbers
                    if (in_array($condition['content']['comp'],['lt','lte','gt','gte'])) {
                        $attr = round((float)$attr,3);
                        $valAttr = round((float)$valAttr,3);
                    } elseif ((is_string($attr) || is_numeric($attr)) && (is_string($valAttr) || is_numeric($valAttr))) {
                        $attr = (string)$attr;
                        $valAttr = (string)$valAttr;
                    }

                    $conditionsDebug[] = json_encode($attr) . ' ' . $condition['content']['comp'] . ' ' . json_encode($valAttr);

                    if ($condition['content']['comp'] == 'eq' && !((isset($multiAttr) && in_array($valAttr,$multiAttr)) || (!isset($multiAttr) && $attr == $valAttr))) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'lt' && !((isset($multiAttr) && count($multiAttr) < $valAttr) || (!isset($multiAttr) && $attr < $valAttr))) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'lte' && !((isset($multiAttr) && count($multiAttr) <= $valAttr) ||(!isset($multiAttr) && $attr <= $valAttr))) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'neq' && !((isset($multiAttr) && count($multiAttr) != $valAttr) || (!isset($multiAttr) && $attr != $valAttr))) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'gte' && !((isset($multiAttr) && count($multiAttr) >= $valAttr) || (!isset($multiAttr) && $attr >= $valAttr))) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'gt' && !((isset($multiAttr) && count($multiAttr) > $valAttr) || (!isset($multiAttr) && $attr > $valAttr))) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                            'pattern' => $valAttr,
                            'msg' => $attr,
                            'words_typo' => 0,
                        ))['found'] == false) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                            'pattern' => $valAttr,
                            'msg' => $attr,
                            'words_typo' => 0,
                        ))['found'] == true) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'contains' && !((isset($multiAttr) && !empty(array_intersect($multiAttr,explode(',',$valAttr)))) || (!isset($multiAttr) && strrpos($attr, $valAttr) !== false))) {
                        $conditionsMet = false;
                        break;
                    }
                }
            }

            erLhcoreClassGenericBotWorkflow::$triggerNameDebug[] = $conditionsDebug;

            if ($conditionsMet == true) {

                if (isset($action['content']['attr_options']['callback_reschedule']) && is_numeric($action['content']['attr_options']['callback_reschedule']) && $action['content']['attr_options']['callback_reschedule'] > 0) {
                    $pendingAction = new erLhcoreClassModelGenericBotPendingEvent();
                    $pendingAction->chat_id = $chat->id;
                    $pendingAction->trigger_id = $action['content']['attr_options']['callback_reschedule'];
                    $pendingAction->saveThis();
                }

                return array (
                    'status' => ((isset($action['content']['attr_options']['continue_all']) && $action['content']['attr_options']['continue_all'] == true) ? 'continue_all' : 'stop'),
                    'trigger_id' => ((isset($action['content']['attr_options']['callback_match']) && is_numeric($action['content']['attr_options']['callback_match'])) ? $action['content']['attr_options']['callback_match'] : null)
                );

            } else {

                if (isset($action['content']['attr_options']['callback_unreschedule']) && is_numeric($action['content']['attr_options']['callback_unreschedule']) && $action['content']['attr_options']['callback_unreschedule'] > 0) {
                    $pendingAction = new erLhcoreClassModelGenericBotPendingEvent();
                    $pendingAction->chat_id = $chat->id;
                    $pendingAction->trigger_id = $action['content']['attr_options']['callback_unreschedule'];
                    $pendingAction->saveThis();
                }

                if (isset($action['content']['attr_options']['callback_unmatch']) && is_numeric($action['content']['attr_options']['callback_unmatch'])){
                    return array(
                        'status' => ((isset($action['content']['attr_options']['continue_all']) && $action['content']['attr_options']['continue_all'] == true) ? 'continue_all' : 'stop'),
                        'trigger_id' => ((isset($action['content']['attr_options']['callback_unmatch']) && is_numeric($action['content']['attr_options']['callback_unmatch'])) ? $action['content']['attr_options']['callback_unmatch'] : null)
                    );
                }
            }
        }
    }
}

?>