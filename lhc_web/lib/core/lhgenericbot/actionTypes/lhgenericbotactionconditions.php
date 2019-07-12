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
                $chatAttributesFrontend[$attr['identifier']] = $attr['value'];
            }

            foreach ($action['content']['conditions'] as $condition) {
                if (isset($condition['content']['attr']) && $condition['content']['attr'] != '' &&
                    isset($condition['content']['val']) && $condition['content']['val'] != '' &&
                    isset($condition['content']['comp']) && $condition['content']['comp'] != '')
                {
                    $attr = null;

                    if ($condition['content']['attr'] == 'lhc.nick') {
                        $attr = $chat->nick;
                    } elseif (isset($chatVariables[$condition['content']['attr']])) {
                        $attr = $chatVariables[$condition['content']['attr']];
                    } elseif ($chatAttributesFrontend[$condition['content']['attr']]) {
                        $attr = $chatAttributesFrontend[$condition['content']['attr']];
                    }

                    if ($attr === null) {
                        $conditionsMet = false;
                        break;
                    }

                    if ($condition['content']['comp'] == 'eq' && !($attr == $condition['content']['val'])) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'lt' && !($attr < $condition['content']['val'])) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'lte' && !($attr <= $condition['content']['val'])) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'neq' && !($attr != $condition['content']['val'])) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'gte' && !($attr >= $condition['content']['val'])) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'gt' && !($attr > $condition['content']['val'])) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$condition['content']['val']),$attr,0) == false) {
                        $conditionsMet = false;
                        break;
                    } else if ($condition['content']['comp'] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$condition['content']['val']),$attr,0) == true) {
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

                return array(
                    'status' => 'stop',
                    'trigger_id' => ((isset($action['content']['attr_options']['callback_match']) && is_numeric($action['content']['attr_options']['callback_match'])) ? $action['content']['attr_options']['callback_match'] : null)
                );
            }
        }
    }
}

?>