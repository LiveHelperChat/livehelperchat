<?php
class erLhcoreClassGenericBotActionConditions {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['conditions']) && is_array($action['content']['conditions']) && !empty($action['content']['conditions'])) {

            $params['current_trigger'] = $trigger;
            if (!isset($params['first_trigger'])) {
                $params['first_trigger'] = $params['current_trigger'];
            }
            $chatVariables = $chat->chat_variables_array;
            $chatAttributes = !empty($chat->additional_data) ? (array)json_decode($chat->additional_data, true) : [];
            $chatAttributesFrontend = [];
            foreach ($chatAttributes as $attr) {
                if (isset($attr['identifier'])) {
                    $chatAttributesFrontend[$attr['identifier']] = $attr['value'];
                }
            }
            erLhcoreClassGenericBotWorkflow::$triggerNameDebug = [];

            // Group conditions by start_or marker.
            $groups = [];
            $currentGroup = [];
            foreach ($action['content']['conditions'] as $condition) {
                if (isset($condition['content']['comp']) && $condition['content']['comp'] === 'start_or') {
                    // If a marker is found, push the current group if not empty.
                    if (!empty($currentGroup)) {
                        $groups[] = $currentGroup;
                    }
                    // Start new empty group.
                    $currentGroup = [];
                } else {
                    $currentGroup[] = $condition;
                }
            }
            if (!empty($currentGroup)) {
                $groups[] = $currentGroup;
            }


            $overallConditionsMet = false;
            // Evaluate each group: if at least one group meets all conditions, then overall is met.
            foreach ($groups as $groupConditions) {

                $conditionsMet = true;
                $conditionsDebug = [];

                foreach ($groupConditions as $condition) {

                    if (isset($multiAttr)) {
                        unset($multiAttr);
                    }

                    if (isset($condition['content']['attr']) && $condition['content']['attr'] != '' &&
                        isset($condition['content']['comp']) && $condition['content']['comp'] != '')
                    {
                        $attr = null;
                        $valAttr = isset($condition['content']['val']) ? $condition['content']['val'] : null;

                        $paramsConditions = explode('.', $condition['content']['attr']);

                        if ($paramsConditions[0] == 'lhc') {

                            if (
                                isset($action['content']['attr_options']['compare_live']) && $action['content']['attr_options']['compare_live'] === true &&
                                in_array($paramsConditions[1],array_keys($chat->getState()))
                            ) {
                                $db = ezcDbInstance::get();
                                $db->beginTransaction();
                                $chat->syncAndLock('`' . $paramsConditions[1] . '`');
                                $db->commit();
                            }

                            $attr = $chat->{$paramsConditions[1]};

                        } elseif (in_array($paramsConditions[0], ['chat_files','operator_files','user_files'])) {

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
                            $attr = implode(',', $multiAttr);

                        } elseif ($paramsConditions[0] == 'siteaccess') {
                            $attr = erLhcoreClassSystem::instance()->SiteAccess;
                        } elseif ($paramsConditions[0] == 'online_department_hours') {
                            $attr = erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                                'exclude_bot' => true,
                                'exclude_online_hours' => false,
                                'ignore_user_status' => true
                            )) ? 1 : 0;
                            $valAttr = (int)$valAttr;
                        } elseif ($paramsConditions[0] == 'online_department') {
                            $attr = erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                                'exclude_bot' => true,
                                'exclude_online_hours' => false
                            )) ? 1 : 0;
                            $valAttr = (int)$valAttr;
                        } elseif ($paramsConditions[0] == 'online_op_department') {
                            $attr = erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                                'exclude_bot' => true,
                                'exclude_online_hours' => true,
                                'include_users' => true
                            )) ? 1 : 0;
                            $valAttr = (int)$valAttr;
                        } elseif (isset($chatVariables[$condition['content']['attr']])) {
                            $attr = $chatVariables[$condition['content']['attr']];
                        } elseif (isset($chatAttributesFrontend[$condition['content']['attr']])) {
                            $attr = $chatAttributesFrontend[$condition['content']['attr']];
                        } elseif (strpos($condition['content']['attr'], '{validation_event__') !== false) {
                            $attr = str_replace(['{validation_event__', '}'], '', $condition['content']['attr']);
                            $result = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_event_handler', array_merge($params, array('render' => $attr, 'chat' => $chat)));
                            $attr = isset($result['validation_result']) ? $result['validation_result'] : '';
                        } elseif ($paramsConditions[0] == 'media_type') {
                            $attr = '';
                            
                            if (isset($params['msg']) && is_object($params['msg'])) {
                                $msg_text = $params['msg']->msg;
                                
                                // Extract file from message content like [file=1999_718792694da94d3018e51319471c09b5]
                                $matches = array();
                                preg_match_all('/\[file="?(.*?)"?\]/', $msg_text, $matches);
                                
                                if (!empty($matches[1])) {
                                    // Check if the file attachment is the sole content of the message
                                    $filePattern = '/\[file="?' . preg_quote($matches[1][0], '/') . '"?\]/';
                                    $msgWithoutFile = preg_replace($filePattern, '', $msg_text);
                                    $msgWithoutFile = trim($msgWithoutFile);
                                    
                                    // Only use file extension as attribute if message contains only the file
                                    if (empty($msgWithoutFile)) {
                                        $body = $matches[1][0]; // Get first file
                                        $parts = explode('_', $body);
                                        $fileID = $parts[0];
                                        $hash = $parts[1];
                                        
                                        try {
                                            $file = erLhcoreClassModelChatFile::fetch($fileID);
                                            if (is_object($file) && $hash == $file->security_hash) {
                                                $attr = $file->extension;
                                            }
                                        } catch (Exception $e) {
                                            // File not found or invalid
                                            $attr = '';
                                        }
                                    }
                                }
                            }

                        } elseif (strpos($condition['content']['attr'], '{args.') !== false) {
                            $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array_merge($params, array('chat' => $chat)), str_replace(array('{args.', '{', '}'), '', $condition['content']['attr']), '.');
                            $attr = $valueAttribute['found'] ? $valueAttribute['value'] : '';
                        } elseif ($paramsConditions[0] == '{condition') {
                            $attr = erLhcoreClassGenericBotWorkflow::translateMessage($condition['content']['attr'], array('rule_value' => $condition['content']['val'], 'chat' => $chat, 'args' => ['chat' => $chat]));
                        } else {
                            $attrData = erLhcoreClassGenericBotActionRestapi::extractAttribute($chatVariables, $condition['content']['attr']);
                            $attr = $attrData['found'] ? $attrData['value'] : '';
                        }

                        if ($attr === null) {
                            $attr = '';
                        }

                        if (empty($attr) && isset($params['replace_array']) && !empty($params['replace_array'])) {
                            $attr = $condition['content']['attr'];
                            foreach ($params['replace_array'] as $keyReplace => $valueReplace) {
                                if (is_object($valueReplace) || is_array($valueReplace)) {
                                    $attr = @str_replace($keyReplace, json_encode($valueReplace), $attr);
                                } else {
                                    $attr = @str_replace($keyReplace, $valueReplace, $attr);
                                }
                            }
                            if ($attr == $condition['content']['attr']) {
                                $attr = '';
                            }
                        }

                        if (strpos($valAttr, '{args.') !== false) {
                            $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array_merge($params, array('chat' => $chat)), str_replace(array('{args.', '{', '}'), '', $valAttr), '.');
                            $valAttr = $valueAttribute['found'] ? $valueAttribute['value'] : $valAttr;
                        }

                        $replaceArray = array('{time}' => time());
                        $attr = str_replace(array_keys($replaceArray), array_values($replaceArray), $attr);
                        $valAttr = str_replace(array_keys($replaceArray), array_values($replaceArray), $valAttr);

                        if (!in_array($condition['content']['comp'], ['like', 'notlike', 'contains', 'in_list', 'in_list_lowercase'])) {
                            $attr = preg_replace('/\s+/', '', $attr);
                            $valAttr = preg_replace('/\s+/', '', $valAttr);
                        }

                        $conditionAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $attr);
                        $valueAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $valAttr);

                        if ($conditionAttrMath != '' && $conditionAttrMath === $attr) {
                            try {
                                eval('$attr = ' . $conditionAttrMath . ";");
                            } catch (ParseError $e) { }
                        }
                        if ($valueAttrMath != '' && $valueAttrMath === $valAttr) {
                            try {
                                eval('$valAttr = ' . $valueAttrMath . ";");
                            } catch (ParseError $e) { }
                        }

                        if (in_array($condition['content']['comp'], ['lt', 'lte', 'gt', 'gte'])) {
                            $attr = round((float)$attr, 3);
                            $valAttr = round((float)$valAttr, 3);
                        } elseif ((is_string($attr) || is_numeric($attr)) && (is_string($valAttr) || is_numeric($valAttr))) {
                            $attr = (string)$attr;
                            $valAttr = (string)$valAttr;
                        }

                        $conditionsDebug[] = $condition['content']['attr'] . ' => ' .json_encode($attr) . ' ' . $condition['content']['comp'] . ' ' . json_encode($valAttr);

                        if ($condition['content']['comp'] == 'eq' && !((isset($multiAttr) && in_array($valAttr, $multiAttr)) || (!isset($multiAttr) && $attr == $valAttr))) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'lt' && !((isset($multiAttr) && count($multiAttr) < $valAttr) || (!isset($multiAttr) && $attr < $valAttr))) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'lte' && !((isset($multiAttr) && count($multiAttr) <= $valAttr) || (!isset($multiAttr) && $attr <= $valAttr))) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'neq' && !((isset($multiAttr) && count($multiAttr) != $valAttr) || (!isset($multiAttr) && $attr != $valAttr))) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'gte' && !((isset($multiAttr) && count($multiAttr) >= $valAttr) || (!isset($multiAttr) && $attr >= $valAttr))) {
                            $conditionsDebug[] = 'INVALID';
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
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                                'pattern' => $valAttr,
                                'msg' => $attr,
                                'words_typo' => 0,
                            ))['found'] == true) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'contains' && !((isset($multiAttr) && !empty(array_intersect($multiAttr, explode(',', trim($valAttr))))) || (!isset($multiAttr) && strrpos(trim($attr), trim($valAttr)) !== false))) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'notempty' && empty($attr)) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'isempty' && !empty($attr)) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'in_list' && !in_array(trim($attr),explode('||',trim($valAttr)))) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        } else if ($condition['content']['comp'] == 'in_list_lowercase' && !in_array(strtolower(trim($attr)),explode('||',strtolower(trim($valAttr))))) {
                            $conditionsDebug[] = 'INVALID';
                            $conditionsMet = false;
                            break;
                        }
                    }
                    $conditionsDebug[] = 'VALID';

                }
                erLhcoreClassGenericBotWorkflow::$triggerNameDebug[] = $conditionsDebug;

                if ($conditionsMet) {
                    erLhcoreClassGenericBotWorkflow::$triggerNameDebug[] = 'VALID';
                    $overallConditionsMet = true;
                    break;
                } else {
                    erLhcoreClassGenericBotWorkflow::$triggerNameDebug[] = 'INVALID';
                }
            }

            if ($overallConditionsMet === true) {

                // We want to log this always
                if (isset($action['content']['attr_options']['log_matched']) && $action['content']['attr_options']['log_matched'] === true) {
                    try {
                        erLhcoreClassGenericBotWorkflow::$triggerNameDebug[] = $params;
                        erLhcoreClassGenericBotWorkflow::logAudit($chat, true);
                    } catch (Exception $e) { // In case log message is to big
                        erLhcoreClassLog::write($e->getMessage(),
                            ezcLog::SUCCESS_AUDIT,
                            array(
                                'source' => 'lhc',
                                'category' => 'bot',
                                'line' => __LINE__,
                                'file' => __FILE__,
                                'object_id' => $chat->id
                            )
                        );
                    }
                }

                if (isset($action['content']['attr_options']['callback_reschedule']) && is_numeric($action['content']['attr_options']['callback_reschedule']) && $action['content']['attr_options']['callback_reschedule'] > 0) {
                    $pendingAction = new erLhcoreClassModelGenericBotPendingEvent();
                    $pendingAction->chat_id = $chat->id;
                    $pendingAction->trigger_id = $action['content']['attr_options']['callback_reschedule'];
                    $pendingAction->saveThis();
                }
                return array(
                    'status' => (isset($action['content']['attr_options']['continue_all']) && $action['content']['attr_options']['continue_all'] == true ? 'continue_all' : 'stop'),
                    'trigger_id' => (isset($action['content']['attr_options']['callback_match']) && is_numeric($action['content']['attr_options']['callback_match']) ? $action['content']['attr_options']['callback_match'] : null)
                );
            } else {

                if (isset($action['content']['attr_options']['log_un_matched']) && $action['content']['attr_options']['log_un_matched'] === true) {
                    try {
                        erLhcoreClassGenericBotWorkflow::$triggerNameDebug[] = $params;
                        erLhcoreClassGenericBotWorkflow::logAudit($chat, true);
                    } catch (Exception $e) { // In case log message is to big
                        erLhcoreClassLog::write($e->getMessage(),
                            ezcLog::SUCCESS_AUDIT,
                            array(
                                'source' => 'lhc',
                                'category' => 'bot',
                                'line' => __LINE__,
                                'file' => __FILE__,
                                'object_id' => $chat->id
                            )
                        );
                    }
                }

                if (isset($action['content']['attr_options']['callback_unreschedule']) && is_numeric($action['content']['attr_options']['callback_unreschedule']) && $action['content']['attr_options']['callback_unreschedule'] > 0) {
                    $pendingAction = new erLhcoreClassModelGenericBotPendingEvent();
                    $pendingAction->chat_id = $chat->id;
                    $pendingAction->trigger_id = $action['content']['attr_options']['callback_unreschedule'];
                    $pendingAction->saveThis();
                }
                if (isset($action['content']['attr_options']['callback_unmatch']) && is_numeric($action['content']['attr_options']['callback_unmatch'])) {
                    return array(
                        'status' => (isset($action['content']['attr_options']['continue_all']) && $action['content']['attr_options']['continue_all'] == true ? 'continue_all' : 'stop'),
                        'trigger_id' => (isset($action['content']['attr_options']['callback_unmatch']) && is_numeric($action['content']['attr_options']['callback_unmatch']) ? $action['content']['attr_options']['callback_unmatch'] : null)
                    );
                }
            }
        }
    }
}
?>