<?php

class erLhcoreClassGenericBotActionConditions {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['conditions']) && is_array($action['content']['conditions']) && !empty($action['content']['conditions']) ) {

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

            foreach ($action['content']['conditions'] as $condition) {
                if (isset($condition['content']['attr']) && $condition['content']['attr'] != '' &&
                    isset($condition['content']['comp']) && $condition['content']['comp'] != '')
                {
                    $attr = null;
                    $valAttr = isset($condition['content']['val']) ? $condition['content']['val'] : null;

                    $paramsConditions = explode('.',$condition['content']['attr']);

                    if ($paramsConditions[0] == 'lhc') {
                        $attr = $chat->{$paramsConditions[1]};
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
                    } elseif (strpos($condition['content']['attr'],'{args.') !== false) {
                        $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(array_merge($params,array('chat' => $chat)), str_replace(array('{args.','{','}'),'',$condition['content']['attr']), '.');
                        $attr = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
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

                    if ($condition['content']['comp'] == 'eq' && !($attr == $valAttr)) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'lt' && !($attr < $valAttr)) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'lte' && !($attr <= $valAttr)) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'neq' && !($attr != $valAttr)) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'gte' && !($attr >= $valAttr)) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'gt' && !($attr > $valAttr)) {
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
                    }
                }
            }

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