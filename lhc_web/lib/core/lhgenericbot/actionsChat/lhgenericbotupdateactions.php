<?php

class erLhcoreClassGenericBotUpdateActions {

    public static function transferToOperatorAction($chat, $payload)
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = isset($payload['payload_message']) ? $payload['payload_message'] : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Chat was transferred to operator!');
        $msg->chat_id = $chat->id;
        $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
        $msg->user_id = -2;
        $msg->time = time() + 5;

        erLhcoreClassChat::getSession()->save($msg);

        $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
        $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list
        $chat->pnd_time = time();
        $chat->saveThis();

        return $msg;
    }

    public static function transferToBotAction($chat, $payload)
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = isset($payload['payload_message']) ? $payload['payload_message'] : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Chat was transferred to bot!');
        $msg->chat_id = $chat->id;
        $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
        $msg->user_id = -2;
        $msg->time = time() + 5;

        erLhcoreClassChat::getSession()->save($msg);

        $chat->status = erLhcoreClassModelChat::STATUS_BOT_CHAT;
        $chat->saveThis();

        return $msg;
    }
}

?>