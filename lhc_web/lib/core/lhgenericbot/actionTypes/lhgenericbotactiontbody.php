<?php

class erLhcoreClassGenericBotActionTbody {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['payload']) && !empty($action['content']['payload']))
        {
            $payload = isset($params['replace_array']) ? str_replace(array_keys($params['replace_array']), array_values($params['replace_array']), $action['content']['payload']) : $action['content']['payload'];

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_actionbody', array(
                'payload' => & $payload,
                'chat' => $chat
            ));

            $triggerBody = json_decode($payload, true);

            if ($triggerBody !== null) {

                $triggerRest = new erLhcoreClassModelGenericBotTrigger();
                $triggerRest->actions = $payload;
                $triggerRest->actions_front = $triggerBody;
                $triggerRest->bot_id = $trigger->bot_id;

                $args = array();

                if (isset($params['msg'])) {
                    $args['args']['msg'] = $params['msg'];
                } elseif (isset($params['msg_text'])) {
                    $args['args']['msg_text'] = $params['msg_text'];
                }

                if (isset($params['replace_array'])) {
                    $args['args']['replace_array'] = $params['replace_array'];
                }

                if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                   return erLhcoreClassGenericBotWorkflow::processTrigger($chat, $triggerRest, true, $args);
                }
            }
        }
    }
}

?>