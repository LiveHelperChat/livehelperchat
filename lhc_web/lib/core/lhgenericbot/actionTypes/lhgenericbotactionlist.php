<?php

class erLhcoreClassGenericBotActionList {

    public static function process($chat, $action)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['list']) && !empty($action['content']['list'])) {
            $metaMessage['content']['list']['items'] = $action['content']['list'];

            if (isset($action['content']['quick_replies']) && !empty($action['content']['quick_replies'])) {
                $metaMessage['content']['list']['list_quick_replies'] = $action['content']['quick_replies'];
            }
        }

        $metaMessage['options']['no_highlight'] = isset($action['content']['list_options']['no_highlight']) && $action['content']['list_options']['no_highlight'] == true ? true : false;

        $msg->msg = '';
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