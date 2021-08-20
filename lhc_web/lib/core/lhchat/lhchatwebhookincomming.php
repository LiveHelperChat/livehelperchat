<?php

class erLhcoreClassChatWebhookIncoming {

    public static function processEvent($incomingWebhook, array $payload) {

        $conditions = $incomingWebhook->conditions_array;

        if (isset($conditions['main_cond']) && $conditions['main_cond'] != "") {
            $conditionsPairs = explode("||",$conditions['main_cond']);
            foreach ($conditionsPairs as $conditionsPair) {
                $conditionsPairData = explode('=',$conditionsPair);

                if ($conditionsPairData[1] === 'false') {
                    $conditionsPairData[1] = false;
                } elseif ($conditionsPairData[1] === 'true') {
                    $conditionsPairData[1] = true;
                } elseif (strpos($conditionsPairData[1], ',') !== false) {
                    $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                }

                if ((is_array($conditionsPairData[1]) && !in_array($payload[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payload[$conditionsPairData[0]]) && $payload[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                    throw new Exception('Main conditions does not met!' . json_encode($payload));
                }
            }
        }

        $messages = isset($conditions['messages']) && $conditions['messages'] != '' ? $payload[$conditions['messages']] : [$payload];

        if (isset($conditions['messages']) && $conditions['messages'] != '' &&  isset($conditions['message_direct']) && $conditions['message_direct'] == true) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            self::processMessage($incomingWebhook, $message, $payload);
        }
    }

    public static function processMessage($incomingWebhook, $payloadMessage, $payloadAll) {

        $conditions = $incomingWebhook->conditions_array;

        $typeMessage = 'unknown';
        $sender = 0;
        $conditionsOperator = '';

        if (isset($conditions['msg_cond_attachments']) && $conditions['msg_cond_attachments'] != "") {
            $typeMessage = 'attachments';
            $conditionsPairs = explode("||",$conditions['msg_cond_attachments']);
            foreach ($conditionsPairs as $conditionsPair) {
                $conditionsPairData = explode('=',$conditionsPair);

                if ($conditionsPairData[1] === 'false') {
                    $conditionsPairData[1] = false;
                } elseif ($conditionsPairData[1] === 'true') {
                    $conditionsPairData[1] = true;
                } elseif (strpos($conditionsPairData[1], ',') !== false) {
                    $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                }

                if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                    $typeMessage = 'unknown';
                }
            }

            if ($typeMessage == 'attachments') {
                $msgBody = $conditions['msg_attachments'];
                $conditionsOperator = isset($conditions['msg_cond_attachments_op']) ? $conditions['msg_cond_attachments_op'] : "";
            }
        }


        if ($typeMessage == 'unknown')
        {
            if (isset($conditions['msg_cond_img']) && $conditions['msg_cond_img'] != "") {
                $typeMessage = 'img';
                $conditionsPairs = explode("||",$conditions['msg_cond_img']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=',$conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'img') {
                    $msgBody = $conditions['msg_img'];
                    $conditionsOperator = isset($conditions['msg_cond_img_op']) ? $conditions['msg_cond_img_op'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            if (isset($conditions['msg_cond_img_2']) && $conditions['msg_cond_img_2'] != "") {
                $typeMessage = 'img_2';
                $conditionsPairs = explode("||",$conditions['msg_cond_img_2']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=',$conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'img_2') {
                    $msgBody = $conditions['msg_img_2'];
                    $conditionsOperator = isset($conditions['msg_cond_img_2_op']) ? $conditions['msg_cond_img_2_op'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            $msgBody = $conditions['msg_body_2'];

            if (isset($conditions['msg_cond_2']) && $conditions['msg_cond_2'] != "") {
                $typeMessage = 'text';
                $conditionsPairs = explode("||",$conditions['msg_cond_2']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=', $conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'text') {
                    $conditionsOperator = isset($conditions['msg_cond_op_2']) ? $conditions['msg_cond_op_2'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            $msgBody = $conditions['msg_body_3'];

            if (isset($conditions['msg_cond_3']) && $conditions['msg_cond_3'] != "") {
                $typeMessage = 'text';
                $conditionsPairs = explode("||",$conditions['msg_cond_3']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=', $conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'text') {
                    $conditionsOperator = isset($conditions['msg_cond_op_3']) ? $conditions['msg_cond_op_3'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            $msgBody = $conditions['msg_body_4'];

            if (isset($conditions['msg_cond_4']) && $conditions['msg_cond_4'] != "") {
                $typeMessage = 'text';
                $conditionsPairs = explode("||",$conditions['msg_cond_4']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=', $conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'text') {
                    $conditionsOperator = isset($conditions['msg_cond_op_4']) ? $conditions['msg_cond_op_4'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            $msgBody = $conditions['msg_body'];

            if (isset($conditions['msg_cond']) && $conditions['msg_cond'] != "") {
                $typeMessage = 'text';
                $conditionsPairs = explode("||",$conditions['msg_cond']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=', $conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }
            } else {
                $typeMessage = 'text';
            }

            if ($typeMessage == 'text') {
                $conditionsOperator = isset($conditions['msg_cond_op']) ? $conditions['msg_cond_op'] : "";
            }
        }

        // Unknown message type.
        if ($typeMessage == 'unknown') {
            throw new Exception('Message conditions does not met! ' . json_encode($payloadMessage));
        }

        if ($conditionsOperator != '') {
            $sender = -2;
            $conditionsPairs = explode("||",$conditionsOperator);
            foreach ($conditionsPairs as $conditionsPair) {
                $conditionsPairData = explode('=', $conditionsPair);

                if ($conditionsPairData[1] === 'false') {
                    $conditionsPairData[1] = false;
                } elseif ($conditionsPairData[1] === 'true') {
                    $conditionsPairData[1] = true;
                } elseif (strpos($conditionsPairData[1], ',') !== false) {
                    $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                }

                if ((is_array($conditionsPairData[1]) && !in_array($payloadMessage[$conditionsPairData[0]], $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($payloadMessage[$conditionsPairData[0]]) && $payloadMessage[$conditionsPairData[0]] == $conditionsPairData[1]))) {
                    $sender = 0;
                }
            }
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

            if ($typeMessage == 'img' || $typeMessage == 'img_2' || $typeMessage == 'attachments') {
                if (isset($conditions['msg_cond_'.$typeMessage.'_url_decode']) && $conditions['msg_cond_'.$typeMessage.'_url_decode'] != '') {
                    $file = self::parseFilesDecode(array(
                        'msg' => $payloadMessage,
                        'url' => $conditions['msg_cond_'.$typeMessage.'_url_decode'],
                        'body_post' => $conditions['msg_cond_'.$typeMessage.'_url_decode_content'],
                        'response_location' => $conditions['msg_cond_'.$typeMessage.'_url_decode_output']
                    ), $chat);
                    if (!empty($file)) {
                        $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']] = $file;
                    }
                } else if (isset($conditions['msg_'.$typeMessage.'_download']) && $conditions['msg_'.$typeMessage.'_download'] == true) {
                    $file = self::parseFiles(
                        self::extractAttribute(
                            'msg_cond_'.$typeMessage.'_body',
                            $conditions,
                            $payloadMessage,
                            (isset($payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']]) ? $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']] : '')),
                        $chat);
                    if (!empty($file)) {
                        self::array_set_value($payloadMessage, $conditions['msg_cond_'.$typeMessage.'_body'], $file);
                    }
                } else if (
                    // base64 encoded file
                    strpos($payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']],'https://') === false &&
                    strpos($payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']],'http://') === false) {
                    $file = self::parseFilesBase64(array('body' => $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']], 'file_name' => $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_file_name']]), $chat);
                    if (!empty($file)) {
                        $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']] = $file;
                    }
                }
            }

            $msg = new erLhcoreClassModelmsg();
            $msg->msg = self::extractMessageBody($msgBody,$payloadMessage);
            $msg->chat_id = $chat->id;
            $msg->user_id = $sender;

            $timeValue = self::extractAttribute('time', $conditions, $payloadMessage, time());
            $msg->time = is_numeric($timeValue) ? $timeValue : strtotime($timeValue);

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
                        $msgResponder->time = time() + 1;
                        erLhcoreClassChat::getSession()->save($msgResponder);

                        $chat->last_msg_id = $msgResponder->id;

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                            'chat' => & $chat,
                            'msg' => $msgResponder
                        ));
                    }
                }
            }

            $chat->nick = self::extractAttribute('nick',$conditions, $payloadMessage, $chat->nick);

            if ($chat->nick == 'Visitor') {
                $chat->nick = self::extractAttribute('nick', $conditions, $payloadAll, $chat->nick);
            }

            $chat->phone = self::extractAttribute('phone',$conditions, $payloadMessage, $chat->phone);

            if ($sender == 0) {
                $ip = self::extractAttribute('ip', $conditions, $payloadMessage, $chat->ip);

                if ($ip != '' && $chat->ip != $ip) {
                    $chat->ip = $ip;
                    erLhcoreClassModelChat::detectLocation($chat, "");
                }
            }

            $chat->updateThis(array('update' => array(
                'country_code',
                'country_name',
                'lat',
                'lon',
                'city',
                'ip',
                'pnd_time',
                'last_user_msg_time',
                'status',
                'nick',
                'email',
                'phone',
                'user_id',
                'chat_variables',
                'status_sub_sub',
                'last_msg_id')));

            if (empty($eChat->payload)) {
                $eChat->payload = json_encode($payloadAll);
            }

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

            $chat->nick = self::extractAttribute('nick', $conditions, $payloadMessage,'Visitor');

            // Perhaps it's first level attribute
            if ($chat->nick == 'Visitor') {
                $chat->nick = self::extractAttribute('nick', $conditions, $payloadAll,'Visitor');
            }

            $chat->phone = self::extractAttribute('phone', $conditions, $payloadMessage);
            $chat->email = self::extractAttribute('email', $conditions, $payloadMessage);

            if ($sender == 0) {
                $ip = self::extractAttribute('ip',$conditions,$payloadMessage,$chat->ip);

                if ($ip != '' && $chat->ip != $ip) {
                    $chat->ip = $ip;
                    erLhcoreClassModelChat::detectLocation($chat, "");
                }
            }

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

            if ($typeMessage == 'img' || $typeMessage == 'img_2' ||  $typeMessage == 'attachments') {
                if (isset($conditions['msg_cond_'.$typeMessage.'_url_decode']) && $conditions['msg_cond_'.$typeMessage.'_url_decode'] != '') {
                    $file = self::parseFilesDecode(array(
                        'msg' => $payloadMessage,
                        'url' => $conditions['msg_cond_'.$typeMessage.'_url_decode'],
                        'body_post' => $conditions['msg_cond_'.$typeMessage.'_url_decode_content'],
                        'response_location' => $conditions['msg_cond_'.$typeMessage.'_url_decode_output']
                    ), $chat);
                    if (!empty($file)) {
                        $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']] = $file;
                    }
                } else if (isset($conditions['msg_'.$typeMessage.'_download']) && $conditions['msg_'.$typeMessage.'_download'] == true) {
                    $file = self::parseFiles(
                        self::extractAttribute(
                            'msg_cond_'.$typeMessage.'_body',
                            $conditions,
                            $payloadMessage,
                            (isset($payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']]) ? $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']] : '')),
                        $chat);
                    if (!empty($file)) {
                        self::array_set_value($payloadMessage, $conditions['msg_cond_'.$typeMessage.'_body'], $file);
                    }
                } else if (
                    // base64 encoded file
                    strpos($payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']],'https://') === false &&
                    strpos($payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']],'http://') === false) {
                    $file = self::parseFilesBase64(array('body' => $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']], 'file_name' => $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_file_name']]), $chat);
                    if (!empty($file)) {
                        $payloadMessage[$conditions['msg_cond_'.$typeMessage.'_body']] = $file;
                    }
                }
            }

            // Save message
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = self::extractMessageBody($msgBody, $payloadMessage);
            $msg->chat_id = $chat->id;
            $msg->user_id = $sender;

            $timeValue = self::extractAttribute('time', $conditions, $payloadMessage, time());
            $msg->time = is_numeric($timeValue) ? $timeValue : strtotime($timeValue);

            erLhcoreClassChat::getSession()->save($msg);

            // Save external chat
            $eChat = ($eChat instanceof erLhcoreClassModelChatIncoming) ? $eChat : (new erLhcoreClassModelChatIncoming());
            $eChat->chat_external_id = self::extractAttribute('chat_id',$conditions,$payloadMessage);

            if ($eChat->chat_external_id == '') {
                throw new Exception('ChatId attribute could not be found!');
            }

            $eChat->incoming_id = $incomingWebhook->id;
            $eChat->chat_id = $chat->id;
            $eChat->utime = time();
            $eChat->payload = json_encode($payloadAll);
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
                        $msg->time = time() + 1;
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

    // https://stackoverflow.com/questions/9628176/using-a-string-path-to-set-nested-array-data
    public static function array_set_value(array &$array, $parents, $value, $glue = '.')
    {
        if (!is_array($parents)) {
            $parents = explode($glue, (string) $parents);
        }

        $ref = &$array;

        foreach ($parents as $parent) {
            if (isset($ref) && !is_array($ref)) {
                $ref = array();
            }

            $ref = &$ref[$parent];
        }

        $ref = $value;
    }

    public static function extractAttribute($attr, $conditions, $payload, $defaultValue = '') {

        if (!isset($conditions[$attr]) || $conditions[$attr] == '') {
            return $defaultValue;
        } else {
            $attrParams = explode('||',$conditions[$attr]);
        }

        $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($payload, $attrParams[0], '.');

        $baseValue = $valueAttribute['found'] == true ? $valueAttribute['value'] : $defaultValue;

        if (isset($attrParams[1]) && isset($attrParams[2])) {
            $baseValueParams = explode($attrParams[1],$baseValue);
            if ($attrParams[2] == 'last') {
                $baseValue = array_pop($baseValueParams);
            } elseif (isset($baseValueParams[(int)$attrParams[2]])) {
                $baseValue = $baseValueParams[(int)$attrParams[2]];
            }
        }

        return $baseValue;
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

    public static function extractMessageBody($body, $payload, $jsonEncode = false) {

        $matchesValues = [];

        preg_match_all('~\{\{msg\.((?:[^\{\}\}]++|(?R))*)\}\}~', $body,$matchesValues);
        $userData = [];
        if (!empty($matchesValues[0])) {
            foreach ($matchesValues[0] as $indexElement => $elementValue) {
                $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($payload, $matchesValues[1][$indexElement], '.');
                $userData[$elementValue] = $valueAttribute['found'] == true ? ($jsonEncode == true ? json_encode($valueAttribute['value']) : $valueAttribute['value']) : null;
            }
        }

        return str_replace(array_keys($userData),array_values($userData), $body);
    }
    
    public static function parseFiles($url, $chat) {

        if (!is_array($url)) {
            $mediaContent = erLhcoreClassModelChatOnlineUser::executeRequest($url);

            // File name
            $partsFilename = explode('/',$url);
            $upload_name = array_pop($partsFilename);

            // File extension
            $partsExtension = explode('.',$url);
            $file_extension = array_pop($partsExtension);

        } else {
            $mediaContent = $url['body'];
            $file_extension = isset($url['ext']) ? (string)$url['ext'] : '';
            $upload_name = isset($url['upload_name']) ? (string)$url['upload_name'] : '';
        }

        if (!empty($mediaContent)) {

            $path = 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/'. $chat->id.'/';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_path', array('path' => & $path, 'storage_id' => $chat->id));

            erLhcoreClassFileUpload::mkdirRecursive( $path );

            $fileUpload = new erLhcoreClassModelChatFile();
            $fileUpload->size = strlen($mediaContent);
            $fileUpload->name = md5($url . time() . rand(0,100));
            $fileUpload->date = time();
            $fileUpload->user_id = 0;
            $fileUpload->upload_name = $upload_name;
            $fileUpload->file_path = $path;
            $fileUpload->chat_id = $chat->id;
            $fileUpload->extension = $file_extension;
            $fileUpload->type = 'application/octet-stream';
            $fileUpload->saveThis();

            // Store content
            file_put_contents($path . $fileUpload->name, $mediaContent);
            chmod($path . $fileUpload->name, 0644);

            if (!is_array($url) || !isset($url['mime'])) {
                $mimeType = erLhcoreClassThemeValidator::get_mime($path . $fileUpload->name);
            } else {
                $mimeType = $url['mime'];
            }

            if ($mimeType !== false) {
                $extension = self::getExtensionByMime($mimeType);
                if ($extension !== false) {
                    $fileUpload->extension = $extension;
                }
            }

            $fileUpload->type = $mimeType !== false ? $mimeType : 'application/octet-stream';
            $fileUpload->saveThis();

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_store', array('chat_file' => $fileUpload));

            return '[file='.$fileUpload->id.'_'.md5($fileUpload->name.'_'.$chat->id).']';
        }
    }

    public function getExtensionByMime($mimeType, $getMime = false) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            'ogg' => 'audio/ogg',

            // images
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mp4' => 'video/mp4',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        if ($getMime == false) {
            return array_search($mimeType,$mime_types);
        } else {
            return $mime_types[$mimeType];
        }
    }

    public function parseFilesBase64($params, $chat) {

        $fileParts = explode('.',$params['file_name']);
        $extension = array_pop($fileParts);

        $returnArray = array();
        $returnArray['mime'] = self::getExtensionByMime($extension, true);
        $returnArray['ext'] = $extension;
        $returnArray['upload_name'] = $params['file_name'];
        $returnArray['body'] = base64_decode($params['body']);

        return self::parseFiles($returnArray, $chat);
    }

    public function parseFilesDecode($params, $chat) {

        $bodyPOST = self::extractMessageBody($params['body_post'], $params['msg'], true);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $params['url']);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyPOST);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        $content = curl_exec($ch);

        $responseBody = json_decode($content,true);

        $bodyRaw = erLhcoreClassGenericBotActionRestapi::extractAttribute($responseBody, $params['response_location'], '.');

        if ($bodyRaw['found'] == 1) {
            $parts = explode(',',$bodyRaw['value']);
            $bodyEncoded = array_pop($parts);
            $returnArray = array();
            if (isset($parts[0])) {
                $returnArray['mime'] = str_replace('data:','',explode(';',$parts[0])[0]);
                $returnArray['ext'] = self::getExtensionByMime($returnArray['mime']);
                $returnArray['upload_name'] = 'file.' . $returnArray['ext'];
            }
            $returnArray['body'] = base64_decode($bodyEncoded);
            return self::parseFiles($returnArray, $chat);
        }

        return null;
    }

    public static function sendMessage($incomingWebhook, $item) {

        $db = ezcDbInstance::get();

        try {
            $db->beginTransaction();

            $incomingChat = erLhcoreClassModelChatIncoming::findOne(array('filter' => array('chat_external_id' => $item->chat_id)));

            if (!($incomingChat instanceof erLhcoreClassModelChatIncoming)) {
                $incomingChat = new erLhcoreClassModelChatIncoming();
                $incomingChat->chat_external_id = str_replace('{chat_id}', $item->chat_id, $incomingWebhook->conditions_array['chat_id_template']);
                $incomingChat->incoming_id = $incomingWebhook->id;
                $incomingChat->utime = time();
                $incomingChat->incoming = $incomingWebhook;
            } else {
                $incomingChat->incoming = $incomingWebhook;
                $incomingChat->incoming_id = $incomingWebhook->id;
            }

            $chat = new erLhcoreClassModelChat();

            if ($item->dep_id > 0) {
                $chat->dep_id = $item->dep_id;
            } else {
                $chat->dep_id = $incomingWebhook->dep_id;
            }

            $chat->nick = $incomingWebhook->name . ' ' . $item->chat_id;
            $chat->time = time();
            $chat->status = 1;
            $chat->hash = erLhcoreClassChat::generateHash();
            $chat->referrer = '';
            $chat->session_referrer = '';
            $chat->chat_variables = json_encode(array(
                'iwh_id' => $incomingWebhook->id
            ));

            $msg = new erLhcoreClassModelmsg();
            $msg->msg = $item->message;

            $worker = 'http';

            if ($item->create_chat == true) {
                $worker = 'resque';

                $chat->saveThis();
                $incomingChat->chat_id = $chat->id;
                $incomingChat->saveThis();

                /**
                 * Store new message
                 */
                $msg->chat_id = $chat->id;
                $msg->user_id =  $item->user_id;
                $msg->name_support = $item->name_support;
                $msg->time = time();
                $msg->saveThis();

                if ($item->close_chat == 1) {
                    $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
                }

                /**
                 * Set appropriate chat attributes
                 */
                $chat->last_msg_id = $msg->id;
                $chat->last_user_msg_time = $msg->time;
                $chat->saveThis();
            }

            $chat->incoming_chat = $incomingChat;
            $incomingChat->chat_id = $chat->id;

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array('wh_worker' => $worker, 'msg' => & $msg, 'chat' => & $chat));

            $db->commit();

            if ($item->create_chat == true) {
                /**
                 * Execute standard callback as chat was started
                 */
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_started', array(
                    'chat' => & $chat,
                    'msg' => $msg
                ));
            }

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        return $chat;
    }

}

?>