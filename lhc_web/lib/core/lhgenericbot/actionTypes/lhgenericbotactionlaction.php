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

            $bodyAction = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['text'], array('as_json' => true, 'chat' => $chat, 'args' => $params));

            $chatAction->body_array = json_decode($bodyAction, true);
            $chatAction->body = json_encode($chatAction->body_array);

            if ($chatAction->body_array == null) {
                $chatAction->body = json_encode($bodyAction);
            }

            $chatAction->saveThis();
        }
    }
}

?>