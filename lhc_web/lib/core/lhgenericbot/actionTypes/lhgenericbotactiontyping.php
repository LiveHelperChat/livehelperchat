<?php

class erLhcoreClassGenericBotActionTyping {

    public static function process($chat, $action, $trigger, $params)
    {
        static $triggersProcessed = array();

        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['duration']) && !empty($action['content']['duration']) && $action['content']['duration'] > 0)
        {

            // Message should be send only on start chat event, but we are not in start chat mode
            if (in_array($trigger->id, $triggersProcessed) || isset($action['content']['on_start_chat']) && $action['content']['on_start_chat'] == true && erLhcoreClassGenericBotWorkflow::$startChat == false)
            {
                return;
            }

            // Send only once
            if (isset($action['content']['on_start_chat']) && $action['content']['on_start_chat'] == true && erLhcoreClassGenericBotWorkflow::$startChat == true)
            {
                $triggersProcessed[] = $trigger->id;
            }

            $metaMessage['content']['typing'] = $action['content'];

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