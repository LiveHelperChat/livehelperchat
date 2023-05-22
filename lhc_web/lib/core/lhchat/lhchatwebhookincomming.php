<?php

class erLhcoreClassChatWebhookIncoming {

    public static function processEvent($incomingWebhook, array $payload) {

        $conditions = $incomingWebhook->conditions_array;

        if (isset($conditions['main_cond']) && $conditions['main_cond'] != "") {
            $conditionsPairs = explode("||",$conditions['main_cond']);
            foreach ($conditionsPairs as $conditionsPair) {
                $conditionsPairData = explode('=',$conditionsPair);

                if (isset($conditionsPairData[1])) {
                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }
                } else { // Checks only for existence of attribute

                    if (!isset($payload[$conditionsPairData[0]])) {
                        throw new Exception('Conditional attribute does not exists ['.$conditionsPairData[0].']!' . json_encode($payload));
                    }

                    // All good, attribute exists
                    continue;
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

                $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                $messageValue = $messageData['value'];

                if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
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

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
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

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
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
            if (isset($conditions['msg_cond_img_3']) && $conditions['msg_cond_img_3'] != "") {
                $typeMessage = 'img_3';
                $conditionsPairs = explode("||",$conditions['msg_cond_img_3']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=',$conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'img_3') {
                    $msgBody = $conditions['msg_img_3'];
                    $conditionsOperator = isset($conditions['msg_cond_img_3_op']) ? $conditions['msg_cond_img_3_op'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            if (isset($conditions['msg_cond_img_4']) && $conditions['msg_cond_img_4'] != "") {
                $typeMessage = 'img_4';
                $conditionsPairs = explode("||",$conditions['msg_cond_img_4']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=',$conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'img_4') {
                    $msgBody = $conditions['msg_img_4'];
                    $conditionsOperator = isset($conditions['msg_cond_img_4_op']) ? $conditions['msg_cond_img_4_op'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown' && isset($conditions['msg_body_2']))
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

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'text') {
                    $conditionsOperator = isset($conditions['msg_cond_op_2']) ? $conditions['msg_cond_op_2'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown' && isset($conditions['msg_body_3']))
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

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'text') {
                    $conditionsOperator = isset($conditions['msg_cond_op_3']) ? $conditions['msg_cond_op_3'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown' && isset($conditions['msg_body_4']))
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

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'text') {
                    $conditionsOperator = isset($conditions['msg_cond_op_4']) ? $conditions['msg_cond_op_4'] : "";
                }
            }
        }

        // Button payload 1
        if ($typeMessage == 'unknown' && (
                (isset($conditions['msg_btn_payload_1']) && $conditions['msg_btn_payload_1'] != '') ||
                (isset($conditions['msg_btn_body_1']) && $conditions['msg_btn_body_1'] != '')
            )
        )
        {
            $msgBody = isset($conditions['msg_btn_body_1']) ? $conditions['msg_btn_body_1'] : '';
            $buttonBody = isset($conditions['msg_btn_payload_1']) ? $conditions['msg_btn_payload_1'] : '';

            if (isset($conditions['msg_btn_cond_1']) && $conditions['msg_btn_cond_1'] != "") {
                $typeMessage = 'button';
                $conditionsPairs = explode("||",$conditions['msg_btn_cond_1']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=', $conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'button') {
                    $conditionsOperator = "";
                }
            }
        }

        if ($typeMessage == 'unknown' && (
                (isset($conditions['msg_btn_payload_2']) && $conditions['msg_btn_payload_2'] != '') ||
                (isset($conditions['msg_btn_body_2']) && $conditions['msg_btn_body_2'] != '')
            )
        )
        {
            $msgBody = isset($conditions['msg_btn_body_2']) ? $conditions['msg_btn_body_2'] : '';
            $buttonBody = isset($conditions['msg_btn_payload_2']) ? $conditions['msg_btn_payload_2'] : '';

            if (isset($conditions['msg_btn_cond_2']) && $conditions['msg_btn_cond_2'] != "") {
                $typeMessage = 'button';
                $conditionsPairs = explode("||",$conditions['msg_btn_cond_2']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=', $conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'button') {
                    $conditionsOperator = "";
                }
            }
        }

        if ($typeMessage == 'unknown' && (
                (isset($conditions['msg_btn_payload_3']) && $conditions['msg_btn_payload_3'] != '') ||
                (isset($conditions['msg_btn_body_3']) && $conditions['msg_btn_body_3'] != '')
            )
        )
        {
            $msgBody = isset($conditions['msg_btn_body_3']) ? $conditions['msg_btn_body_3'] : '';
            $buttonBody = isset($conditions['msg_btn_payload_3']) ? $conditions['msg_btn_payload_3'] : '';

            if (isset($conditions['msg_btn_cond_3']) && $conditions['msg_btn_cond_3'] != "") {
                $typeMessage = 'button';
                $conditionsPairs = explode("||",$conditions['msg_btn_cond_3']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=', $conditionsPair);

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    }
                }

                if ($typeMessage == 'button') {
                    $conditionsOperator = "";
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

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
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

        $chatIdExternal2 = self::extractAttribute('chat_id_2',$conditions,$payloadMessage);

        $chatIdExternal = self::extractAttribute('chat_id',$conditions,$payloadMessage);

        if ($chatIdExternal == '') {
            $chatIdExternal = self::extractAttribute('chat_id',$conditions,$payloadAll);
        }

        if ($chatIdExternal2 == '') {
            $chatIdExternal2 = self::extractAttribute('chat_id_2',$conditions,$payloadAll);
        }

        if ($chatIdExternal != '' && isset($conditions['chat_id_preg_rule']) && $conditions['chat_id_preg_rule'] != '') {
            $chatIdExternal = preg_replace($conditions['chat_id_preg_rule'], $conditions['chat_id_preg_value'], $chatIdExternal);
        }

        if ($chatIdExternal2 != '') {
            $chatIdExternal = $chatIdExternal . '__' . $chatIdExternal2;
        }

        $eChat = erLhcoreClassModelChatIncoming::findOne(array(
            'filter' => array(
                'chat_external_id' => $chatIdExternal,
                'incoming_id' => $incomingWebhook->id
            )
        ));

        $db = ezcDbInstance::get();

        if ($eChat === false) {

            $db->beginTransaction();

            $db = ezcDbInstance::get();
            $stmt = $db->prepare("INSERT IGNORE INTO lh_chat_incoming (`chat_external_id`,`incoming_id`) VALUES (:chat_external_id,:incoming_id)");
            $stmt->bindValue( ':chat_external_id',$chatIdExternal);
            $stmt->bindValue( ':incoming_id',$incomingWebhook->id);
            $stmt->execute();
            $lastInsertId = $db->lastInsertId();

            if ($lastInsertId > 0) {
                $eChat = new erLhcoreClassModelChatIncoming();
                $eChat->chat_external_id = $chatIdExternal;
                $eChat->incoming_id = $incomingWebhook->id;
                $eChat->id = $lastInsertId;
            } else {
                $eChat = erLhcoreClassModelChatIncoming::findOne(array(
                    'filter' => array(
                        'chat_external_id' => $chatIdExternal,
                        'incoming_id' => $incomingWebhook->id
                    )
                ));
            }

            $db->commit();
        }

        try {
            // Lock the record START
            $db->beginTransaction();

            $eChat->syncAndLock();

            $continueIfHasChat = false;

            if ($eChat !== false && ($chat = $eChat->chat) !== false) {
                $continueIfHasChat = ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) || ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && !(!isset($conditions['chat_status']) || $conditions['chat_status'] == ""));
            }

            if ($continueIfHasChat == true && $eChat !== false && ($chat = $eChat->chat) !== false ) {

                $db = ezcDbInstance::get();

                try {

                    $db->beginTransaction();

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

                            if (isset($conditions['reset_dep']) && $conditions['reset_dep'] == true) {
                                $chat->dep_id = $incomingWebhook->dep_id;
                            }
                        }
                    }

                    if ($typeMessage == 'img' || $typeMessage == 'img_2' || $typeMessage == 'img_3' || $typeMessage == 'img_4' || $typeMessage == 'attachments') {
                        if (isset($conditions['msg_cond_' . $typeMessage . '_url_decode']) && $conditions['msg_cond_' . $typeMessage . '_url_decode'] != '') {
                            $file = self::parseFilesDecode(array(
                                'msg' => $payloadMessage,
                                'url' => $conditions['msg_cond_' . $typeMessage . '_url_decode'],
                                'body_post' => (isset($conditions['msg_cond_' . $typeMessage . '_url_decode_content']) ? $conditions['msg_cond_' . $typeMessage . '_url_decode_content'] : ''),
                                'response_location' => (isset($conditions['msg_cond_' . $typeMessage . '_url_decode_output']) ? $conditions['msg_cond_' . $typeMessage . '_url_decode_output'] : ''),
                                'request_headers' => (isset($conditions['msg_cond_' . $typeMessage . '_url_headers_content']) ? $conditions['msg_cond_' . $typeMessage . '_url_headers_content'] : ''),
                                'incoming_webhook' => $incomingWebhook,
                                'remote_request_headers' => (isset($conditions['msg_cond_' . $typeMessage . '_url_remote_headers_content']) ? $conditions['msg_cond_' . $typeMessage . '_url_remote_headers_content'] : ''),
                                'is_remote_location' =>  (isset($conditions['msg_cond_' . $typeMessage . '_url_remote_location']) ? $conditions['msg_cond_' . $typeMessage . '_url_remote_location'] : ''),
                                'file_name_attr' => (isset($conditions['msg_cond_' . $typeMessage . '_file_name']) ? $conditions['msg_cond_' . $typeMessage . '_file_name'] : '')
                            ), $chat);

                            if (!empty($file)) {
                                $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']] = $file;
                            }

                        } else if (isset($conditions['msg_' . $typeMessage . '_download']) && $conditions['msg_' . $typeMessage . '_download'] == true) {

                            $overrideAttributes = [];

                            if ((isset($conditions['msg_cond_' . $typeMessage . '_file_name']) ? $conditions['msg_cond_' . $typeMessage . '_file_name'] : '')){
                                $fileNameAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditions['msg_cond_' . $typeMessage . '_file_name'], '.');
                                if ($fileNameAttribute['found'] == true && is_string($fileNameAttribute['value']) && $fileNameAttribute['value'] !='') {
                                    $overrideAttributes['upload_name'] = $fileNameAttribute['value'];
                                }
                            }

                            $file = self::parseFiles(
                                self::extractAttribute(
                                    'msg_cond_' . $typeMessage . '_body',
                                    $conditions,
                                    $payloadMessage,
                                    (isset($payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']]) ? $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']] : '')),
                                $chat,
                                [],
                                $overrideAttributes
                            );

                            if (!empty($file)) {
                                self::array_set_value($payloadMessage, $conditions['msg_cond_' . $typeMessage . '_body'], $file);
                            }

                        } else if (
                            // base64 encoded file
                            strpos($payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']], 'https://') === false &&
                            strpos($payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']], 'http://') === false) {
                            $file = self::parseFilesBase64(array(
                                'body' => $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']],
                                'file_name' => $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_file_name']]), $chat);
                            if (!empty($file)) {
                                $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']] = $file;
                            }
                        }
                    }

                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = self::extractMessageBody($msgBody, $payloadMessage);
                    $msg->chat_id = $chat->id;
                    $msg->user_id = $sender;

                    $timeValue = self::extractAttribute('time', $conditions, $payloadMessage, time());
                    $msg->time = is_numeric($timeValue) ? $timeValue : strtotime($timeValue);

                    if ($msg->msg != '') {
                        erLhcoreClassChat::getSession()->save($msg);

                        $chat->last_user_msg_time = $msg->time;
                        $chat->last_msg_id = $msg->id;
                    }

                    if ($renotify == true) {

                        $department = $chat->department;

                        if ($department !== false) {
                            $chat->priority = $department->priority;
                        }

                        if ($department !== false && $department->department_transfer_id > 0) {
                            if (
                                !(isset($department->bot_configuration_array['off_if_online']) && $department->bot_configuration_array['off_if_online'] == 1 && erLhcoreClassChat::isOnline($chat->dep_id,false, array('exclude_bot' => true, 'exclude_online_hours' => true)) === true) &&
                                !(isset($department->bot_configuration_array['transfer_min_priority']) && is_numeric($department->bot_configuration_array['transfer_min_priority']) && (int)$department->bot_configuration_array['transfer_min_priority'] > $chat->priority)
                            ) {
                                $chat->transfer_if_na = 1;
                                $chat->transfer_timeout_ts = time();
                                $chat->transfer_timeout_ac = $department->transfer_timeout;
                            }
                        }

                        if ($msg->msg != '') {
                            erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg));
                        } else {
                            erLhcoreClassChatValidator::setBot($chat, array('ignore_default' => ($typeMessage == 'button')));
                        }
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
                                'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
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
                                $msgResponder->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                                $msgResponder->user_id = -2;
                                $msgResponder->time = time() + 1;
                                erLhcoreClassChat::getSession()->save($msgResponder);

                                $chat->last_msg_id = $msgResponder->id;


                            }
                        }
                    }

                    if ($chat->nick == 'Visitor' || $chat->nick == '') {
                        $chat->nick = self::extractAttribute('nick', $conditions, $payloadMessage, $chat->nick);
                    }

                    if (isset($conditions['nick_pregmatch']) && $conditions['nick_pregmatch'] != '' && $chat->nick != 'Visitor') {
                        if (!preg_match($conditions['nick_pregmatch'], $chat->nick)) {
                            $chat->nick = 'Visitor';
                        }
                    }

                    if ($chat->nick == 'Visitor') {
                        $chat->nick = self::extractAttribute('nick', $conditions, $payloadAll, $chat->nick);
                    }

                    $chat->phone = self::extractAttribute('phone', $conditions, $payloadMessage, $chat->phone);

                    if ($sender == 0) {
                        $ip = self::extractAttribute('ip', $conditions, $payloadMessage, $chat->ip);

                        if ($ip != '' && $chat->ip != $ip) {
                            $chat->ip = $ip;
                            erLhcoreClassModelChat::detectLocation($chat, "");
                        }
                    }

                    // Some agents triggers to terminate LHC, because we think it's a bot
                    // GoogleBusinessMessage Scenario
                    $_SERVER['HTTP_USER_AGENT'] = 'API, Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.71 Safari/537.36';

                    // Store online visitor record so previous chat workflow works
                    self::assignOnlineVisitor($chat, $eChat);

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
                        'last_msg_id',
                        'transfer_if_na',
                        'transfer_timeout_ts',
                        'transfer_timeout_ac',
                        'priority',
                        'auto_responder_id'
                    )));

                    if (empty($eChat->payload)) {
                        $eChat->payload = json_encode($payloadAll);
                    }

                    $eChat->utime = time();
                    $eChat->updateThis();

                    $chat->incoming_chat = $eChat;

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollback();
                    throw new Exception($e);
                }

                // Release eChat record
                $db->commit();

                if (isset($msgResponder))
                {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                        'chat' => & $chat,
                        'msg' => $msgResponder
                    ));
                }

                // Button payload message type
                if ($typeMessage == 'button') {
                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $buttonBody, '.');
                    if ($messageData['found'] == true && $messageData['value'] != '') {

                        $buttonPayload = $messageData['value'];

                        if (strpos($buttonPayload, 'trigger__') === 0) {
                            $payloadParts = explode('__',$buttonPayload);
                            $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                            self::sendBotResponse($chat, $message, array(
                                'type' => 'trigger',
                                'payload' => $payloadParts[1] . '__' . $payloadParts[2],
                                'msg_last_id' => $chat->last_msg_id // Message visitor is clicking is not necessary the last message
                            ));
                        } else if (strpos($buttonPayload, 'bpayload__') === 0) {
                            $payloadParts = explode('__',$buttonPayload);
                            $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                            self::sendBotResponse($chat, $message, array(
                                'type' => 'payload',
                                'payload' => $payloadParts[1] . '__' . $payloadParts[2],
                                'msg_last_id' => $chat->last_msg_id // Message visitor is clicking is not necessary the last message
                            ));
                        } else {

                            $event = erLhcoreClassGenericBotWorkflow::findEvent($buttonPayload, $chat->gbot_id, 0, array(), array('dep_id' => $chat->dep_id));

                            if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)){
                                $event = erLhcoreClassGenericBotWorkflow::findTextMatchingEvent($buttonPayload, $chat->gbot_id, array(), array('dep_id' => $chat->dep_id));
                            }

                            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent){
                                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $event->trigger);
                            } else {
                                // Send default message for unknown button click
                                $bot = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);

                                $trigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $bot->getBotIds()), 'filter' => array('default_unknown_btn' => 1)));

                                if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, array('args' => array('msg_text' => $buttonPayload)));
                                }
                            }

                            self::sendBotResponse($chat, $msg, array('msg_last_id' => ($msg->id > 0 ? $msg->id : $chat->last_msg_id), 'init' => true));
                        }
                    }

                } else {
                    self::sendBotResponse($chat, $msg, array('msg_last_id' => ($msg->id > 0 ? $msg->id : $chat->last_msg_id), 'init' => $renotify));
                }

                // Standard event on unread chat messages
                if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat', array(
                        'chat' => & $chat
                    ));
                }

                // We dispatch same event as we were using desktop client, because it force admins and users to resync chat for new messages
                // This allows NodeJS users to know about new message. In this particular case it's admin users
                // If operator has opened chat instantly sync
                if ($msg->id > 0) {

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
                }

            } else {

                try {

                    $db->beginTransaction();

                    // Save chat
                    $chat = new erLhcoreClassModelChat();

                    $chat->nick = self::extractAttribute('nick', $conditions, $payloadMessage, 'Visitor');

                    // Perhaps it's first level attribute
                    if ($chat->nick == 'Visitor') {
                        $chat->nick = self::extractAttribute('nick', $conditions, $payloadAll, 'Visitor');
                    }

                    if (isset($conditions['nick_pregmatch']) && $conditions['nick_pregmatch'] != '' && $chat->nick != 'Visitor') {
                        if (!preg_match($conditions['nick_pregmatch'], $chat->nick)) {
                            $chat->nick = 'Visitor';
                        }
                    }

                    $chat->phone = self::extractAttribute('phone', $conditions, $payloadMessage);
                    $chat->email = self::extractAttribute('email', $conditions, $payloadMessage);

                    if ($sender == 0) {
                        $ip = self::extractAttribute('ip', $conditions, $payloadMessage, $chat->ip);

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
                    $chat->iwh_id = $incomingWebhook->id;

                    $chatVariables = [];

                    if (isset($conditions['add_field_value']) && $conditions['add_field_value'] != '') {
                        $chatVariables['iwh_field'] = self::extractAttribute('add_field_value', $conditions, $payloadMessage, '');
                    }

                    if (isset($conditions['add_field_2_value']) && $conditions['add_field_2_value'] != '') {
                        $chatVariables['iwh_field_2'] = self::extractAttribute('add_field_2_value', $conditions, $payloadMessage, '');
                    }

                    if (!empty($chatVariables)) {
                        $chat->chat_variables = json_encode($chatVariables);
                    }

                    $chat->saveThis();

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.webhook_incoming_chat_started', array(
                        'webhook' => & $incomingWebhook,
                        'data' => & $payloadAll,
                        'chat' => & $chat
                    ));

                    if ($typeMessage == 'img' || $typeMessage == 'img_2' || $typeMessage == 'img_3' || $typeMessage == 'img_4' || $typeMessage == 'attachments') {
                        if (isset($conditions['msg_cond_' . $typeMessage . '_url_decode']) && $conditions['msg_cond_' . $typeMessage . '_url_decode'] != '') {
                            $file = self::parseFilesDecode(array(
                                'msg' => $payloadMessage,
                                'url' => $conditions['msg_cond_' . $typeMessage . '_url_decode'],
                                'body_post' => $conditions['msg_cond_' . $typeMessage . '_url_decode_content'],
                                'response_location' => $conditions['msg_cond_' . $typeMessage . '_url_decode_output'],
                                'request_headers' => (isset($conditions['msg_cond_' . $typeMessage . '_url_headers_content']) ? $conditions['msg_cond_' . $typeMessage . '_url_headers_content'] : ''),
                                'incoming_webhook' => $incomingWebhook,
                                'remote_request_headers' => (isset($conditions['msg_cond_' . $typeMessage . '_url_remote_headers_content']) ? $conditions['msg_cond_' . $typeMessage . '_url_remote_headers_content'] : ''),
                                'is_remote_location' =>  (isset($conditions['msg_cond_' . $typeMessage . '_url_remote_location']) ? $conditions['msg_cond_' . $typeMessage . '_url_remote_location'] : ''),
                                'file_name_attr' => (isset($conditions['msg_cond_' . $typeMessage . '_file_name']) ? $conditions['msg_cond_' . $typeMessage . '_file_name'] : '')
                            ), $chat);
                            if (!empty($file)) {
                                $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']] = $file;
                            }
                        } else if (isset($conditions['msg_' . $typeMessage . '_download']) && $conditions['msg_' . $typeMessage . '_download'] == true) {
                            $file = self::parseFiles(
                                self::extractAttribute(
                                    'msg_cond_' . $typeMessage . '_body',
                                    $conditions,
                                    $payloadMessage,
                                    (isset($payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']]) ? $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']] : '')),
                                $chat);
                            if (!empty($file)) {
                                self::array_set_value($payloadMessage, $conditions['msg_cond_' . $typeMessage . '_body'], $file);
                            }
                        } else if (
                            // base64 encoded file
                            strpos($payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']], 'https://') === false &&
                            strpos($payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']], 'http://') === false) {
                            $file = self::parseFilesBase64(array('body' => $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']], 'file_name' => $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_file_name']]), $chat);
                            if (!empty($file)) {
                                $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']] = $file;
                            }
                        }
                    }

                    // Save message
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = self::extractMessageBody($msgBody, $payloadMessage);
                    $msg->chat_id = $chat->id;
                    $msg->user_id = $sender;

                    if ($msg->msg != '') {
                        $timeValue = self::extractAttribute('time', $conditions, $payloadMessage, time());
                        $msg->time = is_numeric($timeValue) ? $timeValue : strtotime($timeValue);

                        erLhcoreClassChat::getSession()->save($msg);
                    }

                    // Save external chat
                    $eChat = ($eChat instanceof erLhcoreClassModelChatIncoming) ? $eChat : (new erLhcoreClassModelChatIncoming());

                    if ($eChat->chat_id > 0) {
                        $previousChat = erLhcoreClassModelChat::fetch($eChat->chat_id);
                    }

                    $chatIdExternal2 = self::extractAttribute('chat_id_2', $conditions, $payloadMessage);

                    $eChat->chat_external_id = self::extractAttribute('chat_id', $conditions, $payloadMessage);

                    if ($eChat->chat_external_id == '') {
                        $eChat->chat_external_id = self::extractAttribute('chat_id', $conditions, $payloadAll);
                    }

                    if ($chatIdExternal2 == '') {
                        $chatIdExternal2 = self::extractAttribute('chat_id_2', $conditions, $payloadAll);
                    }

                    if ($eChat->chat_external_id == '') {
                        throw new Exception('ChatId attribute could not be found!');
                    }

                    if (isset($conditions['chat_id_preg_rule']) && $conditions['chat_id_preg_rule'] != '') {
                        $eChat->chat_external_id = preg_replace($conditions['chat_id_preg_rule'], $conditions['chat_id_preg_value'], $eChat->chat_external_id);
                    }

                    if ($chatIdExternal2 != '') {
                        $eChat->chat_external_id = $eChat->chat_external_id . '__' . $chatIdExternal2;
                    }

                    $eChat->incoming_id = $incomingWebhook->id;
                    $eChat->chat_id = $chat->id;
                    $eChat->utime = time();
                    $eChat->payload = json_encode($payloadAll);
                    $eChat->saveThis();

                    /**
                     * Set appropriate chat attributes
                     */
                    if ($msg->id > 0) {
                        $chat->last_msg_id = $msg->id;
                        $chat->last_user_msg_time = $msg->time;
                    }

                    $chat->incoming_chat = $eChat;

                    $department = $chat->department;

                    if ($department !== false) {
                        $chat->priority = $department->priority;
                    }

                    if ($department !== false && $department->department_transfer_id > 0) {
                        if (
                            !(isset($department->bot_configuration_array['off_if_online']) && $department->bot_configuration_array['off_if_online'] == 1 && erLhcoreClassChat::isOnline($chat->dep_id,false, array('exclude_bot' => true, 'exclude_online_hours' => true)) === true) &&
                            !(isset($department->bot_configuration_array['transfer_min_priority']) && is_numeric($department->bot_configuration_array['transfer_min_priority']) && (int)$department->bot_configuration_array['transfer_min_priority'] > $chat->priority)
                        ) {
                            $chat->transfer_if_na = 1;
                            $chat->transfer_timeout_ts = time();
                            $chat->transfer_timeout_ac = $department->transfer_timeout;
                        }
                    }

                    $chat->updateThis(['update' => [
                        'last_msg_id',
                        'last_user_msg_time',
                        'dep_id',
                        'priority',
                        'transfer_if_na',
                        'transfer_timeout_ts',
                        'transfer_timeout_ac'
                    ]]);

                    // Set bot
                    if ($msg->id > 0) {
                        erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg, 'ignore_default' => ($typeMessage == 'button')));
                    } else {
                        erLhcoreClassChatValidator::setBot($chat, array('ignore_default' => ($typeMessage == 'button')));
                    }

                    $db->commit();

                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }

                // Release eChat record
                $db->commit();

                if ($typeMessage == 'button') {
                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $buttonBody, '.');
                    if ($messageData['found'] == true && $messageData['value'] != '') {

                        $buttonPayload = $messageData['value'];

                        if (strpos($buttonPayload, 'trigger__') === 0) {
                            $payloadParts = explode('__',$buttonPayload);
                            $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                            self::sendBotResponse($chat, $message, array(
                                'init' => true,
                                'type' => 'trigger',
                                'payload' => $payloadParts[1] . '__' . $payloadParts[2],
                                'msg_last_id' => $chat->last_msg_id // Message visitor is clicking is not necessary the last message
                            ));
                        } else if (strpos($buttonPayload, 'bpayload__') === 0) {
                            $payloadParts = explode('__',$buttonPayload);
                            $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                            self::sendBotResponse($chat, $message, array(
                                'init' => true,
                                'type' => 'payload',
                                'payload' => $payloadParts[1] . '__' . $payloadParts[2],
                                'msg_last_id' => $chat->last_msg_id // Message visitor is clicking is not necessary the last message
                            ));
                        } else {

                            $event = erLhcoreClassGenericBotWorkflow::findEvent($buttonPayload, $chat->gbot_id, 0, array(), array('dep_id' => $chat->dep_id));

                            if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)){
                                $event = erLhcoreClassGenericBotWorkflow::findTextMatchingEvent($buttonPayload, $chat->gbot_id, array(), array('dep_id' => $chat->dep_id));
                            }

                            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent){
                                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $event->trigger);
                            } else {
                                // Send default message for unknown button click
                                $bot = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);

                                $trigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $bot->getBotIds()), 'filter' => array('default_unknown_btn' => 1)));

                                if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, array('args' => array('msg_text' => $buttonPayload)));
                                }
                            }

                            self::sendBotResponse($chat, $msg, array('msg_last_id' => ($msg->id > 0 ? $msg->id : $chat->last_msg_id), 'init' => true));
                        }
                    }

                } else {
                    self::sendBotResponse($chat, $msg, array('msg_last_id' => ($msg->id > 0 ? $msg->id : $chat->last_msg_id), 'init' => true));
                }

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
                $chat->updateThis();

                if (!isset($_SERVER['HTTP_USER_AGENT'])) {
                    $_SERVER['HTTP_USER_AGENT'] = 'API, Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.71 Safari/537.36';
                }

                // Store online visitor record so previous chat workflow works
                self::assignOnlineVisitor($chat, $eChat);

                // If previous chat did not had online record associated assign in
                if (isset($previousChat) && $previousChat instanceof erLhcoreClassModelChat && $previousChat->online_user_id == 0 && $chat->online_user_id > 0) {
                    $previousChat->online_user_id = $chat->online_user_id;
                    $previousChat->updateThis(['update' => ['online_user_id']]);
                }

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



        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

    }

    public static function assignOnlineVisitor(& $chat, $eChat)
    {
        if ($chat->online_user_id == 0 && erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1) {
            $vid = md5($eChat->incoming_id . '_' . $eChat->chat_external_id);
            $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array(
                'pages_count' => true,
                'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value,
                'vid' => $vid));

            $userInstance->chat_id = $chat->id;
            $userInstance->dep_id = $chat->dep_id;

            if ($userInstance->visitor_tz == '') {
                $userInstance->visitor_tz = $chat->user_tz_identifier;
            }

            $userInstance->updateThis(['update' => ['chat_id','dep_id','visitor_tz']]);

            $chat->online_user_id = $userInstance->id;
            $chat->updateThis(['update' => ['online_user_id']]);
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

            $lastMessageIdNew = $lastMessageId = $chat->last_msg_id;

            if (!isset($params['init']) || $params['init'] == false) {
                if (isset($params['type']) && $params['type'] == 'payload' && $msg instanceof erLhcoreClassModelmsg) {
                    erLhcoreClassGenericBotWorkflow::processButtonClick($chat, $msg, $params['payload'], array('processed' => false));
                } else if (isset($params['type']) && $params['type'] == 'trigger' && $msg instanceof erLhcoreClassModelmsg) {
                    erLhcoreClassGenericBotWorkflow::processTriggerClick($chat, $msg, $params['payload'], array('processed' => false));
                } else {
                    erLhcoreClassGenericBotWorkflow::userMessageAdded($chat, $msg);
                }
            }

            if (!isset($params['msg_last_id'])) {
                $params['msg_last_id'] = $msg->id;
            }

            // Find a new messages
            $botMessages = erLhcoreClassModelmsg::getList(array('filter' => array('user_id' => -2, 'chat_id' => $chat->id), 'filtergt' => array('id' => $params['msg_last_id'])));
            foreach ($botMessages as $botMessage) {

                $lastMessageIdNew = $botMessage->id;

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                    'chat' => & $chat,
                    'msg' => $botMessage,
                    'no_auto_events' => true    // Some triggers updates last message and webhooks them self sends this event, we want to avoid that
                ));
            }

            if ($lastMessageId < $lastMessageIdNew) {
                $chat->last_msg_id = $lastMessageIdNew;
                $chat->updateThis(['update' => ['last_msg_id']]);
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
    
    public static function parseFiles($url, $chat, $headers = [], $overrideAttributes = []) {

        if (!is_array($url)) {

            $mediaContent = erLhcoreClassModelChatOnlineUser::executeRequest(str_replace(' ','%20',trim($url)), $headers);

            // File name
            $partsFilename = explode('/',strtok($url, '?'));
            $upload_name = (isset($overrideAttributes['upload_name']) && $overrideAttributes['upload_name'] != '') ? $overrideAttributes['upload_name'] : array_pop($partsFilename);

            // File extension
            $partsExtension = explode('.',strtok($url, '?'));
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
                $mimeType = trim(explode(';',$mimeType)[0]);
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

    public static function getExtensionByMime($mimeType, $getMime = false) {
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
            'webm' => 'video/x-matroska',

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
            'webp' => 'image/webp',

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
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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

    public static function parseFilesDecode($params, $chat) {

        $bodyPOST = self::extractMessageBody($params['body_post'], $params['msg'], true);
        $headers = [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($bodyPOST != '') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyPOST);
            $headers[] ='Content-Type: application/json';
        }

        curl_setopt($ch, CURLOPT_URL, self::extractMessageBody($params['url'], $params['msg']));
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (isset($params['request_headers']) && trim($params['request_headers']) != '') {
            $paramsHeader = $params['msg'];
            $paramsHeader['incoming_webhook'] = $params['incoming_webhook'];
            $headersParsed = self::extractMessageBody(trim($params['request_headers']), $paramsHeader);
            $headersItems = explode("\n",trim($headersParsed));
            foreach ($headersItems as $header) {
                $headers[] = $header;
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $content = curl_exec($ch);

        $responseBody = json_decode($content,true);

        $bodyRaw = erLhcoreClassGenericBotActionRestapi::extractAttribute($responseBody, $params['response_location'], '.');

        if ($bodyRaw['found'] == 1) {
            if (isset($params['is_remote_location']) && $params['is_remote_location'] == true) {
                $paramsHeader = $params['msg'];
                $paramsHeader['incoming_webhook'] = $params['incoming_webhook'];
                $headersParsed = self::extractMessageBody(trim($params['remote_request_headers']), $paramsHeader);

                $overrideAttributes = [];
                if (isset($params['file_name_attr']) && $params['file_name_attr'] != '') {
                    $fileNameAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($params['msg'], $params['file_name_attr'], '.');
                    if ($fileNameAttribute['found'] == true && is_string($fileNameAttribute['value']) && $fileNameAttribute['value'] !='') {
                        $overrideAttributes['upload_name'] = $fileNameAttribute['value'];
                    }
                }

                return self::parseFiles($bodyRaw['value'], $chat, explode("\n",trim($headersParsed)), $overrideAttributes);
            } else {
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
            $chat->iwh_id = $incomingWebhook->id;

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

            $incomingChat->chat_id = $chat->id;
            $chat->incoming_chat = $incomingChat;

            $db->commit();

            $chat->last_message = $msg;

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array('wh_worker' => $worker, 'msg' => & $msg, 'chat' => & $chat));

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
