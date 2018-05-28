<?php

class erLhcoreClassGenericBotActionTyping {

    public static function process($chat, $action)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['duration']) && !empty($action['content']['duration']) && $action['content']['duration'] > 0)
        {
            $metaMessage['content']['typing'] = $action['content'];

            $msg->msg = "";
            $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
            $msg->chat_id = $chat->id;
            $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
            $msg->user_id = -2;
            $msg->time = time() + 5;

            erLhcoreClassChat::getSession()->save($msg);
        }

        return $msg;
    }
}

?>