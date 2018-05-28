<?php

class erLhcoreClassGenericBotActionButtons {

    public static function process($chat, $action)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['buttons']) && !empty($action['content']['buttons'])) {
            $metaMessage['content']['buttons_generic'] = $action['content']['buttons'];
        }

        $msg->msg = isset($action['content']['buttons_options']['message']) && !empty($action['content']['buttons_options']['message']) ? $action['content']['buttons_options']['message'] : '';
        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
        $msg->chat_id = $chat->id;
        $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
        $msg->user_id = -2;
        $msg->time = time() + 5;

        erLhcoreClassChat::getSession()->save($msg);

        return $msg;
    }
}

?>