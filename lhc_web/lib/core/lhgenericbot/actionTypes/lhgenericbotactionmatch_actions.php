<?php

class erLhcoreClassGenericBotActionMatch_actions {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['on_start_type']) && is_numeric($action['content']['on_start_type']) && $action['content']['on_start_type'] > 0) {

            $payload = '';
            if (isset($params['msg']) && $params['msg'] instanceof erLhcoreClassModelmsg) {
                $payload = $params['msg']->msg;
            } elseif (isset($params['msg_text']) && $params['msg_text'] != '') {
                $payload = $params['msg_text'];
            }

            $event = erLhcoreClassGenericBotWorkflow::findTextMatchingEvent($payload, $chat->chat_variables_array['gbot_id'], array('filter' => array('on_start_type' => $action['content']['on_start_type'])));

            if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)) {
                $event = erLhcoreClassGenericBotWorkflow::findEvent($payload, $chat->chat_variables_array['gbot_id'],0, array('filter' => array('on_start_type' => $action['content']['on_start_type'])));
            }

            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent){
                if (isset($action['content']['on_start_type']) && $action['content']['on_start_type'] == 1) {
                    return array(
                        'status' => 'continue_all',
                        'trigger_id' => $event->trigger_id
                    );
                } elseif (isset($action['content']['on_start_type']) && $action['content']['on_start_type'] == 2) {
                    return array(
                        'status' => 'stop',
                        'trigger_id' => $event->trigger_id
                    );
                } elseif (isset($action['content']['on_start_type']) && $action['content']['on_start_type'] == 3) {
                    return array(
                        'status' => 'continue',
                        'trigger_id' => $event->trigger_id
                    );
                } elseif (isset($action['content']['on_start_type']) && $action['content']['on_start_type'] == 4) {
                    $pendingAction = new erLhcoreClassModelGenericBotPendingEvent();
                    $pendingAction->chat_id = $chat->id;
                    $pendingAction->trigger_id = $event->trigger_id;
                    $pendingAction->saveThis();
                }
            }
        }

        return null;
    }
}

?>