<?php

class erLhcoreClassGenericBotActionButtons {

    public static function process($chat, $action, $trigger, $params)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['buttons']) && !empty($action['content']['buttons'])) {
            $metaMessage['content']['buttons_generic'] = $action['content']['buttons'];
        }

        if (isset($action['content']['buttons_options']['hide_text_area']) && $action['content']['buttons_options']['hide_text_area'] == true) {
             $metaMessage['content']['attr_options']['hide_text_area'] = true;
        }

        $msgText = isset($action['content']['buttons_options']['message']) && !empty($action['content']['buttons_options']['message']) ? $action['content']['buttons_options']['message'] : '';

        if ($msgText != '') {
            $msgText = erLhcoreClassGenericBotWorkflow::translateMessage($msgText, array('chat' => $chat));
        }

        $msg->msg = $msgText;

        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';

        if ($msg->meta_msg != '') {
            $msg->meta_msg = erLhcoreClassGenericBotWorkflow::translateMessage($msg->meta_msg, array('chat' => $chat));
        }

        $msg->chat_id = $chat->id;
        $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
        $msg->user_id = -2;
        $msg->time = time() + 5;

        if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
            erLhcoreClassChat::getSession()->save($msg);
        }

        return $msg;
    }
}

?>