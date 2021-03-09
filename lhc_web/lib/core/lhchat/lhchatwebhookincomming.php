<?php

class erLhcoreClassChatWebhookIncoming {

    public static function processEvent($incomingWebhook, array $payload) {

        $conditions = $incomingWebhook->conditions_array;

        if (isset($conditions['main_cond']) && $conditions['main_cond'] != "") {
            $conditionsPairs = explode("||",$conditions['main_cond']);
            foreach ($conditionsPairs as $conditionsPair) {
                $conditionsPairData = explode('=',$conditionsPair);
                if (!(isset($payload[$conditionsPairData[0]]) && $payload[$conditionsPairData[0]] == $conditionsPairData[1])) {
                    return;
                }
            }
        }

        $messages = isset($conditions['messages']) && $conditions['messages'] != '' ? $payload[$conditions['messages']] : [$payload];

        foreach ($messages as $message){
            self::processMessage($incomingWebhook, $message);
        }
    }

    public static function processMessage($incomingWebhook, $payloadMessage) {

        $conditions = $incomingWebhook->conditions_array;

        $typeMessage = 'unknown';

        if (isset($conditions['msg_cond']) && $conditions['msg_cond'] != "") {
            $conditionsPairs = explode("||",$conditions['msg_cond']);
            foreach ($conditionsPairs as $conditionsPair) {
                $conditionsPairData = explode('=',$conditionsPair);
                if (isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]) {
                    $typeMessage = 'text';
                }
            }
        } else {
            $typeMessage = 'text';
        }

        // Unknown message type.
        if ($typeMessage == 'unknown') {
            return;
        }

        $eChat = erLhcoreClassModelChatIncoming::findOne(array(
            'filter' => array(
                'chat_external_id' => $payloadMessage[$conditions['chat_id']],
                'incoming_id' => $incomingWebhook->id
            )
        ));

        $continueIfHasChat = false;

        if ($eChat !== false && ($chat = $eChat->chat) !== false) {
            $continueIfHasChat = ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) || ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && !(!isset($conditions['chat_status']) || $conditions['chat_status'] == ""));
        }

        if ($continueIfHasChat == true && $eChat !== false && ($chat = $eChat->chat) !== false ) {
            $renotify = false;

            if ($chat instanceof erLhcoreClassModelChat && $chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {

                if (isset($conditions['chat_status']) && $conditions['chat_status'] == 'active' && $chat->user_id > 0) {
                    $chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
                    $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list
                } else {
                    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                    $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list

                    if (isset($conditions['reset_op']) && $conditions['reset_op'] == true) {
                        $chat->user_id = 0;
                    }

                    $chat->pnd_time = time();
                    $renotify = true;
                }
            }

            $msg = new erLhcoreClassModelmsg();
            $msg->msg = trim($payloadMessage[$conditions['msg_body']]);
            $msg->chat_id = $chat->id;
            $msg->user_id = 0;
            $msg->time = time();
            erLhcoreClassChat::getSession()->save($msg);

            $chat->last_user_msg_time = $msg->time;
            $chat->last_msg_id = $msg->id;

            if ($renotify == true) {
                erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg));
            }

            // Create auto responder if there is none
            if ($chat->auto_responder === false) {
                $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);
                if ($responder instanceof erLhAbstractModelAutoResponder) {
                    $responderChat = new erLhAbstractModelAutoResponderChat();
                    $responderChat->auto_responder_id = $responder->id;
                    $responderChat->chat_id = $chat->id;
                    $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                    $responderChat->saveThis();

                    $chat->auto_responder_id = $responderChat->id;
                    $chat->auto_responder = $responderChat;
                }
            }

            $chatVariables = $chat->chat_variables_array;

            // Auto responder if department is offline
            if ($chat->auto_responder !== false) {

                $responder = $chat->auto_responder->auto_responder;

                if ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT && is_object($responder) && $responder->offline_message != '' && !erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                        'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
                        'exclude_bot' => true
                    ))) {
                    if (!isset($chatVariables['iwh_timeout']) || $chatVariables['iwh_timeout'] < time() - (int)259200) {
                        $chatVariables['iwh_timeout'] = time();
                        $chat->chat_variables_array = $chatVariables;
                        $chat->chat_variables = json_encode($chatVariables);

                        $msgResponder = new erLhcoreClassModelmsg();
                        $msgResponder->msg = trim($responder->offline_message);
                        $msgResponder->chat_id = $chat->id;
                        $msgResponder->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
                        $msgResponder->user_id = -2;
                        $msgResponder->time = time() + 5;
                        erLhcoreClassChat::getSession()->save($msgResponder);

                        $chat->last_msg_id = $msgResponder->id;

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                            'chat' => & $chat,
                            'msg' => $msgResponder
                        ));
                    }
                }
            }

            $chat->updateThis(array('update' => array(
                'pnd_time',
                'last_user_msg_time',
                'status',
                'user_id',
                'chat_variables',
                'status_sub_sub',
                'last_msg_id')));

            $eChat->utime = time();
            $eChat->updateThis();

            self::sendBotResponse($chat, $msg, array('init' => $renotify));

            // Standard event on unread chat messages
            if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat', array(
                    'chat' => & $chat
                ));
            }

            // We dispatch same event as we were using desktop client, because it force admins and users to resync chat for new messages
            // This allows NodeJS users to know about new message. In this particular case it's admin users
            // If operator has opened chat instantly sync
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_passive', array(
                'chat' => & $chat,
                'msg' => $msg
            ));

            // If operator has closed a chat we need force back office sync
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.nodjshelper_notify_delay', array(
                'chat' => & $chat,
                'msg' => $msg
            ));

            if ($renotify == true) {
                // General module signal that it has received an sms
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.restart_chat',array(
                    'chat' => & $chat,
                    'msg' => $msg,
                ));
            }

        } else {

            // Save chat
            $chat = new erLhcoreClassModelChat();
            $chat->nick = $payloadMessage[$conditions['nick']];
            $chat->time = time();
            $chat->pnd_time = time();
            $chat->status = 0;
            $chat->hash = erLhcoreClassChat::generateHash();
            $chat->referrer = '';
            $chat->session_referrer = '';
            $chat->dep_id = $incomingWebhook->dep_id;

            $chatVariables = array(
                'iwh_id' => $incomingWebhook->id,
            );

            $chat->chat_variables = json_encode($chatVariables);
            $chat->saveThis();

            // Save message
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = trim($payloadMessage[$conditions['msg_body']]);
            $msg->chat_id = $chat->id;
            $msg->user_id = 0;
            $msg->time = time();
            erLhcoreClassChat::getSession()->save($msg);

            // Save external chat
            $eChat = ($eChat instanceof erLhcoreClassModelChatIncoming) ? $eChat : (new erLhcoreClassModelChatIncoming());
            $eChat->chat_external_id = $payloadMessage[$conditions['chat_id']];
            $eChat->incoming_id = $incomingWebhook->id;
            $eChat->chat_id = $chat->id;
            $eChat->utime = time();
            $eChat->saveThis();

            // Set bot
            erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg));
            self::sendBotResponse($chat, $msg, array('init' => true));

            /**
             * Set appropriate chat attributes
             */
            $chat->last_msg_id = $msg->id;
            $chat->last_user_msg_time = $msg->time;

            // Process auto responder
            $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);

            if ($responder instanceof erLhAbstractModelAutoResponder) {
                $responderChat = new erLhAbstractModelAutoResponderChat();
                $responderChat->auto_responder_id = $responder->id;
                $responderChat->chat_id = $chat->id;
                $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                $responderChat->saveThis();

                $chat->auto_responder_id = $responderChat->id;

                if ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT)
                {
                    if ($responder->offline_message != '' && !erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                            'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                            'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
                            'exclude_bot' => true
                        ))) {
                        $msg = new erLhcoreClassModelmsg();
                        $msg->msg = trim($responder->offline_message);
                        $msg->chat_id = $chat->id;
                        $msg->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
                        $msg->user_id = -2;
                        $msg->time = time() + 5;
                        erLhcoreClassChat::getSession()->save($msg);

                        $messageResponder = $msg;

                        if ($chat->last_msg_id < $msg->id) {
                            $chat->last_msg_id = $msg->id;
                        }

                        $chatVariables['iwh_timeout'] = time();
                        $chat->chat_variables_array = $chatVariables;
                        $chat->chat_variables = json_encode($chatVariables);
                    }
                }
            }

            // Save chat
            $chat->saveThis();

            // Auto responder has something to send to visitor.
            if (isset($messageResponder)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                    'chat' => & $chat,
                    'msg' => $messageResponder
                ));
            }

            /**
             * Execute standard callback as chat was started
             */
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_started', array(
                'chat' => & $chat,
                'msg' => $msg
            ));
        }
    }

    public static function sendBotResponse($chat, $msg, $params = array()) {
        if ($chat->gbot_id > 0 && (!isset($chat->chat_variables_array['gbot_disabled']) || $chat->chat_variables_array['gbot_disabled'] == 0)) {

            $chat->refreshThis();

            if (!isset($params['init']) || $params['init'] == false) {
                erLhcoreClassGenericBotWorkflow::userMessageAdded($chat, $msg);
            }

            // Find a new messages
            $botMessages = erLhcoreClassModelmsg::getList(array('filter' => array('user_id' => -2, 'chat_id' => $chat->id), 'filtergt' => array('id' => $msg->id)));
            foreach ($botMessages as $botMessage) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                    'chat' => & $chat,
                    'msg' => $botMessage
                ));
            }
        }
    }


}

?>