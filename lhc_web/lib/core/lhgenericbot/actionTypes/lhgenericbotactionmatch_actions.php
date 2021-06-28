<?php

class erLhcoreClassGenericBotActionMatch_actions {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['on_start_type']) && is_numeric($action['content']['on_start_type']) && $action['content']['on_start_type'] > 0) {

            if (isset($action['content']['event_background']) && $action['content']['event_background'] == true) {
                $event = new erLhcoreClassModelGenericBotChatEvent();
                $event->chat_id = $chat->id;
                $event->ctime = time();
                $event->content = json_encode(array('callback_list' => array(
                    array(
                        'content' => array(
                            'type' => 'default_actions',
                            'event' => (isset($action['content']['event']) ? $action['content']['event'] : null),
                            'event_args' => array(
                                'alternative_callback' => (isset($action['content']['alternative_callback']) ? $action['content']['alternative_callback'] : null),
                                'on_start_type' => (isset($action['content']['on_start_type']) ? $action['content']['on_start_type'] : null)
                            )
                        )
                    )
                )));

                if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                    $event->saveThis();
                }

                return;
            }

            $payload = '';
            if (isset($params['msg']) && $params['msg'] instanceof erLhcoreClassModelmsg) {
                $payload = $params['msg']->msg;
            } elseif (isset($params['msg_text']) && $params['msg_text'] != '') {
                $payload = $params['msg_text'];
            }

            // RAW visitor payload message
            $payloadVisitor = $payload;

            // Override search payload
            if (isset($action['content']['text']) && !empty($action['content']['text'])) {
                $payload = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['text'], array('chat' => $chat, 'args' => $params));

                if (isset($params['replace_array'])) {
                    $payload = str_replace(array_keys($params['replace_array']), array_values($params['replace_array']), $payload);
                }
            }

            $filter = array();

            if (isset($action['content']['on_start_type']) && is_numeric($action['content']['on_start_type']) && $action['content']['on_start_type'] != 5) {
                $filter = array('filter' => array('on_start_type' => $action['content']['on_start_type']));
            }

            $event = erLhcoreClassGenericBotWorkflow::findTextMatchingEvent($payload, $chat->gbot_id, $filter, array('dep_id' => $chat->dep_id));

            if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)) {
                $event = erLhcoreClassGenericBotWorkflow::findEvent($payload, $chat->gbot_id, 0, $filter, array('dep_id' => $chat->dep_id));
            }

            if (isset($action['content']['check_visitor_msg']) && $action['content']['check_visitor_msg'] == true && isset($action['content']['check_visitor_first']) && $action['content']['check_visitor_first'] == true) {
                $eventVisitor = erLhcoreClassGenericBotWorkflow::findTextMatchingEvent($payloadVisitor, $chat->gbot_id, $filter, array('dep_id' => $chat->dep_id));
                if (!($eventVisitor instanceof erLhcoreClassModelGenericBotTriggerEvent)) {
                    $eventVisitor = erLhcoreClassGenericBotWorkflow::findEvent($payloadVisitor, $chat->gbot_id, 0, $filter, array('dep_id' => $chat->dep_id));
                }

                if ($eventVisitor instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                    $event = $eventVisitor;
                }

            } else if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent) && isset($action['content']['check_visitor_msg']) && $action['content']['check_visitor_msg'] == true) {
                $event = erLhcoreClassGenericBotWorkflow::findTextMatchingEvent($payloadVisitor, $chat->gbot_id, $filter, array('dep_id' => $chat->dep_id));
                if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)) {
                    $event = erLhcoreClassGenericBotWorkflow::findEvent($payloadVisitor, $chat->gbot_id, 0, $filter, array('dep_id' => $chat->dep_id));
                }
            }

            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
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
                    for ($i = 0; $i < 3; $i++) {
                        try {
                            $pendingAction = new erLhcoreClassModelGenericBotPendingEvent();
                            $pendingAction->chat_id = $chat->id;
                            $pendingAction->trigger_id = $event->trigger_id;
                            $pendingAction->saveThis();
                            break;
                        } catch (Exception $e) {
                            usleep(500);
                        }
                    }
                } elseif (isset($action['content']['on_start_type']) && $action['content']['on_start_type'] == 5) {
                    return array(
                        'status' => 'continue_all',
                        'trigger_id' => $event->trigger_id
                    );
                }
            } else if (isset($action['content']['alternative_callback']) && is_numeric($action['content']['alternative_callback']) && $action['content']['alternative_callback'] > 0) {
                return array(
                    'status' => 'continue_all',
                    'trigger_id' => $action['content']['alternative_callback']
                );
            }
        }

        return null;
    }
}

?>