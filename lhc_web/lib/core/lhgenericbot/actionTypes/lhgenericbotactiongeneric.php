<?php

class erLhcoreClassGenericBotActionGeneric {

    public static function process($chat, $action)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['list']) && !empty($action['content']['list'])) {
            $metaMessage['content']['generic']['items'] = $action['content']['list'];
        }

        $msg->msg = '';
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