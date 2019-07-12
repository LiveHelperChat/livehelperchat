<?php

class erLhcoreClassGenericBotActionEvent_type {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($params['identifier'])) {
            foreach ($action['content']['events'] as $event) {
                if ($event['content']['identifier'] == $params['identifier']) {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($event['content']['trigger_id']);

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_event_type', array(
                        'trigger' => & $trigger,
                        'render_args' => $params,
                        'chat' => & $chat
                    ));

                    return erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, array('args' => $params));
                }
            }
        }

        return null;
    }
}

?>