<?php

class erLhcoreClassGenericBotWorkflow {

    public static function findEvent($text, $botId, $type = 0)
    {
        $event = erLhcoreClassModelGenericBotTriggerEvent::findOne(array('filter' => array('bot_id' => $botId, 'type' => $type),'filterlikeright' => array('pattern' => $text)));
        return $event;
    }

    public static function userMessageAdded(& $chat, $msg) {

        $event = self::findEvent($msg->msg, $chat->chat_variables_array['gbot_id']);

        if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
            self::processTrigger($chat, $event->trigger);
        }
    }

    public static function processTrigger($chat, $trigger)
    {
        $message = null;
        foreach ($trigger->actions_front as $action) {
            $message = call_user_func_array("erLhcoreClassGenericBotAction" . ucfirst($action['type']).'::process',array($chat, $action));
        }

        return $message;
    }

    public static function getClickName($metaData, $payload, $returnAll = false)
    {
        foreach ($metaData['content']['quick_replies'] as $reply) {
            if ($reply['content']['payload'] == $payload) {
                return $returnAll == false ? $reply['content']['name'] : $reply['content'];
            }
        }
    }

    public static function processButtonClick($chat, $messageContext, $payload) {

        if (isset($chat->chat_variables_array['gbot_id'])) {

            $event = self::findEvent($payload, $chat->chat_variables_array['gbot_id'], 1);

            $messageClick = self::getClickName($messageContext->meta_msg_array, $payload);

            if (!empty($messageClick)) {
                $messageContext->meta_msg_array['processed'] = true;
                $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                $messageContext->saveThis();
                self::sendAsUser($chat, $messageClick);
            }

            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                $message = self::processTrigger($chat, $event->trigger);
            }

            if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                self::setLastMessageId($chat->id, $message->id);
            } else {
                self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Button action could not be found!'));
            }
        }
    }

    /**
     * @desc generic update actions
     *
     * @param $chat
     * @param $messageContext
     * @param $payload
     */
    public static function processUpdateClick($chat, $messageContext, $payload)
    {
        if (isset($chat->chat_variables_array['gbot_id'])) {

            if (is_callable('erLhcoreClassGenericBotUpdateActions::' . $payload . 'Action')){

                $messageClick = self::getClickName($messageContext->meta_msg_array, $payload, true);

                if (!empty($messageClick)) {
                    self::sendAsUser($chat, $messageClick['name']);
                }

                $message = call_user_func_array("erLhcoreClassGenericBotUpdateActions::" . $payload . 'Action', array($chat, $messageClick));

                $messageContext->meta_msg_array['processed'] = true;
                $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                $messageContext->saveThis();

                if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                    self::setLastMessageId($chat->id, $message->id);
                }

            } else {
                self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Update actions could not be found!'));
            }
        }
    }

    public static function sendAsBot($chat, $message)
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = $message;
        $msg->chat_id = $chat->id;
        $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
        $msg->user_id = -2;
        $msg->time = time() + 5;
        erLhcoreClassChat::getSession()->save($msg);

        self::setLastMessageId($chat->id, $msg->id);
    }

    public static function sendAsUser($chat, $messageText) {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = trim($messageText);
        $msg->chat_id = $chat->id;
        $msg->user_id = 0;
        $msg->time = time();

        if ($chat->chat_locale != '' && $chat->chat_locale_to != '') {
            erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved',array('msg' => & $msg, 'chat' => & $chat));

        erLhcoreClassChat::getSession()->save($msg);
    }

    public static function setLastMessageId($chatId, $messageId) {

        $db = ezcDbInstance::get();

        $stmt = $db->prepare('UPDATE lh_chat SET last_user_msg_time = :last_user_msg_time, lsync = :lsync, last_msg_id = :last_msg_id, has_unread_messages = 1, unanswered_chat = :unanswered_chat WHERE id = :id');
        $stmt->bindValue(':id', $chatId, PDO::PARAM_INT);
        $stmt->bindValue(':lsync', time(), PDO::PARAM_INT);
        $stmt->bindValue(':last_user_msg_time', time(), PDO::PARAM_INT);
        $stmt->bindValue(':unanswered_chat', 0, PDO::PARAM_INT);
        $stmt->bindValue(':last_msg_id',$messageId,PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>