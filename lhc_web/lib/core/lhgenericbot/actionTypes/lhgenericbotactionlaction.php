<?php

class erLhcoreClassGenericBotActionLaction {

    public static function process($chat, $action, $trigger, $params = array())
    {
        if (isset($action['content']['text']) && $action['content']['text'] != '') {

            if (!isset($action['content']['action_options']['action']) || $action['content']['action_options']['action'] == '') {
                return;
            }

            $params['current_trigger'] = $trigger;

            if (!isset($params['first_trigger'])) {
                $params['first_trigger'] = $params['current_trigger'];
            }

            $chatAction = new erLhcoreClassModelChatAction();
            $chatAction->chat_id = $chat->id;
            $chatAction->action = (string)$action['content']['action_options']['action'];
            $chatAction->body_array = json_decode(erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['text'], array('as_json' => true, 'chat' => $chat, 'args' => $params)), true);
            $chatAction->body = json_encode($chatAction->body_array);
            $chatAction->saveThis();
        }
    }
}

?>