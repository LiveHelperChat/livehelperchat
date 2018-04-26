<?php

class erLhcoreClassGenericBotActionText {

    public static function process($chat, $action)
    {
        $buttons = '';

        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['quick_replies']) && !empty($action['content']['quick_replies']))
        {
            $metaMessage['content']['quick_replies'] = $action['content']['quick_replies'];
        }

        $msg->msg = trim($action['content']['text']) . $buttons;
        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
        $msg->chat_id = $chat->id;
        $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
        $msg->user_id = -2;
        $msg->time = time() + 5;

        erLhcoreClassChat::getSession()->save($msg);

        return $msg;
    }
}

?>