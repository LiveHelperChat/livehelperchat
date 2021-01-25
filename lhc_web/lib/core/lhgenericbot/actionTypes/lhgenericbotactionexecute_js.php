<?php

class erLhcoreClassGenericBotActionExecute_js {

    public static function process($chat, $action, $trigger, $params)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if ((isset($action['content']['payload']) && !empty($action['content']['payload'])) || (isset($action['content']['ext_execute']) && !empty($action['content']['ext_execute'])))
        {
            if (isset($action['content']['payload'])) {
                $action['content']['payload'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload'], array('chat' => $chat, 'args' => $params));
            }

            if (isset($action['content']['ext_args'])) {
                $action['content']['ext_args'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['ext_args'], array('chat' => $chat, 'args' => $params));
            }

            if (isset($action['content']['ext_execute'])) {
                $action['content']['ext_execute'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['ext_execute'], array('chat' => $chat, 'args' => $params));
            }

            $metaMessage['content']['execute_js'] = $action['content'];

            $msg->msg = "";
            $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
            $msg->chat_id = $chat->id;
            $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
            $msg->user_id = -2;
            $msg->time = time() + 5;

            if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                erLhcoreClassChat::getSession()->save($msg);
            }
        }

        return $msg;
    }
}

?>