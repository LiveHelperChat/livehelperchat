<?php

class erLhcoreClassChatWebhookIncoming {

    public static $staticErrors = [];
    public static $chatInstance = null;
    public static function processEvent($incomingWebhook, array $payload) {

        $conditions = $incomingWebhook->conditions_array;

        if (isset($conditions['main_cond']) && $conditions['main_cond'] != "") {
            if (!self::isValidCondition('main_cond', $conditions, $payload)) {
                throw new Exception('Main conditions does not met!' . json_encode($payload));
            }
        }

        $messages = isset($conditions['messages']) && $conditions['messages'] != '' ? $payload[$conditions['messages']] : [$payload];

        if (isset($conditions['messages']) && $conditions['messages'] != '' &&  isset($conditions['message_direct']) && $conditions['message_direct'] == true) {
            $messages = [$messages];
        }

        $hasDeliveryProcessed = false;

        foreach ($messages as $message) {
            if (self::processDeliveryStatus($incomingWebhook, $message, $payload) === true) {
                $hasDeliveryProcessed = true;
            }
        }

        if ($hasDeliveryProcessed === false) {
            foreach ($messages as $message) {
                self::processMessage($incomingWebhook, $message, $payload);
            }
        }
    }

    public static function processDeliveryStatus($incomingWebhook, $payloadMessage, $payloadAll)
    {
        $conditions = $incomingWebhook->conditions_array;

        foreach (['sent','delivered','read','rejected','reaction','un_reaction','edited','deleted'] as $mainConditionStatus) {
            $messageId = self::extractAttribute('msg_delivery_'.$mainConditionStatus.'_id', $conditions, $payloadMessage, '');
            if ($messageId != '' &&
                (
                    self::isValidCondition('msg_delivery_'.$mainConditionStatus.'_condition', $conditions, $payloadMessage) ||
                    self::isValidCondition('msg_delivery_'.$mainConditionStatus.'_condition', $conditions, $payloadAll)
                )
            ) {
                $chat_id = 'chat_id';
                if (isset($conditions['msg_delivery_'.$mainConditionStatus.'_chat_id']) && !empty($conditions['msg_delivery_'.$mainConditionStatus.'_chat_id'])) {
                    $chat_id = 'msg_delivery_'.$mainConditionStatus.'_chat_id';
                }

                $chat_id_2 = 'chat_id_2';
                if (isset($conditions['msg_delivery_'.$mainConditionStatus.'_chat_id_2']) && !empty($conditions['msg_delivery_'.$mainConditionStatus.'_chat_id_2'])) {
                    $chat_id_2 = 'msg_delivery_'.$mainConditionStatus.'_chat_id_2';
                }

                $chatIdSwitch = self::isValidCondition('chat_id_switch', $conditions, $payloadMessage);

                $chatIdFirst = $chatIdSwitch === true ? $chat_id_2 : $chat_id;
                $chatIdLast = $chatIdSwitch === true ? $chat_id : $chat_id_2;

                $chatIdExternal2 = self::extractAttribute($chatIdLast,$conditions,$payloadMessage);

                $chat_id_original = $conditions[$chatIdFirst];
                foreach (explode('|||',$chat_id_original) as $chat_id) {
                    $conditions[$chatIdFirst] = $chat_id;
                    $chatIdExternal = self::extractAttribute($chatIdFirst,$conditions,$payloadMessage);
                    if ($chatIdExternal != '') {
                        break;
                    }
                }

                if ($chatIdExternal == '') {
                    foreach (explode('|||',$chat_id_original) as $chat_id) {
                        $conditions[$chatIdFirst] = $chat_id;
                        $chatIdExternal = self::extractAttribute($chatIdFirst,$conditions,$payloadAll);
                        if ($chatIdExternal != '') {
                            break;
                        }
                    }
                    $chatIdExternal = self::extractAttribute($chatIdFirst,$conditions,$payloadAll);
                }


                if ($chatIdExternal2 == '') {
                    $chatIdExternal2 = self::extractAttribute($chatIdLast,$conditions,$payloadAll);
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

                $statusMap = [
                    'sent' => erLhcoreClassModelmsg::STATUS_SENT,
                    'delivered' => erLhcoreClassModelmsg::STATUS_DELIVERED,
                    'read' =>  erLhcoreClassModelmsg::STATUS_READ,
                    'rejected' =>  erLhcoreClassModelmsg::STATUS_REJECTED
                ];

                if ($eChat !== false && ($chat = $eChat->chat) !== false && $chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {

                         if (isset($conditions['msg_delivery_'.$mainConditionStatus.'_global']) && $conditions['msg_delivery_'.$mainConditionStatus.'_global'] == 1) {

                             $msgReplyToAll = erLhcoreClassModelmsg::getList(['filter' => ['chat_id' => $chat->id],
                                 'filternotin' => ['user_id' => [-1,0]],
                                 'filterin' => ['del_st' => [erLhcoreClassModelmsg::STATUS_PENDING,erLhcoreClassModelmsg::STATUS_SENT,erLhcoreClassModelmsg::STATUS_DELIVERED]
                             ]]);

                             if (!empty($msgReplyToAll)) {
                                 $msgReplyTo = array_shift($msgReplyToAll);
                             }

                         } else {
                             $msgReplyTo = erLhcoreClassModelmsg::findOne(['filter' => ['chat_id' => $chat->id], 'customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.iwh_msg_id\') = ' . ezcDbInstance::get()->quote($messageId)]]);
                         }

                } elseif (isset($conditions['msg_delivery_'.$mainConditionStatus.'_use_msg_id']) && $conditions['msg_delivery_'.$mainConditionStatus.'_use_msg_id'] == 1) {
                    $msgReplyTo = erLhcoreClassModelmsg::findOne(
                        [
                            'innerjoin' => ['`lh_chat`' => ['`lh_chat`.`id`','`lh_msg`.`chat_id`']],
                            'filterin' => ['`lh_chat`.`status`' => [erLhcoreClassModelChat::STATUS_PENDING_CHAT,erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,erLhcoreClassModelChat::STATUS_BOT_CHAT]],
                            'filter' => ['iwh_id' => $incomingWebhook->id],
                            'customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.iwh_msg_id\') = ' . ezcDbInstance::get()->quote($messageId)]
                        ]);
                    if (is_object($msgReplyTo)) {
                        $chat = erLhcoreClassModelChat::fetch($msgReplyTo->chat_id);
                    }
                }

                if (is_object($msgReplyTo) && isset($chat) && is_object($chat) && $chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
                    if (key_exists($mainConditionStatus,$statusMap) && $msgReplyTo->del_st != erLhcoreClassModelmsg::STATUS_READ) {
                        $msgReplyTo->del_st = max($statusMap[$mainConditionStatus],$msgReplyTo->del_st);
                        $msgReplyTo->updateThis(['update' => ['del_st']]);
                        $chat->operation_admin = "lhinst.updateMessageRowAdmin({$msgReplyTo->chat_id},{$msgReplyTo->id});";

                        // Mark remaining messages to correct status
                        if (!empty($msgReplyToAll)) {
                            foreach ($msgReplyToAll as $msgReplyToAllItem) {

                                $msgReplyToAllItem->del_st = max($statusMap[$mainConditionStatus],$msgReplyToAllItem->del_st);
                                $msgReplyToAllItem->updateThis(['update' => ['del_st']]);

                                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', array('msg' => & $msgReplyToAllItem, 'chat' => & $chat));

                                if (strlen($chat->operation_admin) < 120) {
                                    $chat->operation_admin .= "lhinst.updateMessageRowAdmin({$msgReplyToAllItem->chat_id},{$msgReplyToAllItem->id});";
                                }
                            }
                        }

                        if ($msgReplyTo->del_st == erLhcoreClassModelmsg::STATUS_READ) {
                            $chat->has_unread_op_messages = 0;
                        }
                        $chat->updateThis(['update' => ['operation_admin','has_unread_op_messages']]);
                        // NodeJS to update message delivery status
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', array('msg' => & $msgReplyTo, 'chat' => & $chat, 'reason' => 'delivery_status_change'));
                    } elseif ($mainConditionStatus == 'deleted') {

                        $msgReplyTo->removeThis();
                        $chat->operation_admin = "lhinst.updateMessageRowAdmin({$msgReplyTo->chat_id},{$msgReplyTo->id});\n";
                        $chat->updateThis(array('update' => array('operation_admin')));

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.msg_removed', array('msg' => $msgReplyTo, 'chat' => $chat));

                    } elseif ($mainConditionStatus == 'edited') {

                        $reactionContent = self::extractAttribute('msg_delivery_edited_location', $conditions, $payloadMessage);

                        if (!empty($reactionContent)) {
                            $msgReplyTo->msg = $reactionContent;
                            $msgReplyTo->updateThis(['update' => ['msg']]);
                            $chat->operation_admin = "lhinst.updateMessageRowAdmin({$msgReplyTo->chat_id},{$msgReplyTo->id});";
                            $chat->updateThis(['update' => ['operation_admin']]);
                            
                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', array('msg' => & $msgReplyTo, 'chat' => & $chat, 'reason' => 'content_edited'));
                        }

                    } elseif ($mainConditionStatus == 'reaction') { // Reaction to message
                        $reactionContent = self::extractAttribute('msg_delivery_reaction_location', $conditions, $payloadMessage);
                        if (!empty($reactionContent)) {
                            $metaMsg = $msgReplyTo->meta_msg_array;

                            $reactionId = self::extractAttribute('msg_delivery_'.$mainConditionStatus.'_action_id',$conditions,$payloadMessage,1);

                            $removePrevious = isset($conditions['msg_delivery_'.$mainConditionStatus.'_remove_prev']) && $conditions['msg_delivery_'.$mainConditionStatus.'_remove_prev'] == 1;

                            if (isset($conditions['msg_delivery_'.$mainConditionStatus.'_use_emoji']) && $conditions['msg_delivery_'.$mainConditionStatus.'_use_emoji'] == 1) {
                                $reactionScope = 'current_emoji';
                            } else {
                                $reactionScope = 'current';
                            }

                            if ($removePrevious === true) {
                                $metaMsg['content']['reactions'][$reactionScope] = [];
                            }

                            $metaMsg['content']['reactions'][$reactionScope][$reactionContent] = $reactionId;

                            $msgReplyTo->meta_msg = json_encode($metaMsg);
                            $msgReplyTo->meta_msg_array = $metaMsg;
                            $msgReplyTo->updateThis(['update' => ['meta_msg']]);
                            $chat->operation_admin = "lhinst.updateMessageRowAdmin({$msgReplyTo->chat_id},{$msgReplyTo->id});";
                            $chat->updateThis(['update' => ['operation_admin']]);
                            // NodeJS to update message delivery status
                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', array('msg' => & $msgReplyTo, 'chat' => & $chat,'reason' => 'emoji_add'));
                        } elseif (isset($conditions['msg_delivery_'.$mainConditionStatus.'_remove_if_empty']) && $conditions['msg_delivery_'.$mainConditionStatus.'_remove_if_empty'] == 1) {
                            $mainConditionStatus = 'un_reaction'; // Un-Reaction process should take over
                        }
                    }

                    if ($mainConditionStatus == 'un_reaction') {

                        $reactionId = self::extractAttribute('msg_delivery_'.$mainConditionStatus.'_action_id',$conditions,$payloadMessage,1);

                        if ($reactionId == '') {
                            $reactionId = 1;
                        }

                        $metaMsg = $msgReplyTo->meta_msg_array;

                        if (isset($conditions['msg_delivery_'.$mainConditionStatus.'_use_emoji']) && $conditions['msg_delivery_'.$mainConditionStatus.'_use_emoji'] == 1) {
                            $reactionScope = 'current_emoji';
                        } else {
                            $reactionScope = 'current';
                        }

                        if (is_array($metaMsg['content']['reactions'][$reactionScope]) && !empty($metaMsg['content']['reactions'][$reactionScope]))
                        {
                            $index = array_search($reactionId,$metaMsg['content']['reactions'][$reactionScope]);

                            if ($index !== false) {
                                unset($metaMsg['content']['reactions'][$reactionScope][$index]);
                                $msgReplyTo->meta_msg_array = $metaMsg;
                                $msgReplyTo->meta_msg = json_encode($metaMsg);
                                $msgReplyTo->updateThis(['update' => ['meta_msg']]);

                                $chat->operation_admin = "lhinst.updateMessageRowAdmin({$msgReplyTo->chat_id},{$msgReplyTo->id});";
                                $chat->updateThis(['update' => ['operation_admin']]);
                                // NodeJS to update message delivery status
                                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', array('msg' => & $msgReplyTo, 'chat' => & $chat, 'reason' => 'emoji_remove'));
                            }
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    public static function isValidCondition($field, $conditions, $payloadMessage)
    {
        if (!isset($conditions[$field]) || empty($conditions[$field])) {
            return false;
        }

        $validConditions = true;

        $conditionsPairs = explode("||",$conditions[$field]);

        foreach ($conditionsPairs as $conditionsPair) {
            $conditionsPairData = explode('=', $conditionsPair);

            if (!isset($conditionsPairData[1])) {
                $conditionsPairData[1] = '__exists__';
            }

            $exists = false;
            if ($conditionsPairData[1] === 'false') {
                $conditionsPairData[1] = false;
            } elseif ($conditionsPairData[1] === '__exists__') {
                $exists = true;
            } elseif ($conditionsPairData[1] === 'true') {
                $conditionsPairData[1] = true;
            } elseif (strpos($conditionsPairData[1], ',') !== false) {
                $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
            }

            $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
            $messageValue = $messageData['value'];

            if ($messageData['found'] === false) {
                $validConditions = false;
            }

            if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                $validConditions = false;
            }

            if ($messageData['found'] !== true && $exists == true) {
                $validConditions = false;
            } elseif ($messageData['found'] === true && $exists === true) {
                $validConditions = true;
            }

            if ($validConditions === false) {
                break;
            }
        }

        return $validConditions;
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

                $exists = false;

                if ($conditionsPairData[1] === 'false') {
                    $conditionsPairData[1] = false;
                } elseif ($conditionsPairData[1] === '__exists__') {
                    $exists = true;
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

                if ($messageData['found'] === true && $exists == true) {
                    $typeMessage = 'attachments';
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

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
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

                    if ($messageData['found'] === true && $exists === true) {
                        $typeMessage = 'img';
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

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
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

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'img_2';
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

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
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

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'img_3';
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

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
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

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'img_4';
                    }
                }

                if ($typeMessage == 'img_4') {
                    $msgBody = $conditions['msg_img_4'];
                    $conditionsOperator = isset($conditions['msg_cond_img_4_op']) ? $conditions['msg_cond_img_4_op'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            if (isset($conditions['msg_cond_img_5']) && $conditions['msg_cond_img_5'] != "") {
                $typeMessage = 'img_5';
                $conditionsPairs = explode("||",$conditions['msg_cond_img_5']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=',$conditionsPair);

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
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

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'img_5';
                    }
                }

                if ($typeMessage == 'img_5') {
                    $msgBody = $conditions['msg_img_5'];
                    $conditionsOperator = isset($conditions['msg_cond_img_5_op']) ? $conditions['msg_cond_img_5_op'] : "";
                }
            }
        }

        if ($typeMessage == 'unknown')
        {
            if (isset($conditions['msg_cond_img_6']) && $conditions['msg_cond_img_6'] != "") {
                $typeMessage = 'img_6';
                $conditionsPairs = explode("||",$conditions['msg_cond_img_6']);
                foreach ($conditionsPairs as $conditionsPair) {
                    $conditionsPairData = explode('=',$conditionsPair);

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
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

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'img_6';
                    }
                }

                if ($typeMessage == 'img_6') {
                    $msgBody = $conditions['msg_img_6'];
                    $conditionsOperator = isset($conditions['msg_cond_img_6_op']) ? $conditions['msg_cond_img_6_op'] : "";
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

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
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

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'text';
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

                    $exists = false;
                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    } elseif (isset($conditions['msg_btn_cond_payload_1']) && $conditions['msg_btn_cond_payload_1'] != "") {
                        $startsWithOptions = explode(',',str_replace(' ','',$conditions['msg_btn_cond_payload_1']));
                        $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $buttonBody, '.');
                        if ($messageData['found'] == true && $messageData['value'] != '') {
                            $validStartOption = false;
                            foreach ($startsWithOptions as $startsWithOption) {
                                if (strpos($messageData['value'], $startsWithOption) === 0) {
                                    $validStartOption = true;
                                    break;
                                }
                            }
                            if ($validStartOption === false) {
                                $typeMessage = 'unknown';
                            }
                        } else {
                            $typeMessage = 'unknown';
                        }
                    }

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'button';
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

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
                    }  elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    } elseif (isset($conditions['msg_btn_cond_payload_2']) && $conditions['msg_btn_cond_payload_2'] != "") {
                        $startsWithOptions = explode(',',str_replace(' ','',$conditions['msg_btn_cond_payload_2']));
                        $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $buttonBody, '.');
                        if ($messageData['found'] == true && $messageData['value'] != '') {
                            $validStartOption = false;
                            foreach ($startsWithOptions as $startsWithOption) {
                                if (strpos($messageData['value'], $startsWithOption) === 0) {
                                    $validStartOption = true;
                                    break;
                                }
                            }
                            if ($validStartOption === false) {
                                $typeMessage = 'unknown';
                            }
                        } else {
                            $typeMessage = 'unknown';
                        }
                    }

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'button';
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

                    $exists = false;

                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
                    } elseif ($conditionsPairData[1] === 'true') {
                        $conditionsPairData[1] = true;
                    } elseif (strpos($conditionsPairData[1], ',') !== false) {
                        $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                    }

                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                    $messageValue = $messageData['value'];

                    if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                        $typeMessage = 'unknown';
                    } elseif (isset($conditions['msg_btn_cond_payload_3']) && $conditions['msg_btn_cond_payload_3'] != "") {
                        $startsWithOptions = explode(',',str_replace(' ','',$conditions['msg_btn_cond_payload_3']));
                        $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $buttonBody, '.');
                        if ($messageData['found'] == true && $messageData['value'] != '') {
                            $validStartOption = false;
                            foreach ($startsWithOptions as $startsWithOption) {
                                if (strpos($messageData['value'], $startsWithOption) === 0) {
                                    $validStartOption = true;
                                    break;
                                }
                            }
                            if ($validStartOption === false) {
                                $typeMessage = 'unknown';
                            }
                        } else {
                            $typeMessage = 'unknown';
                        }
                    }

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'button';
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

                    $exists = false;
                    if ($conditionsPairData[1] === 'false') {
                        $conditionsPairData[1] = false;
                    } elseif ($conditionsPairData[1] === '__exists__') {
                        $exists = true;
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

                    if ($messageData['found'] === true && $exists == true) {
                        $typeMessage = 'text';
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

                $exists = false;

                if ($conditionsPairData[1] === 'false') {
                    $conditionsPairData[1] = false;
                } elseif ($conditionsPairData[1] === '__exists__') {
                    $exists = true;
                } elseif ($conditionsPairData[1] === 'true') {
                    $conditionsPairData[1] = true;
                } elseif (strpos($conditionsPairData[1], ',') !== false) {
                    $conditionsPairData[1] = explode(',', $conditionsPairData[1]);
                }

                $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditionsPairData[0], '.');
                $messageValue = $messageData['value'];

                if ($messageData['found'] === false) {
                    $sender = 0;
                }

                if ($messageData['found'] === true && (is_array($conditionsPairData[1]) && !in_array($messageValue, $conditionsPairData[1])) || (!is_array($conditionsPairData[1]) && !(isset($messageValue) && $messageValue == $conditionsPairData[1]))) {
                    $sender = 0;
                }

                if ($messageData['found'] === true && $exists == true) {
                    $sender = -2;
                }
            }
        }

        $chatIdSwitch = self::isValidCondition('chat_id_switch', $conditions, $payloadMessage);

        $chatIdFirst = $chatIdSwitch === true ? 'chat_id_2' : 'chat_id';
        $chatIdLast = $chatIdSwitch === true ? 'chat_id' : 'chat_id_2';

        $chatIdExternal2 = self::extractAttribute($chatIdLast,$conditions,$payloadMessage);

        $chat_id_original = $conditions[$chatIdFirst];
        foreach (explode('|||',$chat_id_original) as $chat_id) {
            $conditions[$chatIdFirst] = $chat_id;
            $chatIdExternal = self::extractAttribute($chatIdFirst,$conditions,$payloadMessage);
            if ($chatIdExternal != '') {
                break;
            }
        }

        if ($chatIdExternal == '') {
            foreach (explode('|||',$chat_id_original) as $chat_id) {
                $conditions[$chatIdFirst] = $chat_id;
                $chatIdExternal = self::extractAttribute($chatIdFirst,$conditions,$payloadAll);
                if ($chatIdExternal != '') {
                    break;
                }
            }
            $chatIdExternal = self::extractAttribute($chatIdFirst,$conditions,$payloadAll);
        }

        if ($chatIdExternal2 == '') {
            $chatIdExternal2 = self::extractAttribute($chatIdLast,$conditions,$payloadAll);
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

        // Check for block against online visitor record
        // It's the only blocking option for third party chats
        $vid = md5($incomingWebhook->id . '_' . $chatIdExternal);

        if ($eChat === false) {

            if ($sender === -2) {
                throw new Exception('First message is operator message. We ignore those and do not start chat in that scenario');
            }

            if (($onlineUser = erLhcoreClassModelChatOnlineUser::fetchByVid($vid)) !== false) {
                if (erLhcoreClassModelChatBlockedUser::isBlocked(['online_user_id' => $onlineUser->id])) {
                    throw new Exception('Blocked by online visitor profile!');
                }
            }

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

                        if (($onlineUser = erLhcoreClassModelChatOnlineUser::fetchByVid($vid)) !== false) {
                            if (erLhcoreClassModelChatBlockedUser::isBlocked(['online_user_id' => $onlineUser->id])) {
                                throw new Exception('Blocked by online visitor profile!');
                            }
                        }

                        if (isset($conditions['chat_status']) && $conditions['chat_status'] == 'active' && $chat->user_id > 0) {
                            $chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
                            $chat->status_sub_sub = erLhcoreClassModelChat::STATUS_SUB_SUB_CLOSED; // Will be used to indicate that we have to show notification for this chat if it appears on list
                        } else {

                            $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                            $chat->status_sub_sub = erLhcoreClassModelChat::STATUS_SUB_SUB_CLOSED; // Will be used to indicate that we have to show notification for this chat if it appears on list

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

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.webhook_incoming_chat_continue', array(
                        'webhook' => & $incomingWebhook,
                        'data' => & $payloadAll,
                        'chat' => & $chat,
                        'echat' => $eChat
                    ));

                    if ($typeMessage == 'img' || $typeMessage == 'img_2' || $typeMessage == 'img_3' || $typeMessage == 'img_4' || $typeMessage == 'img_5' || $typeMessage == 'img_6' || $typeMessage == 'attachments') {
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
                                'file_name_attr' => (isset($conditions['msg_cond_' . $typeMessage . '_file_name']) ? $conditions['msg_cond_' . $typeMessage . '_file_name'] : ''),
                                'file_size_attr' => (isset($conditions['msg_cond_' . $typeMessage . '_file_size']) ? $conditions['msg_cond_' . $typeMessage . '_file_size'] : '')
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

                            $headers = [];
                            if ((isset($conditions['msg_cond_' . $typeMessage . '_url_headers_content']) ? $conditions['msg_cond_' . $typeMessage . '_url_headers_content'] : '')) {
                                $paramsHeader = $payloadMessage;
                                $paramsHeader['incoming_webhook'] = $incomingWebhook;
                                $paramsHeader['incoming_webhook'] ->chat = $chat;
                                $headersParsed = self::extractMessageBody(trim($conditions['msg_cond_' . $typeMessage . '_url_headers_content']), $paramsHeader);
                                $headersItems = explode("\n",trim($headersParsed));
                                foreach ($headersItems as $header) {
                                    $headers[] = $header;
                                }
                            }

                            if ((isset($conditions['msg_cond_' . $typeMessage . '_mime_type']) ? $conditions['msg_cond_' . $typeMessage . '_mime_type'] : '')){
                                $fileNameAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditions['msg_cond_' . $typeMessage . '_mime_type'], '.');
                                if ($fileNameAttribute['found'] == true && is_string($fileNameAttribute['value']) && $fileNameAttribute['value'] !='') {
                                    $overrideAttributes['mime_type'] = $fileNameAttribute['value'];
                                }
                            }

                            $file = self::parseFiles(
                                self::extractAttribute(
                                    'msg_cond_' . $typeMessage . '_body',
                                    $conditions,
                                    $payloadMessage,
                                    (isset($payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']]) ? $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']] : '')),
                                $chat,
                                $headers,
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

                    $last_user_msg_time = $chat->last_user_msg_time;

                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = self::extractMessageBody($msgBody, $payloadMessage);
                    $msg->chat_id = $chat->id;
                    $msg->user_id = $sender;

                    $timeValue = self::extractAttribute('time', $conditions, $payloadMessage, time());
                    $msg->time = is_numeric($timeValue) ? $timeValue : strtotime($timeValue);

                    $externalMessageId = self::extractAttribute('message_id', $conditions, $payloadMessage, '');

                    $metaMessage = [];
                    if (!empty($externalMessageId)) {
                        $metaMessage = ['iwh_msg_id' => $externalMessageId];
                        if (isset($conditions['message_id_uniq']) && $conditions['message_id_uniq'] == 1 && erLhcoreClassModelmsg::getCount(['filter' => ['chat_id' => $chat->id], 'customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.iwh_msg_id\') = ' . ezcDbInstance::get()->quote($externalMessageId)]]) > 0) {
                            throw new Exception('Message with external id of '.$externalMessageId.' already exists!');
                        }
                    }

                    $replyToMessageId = self::extractAttribute('message_id_reply', $conditions, $payloadMessage, '');
                    if (!empty($replyToMessageId)) {
                        $metaReplyMessage = ['reply_to' => ['iwh_msg_id' => $replyToMessageId]];
                        $msgReplyTo = erLhcoreClassModelmsg::findOne(['filter' => ['chat_id' => $chat->id],'customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.iwh_msg_id\') = ' . ezcDbInstance::get()->quote($replyToMessageId)]]);
                        if (is_object($msgReplyTo)) {
                            $metaReplyMessage['reply_to']['db_msg_id'] = $msgReplyTo->id;
                        }
                        $metaMessage['content'] = $metaReplyMessage;
                    }

                    if (!empty($metaMessage)) {
                        $msg->meta_msg = json_encode($metaMessage);
                        $msg->meta_msg_array = $metaMessage;
                    }

                    if ($msg->msg != '') {
                        erLhcoreClassChat::getSession()->save($msg);
                        $chat->last_user_msg_time = $msg->time;
                        $chat->last_msg_id = $msg->id;
                    }

                    $errorMessages = [];
                    if (!empty(self::$staticErrors)) {
                        foreach (self::$staticErrors as $staticError) {
                            $msgError = new erLhcoreClassModelmsg();
                            $msgError->msg = $staticError;
                            $msgError->chat_id = $chat->id;
                            $msgError->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
                            $msgError->user_id = -2;
                            $msgError->time = time() + 1;
                            erLhcoreClassChat::getSession()->save($msgError);
                            $chat->last_msg_id = $msgError->id;
                            $errorMessages[] = $msgError;
                        }
                    }

                    if ($renotify == true) {

                        $department = $chat->department;

                        if ($department !== false) {
                            $chat->priority = $department->priority;
                        }

                        $priority = \erLhcoreClassChatValidator::getPriorityByAdditionalData($chat, array('detailed' => true));

                        if ($priority !== false && $priority['priority'] > $chat->priority) {
                            $chat->priority = $priority['priority'];
                        }
                        
                        if ($priority !== false && $priority['dep_id'] > 0) {
                            $chat->dep_id = $priority['dep_id'];
                            $chat->department = $department = erLhcoreClassModelDepartament::fetch($chat->dep_id);
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

                        if ($priority === false || $priority['skip_bot'] !== true) {
                            if ($msg->msg != '') {
                                erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg));
                            } else {
                                erLhcoreClassChatValidator::setBot($chat, array('ignore_default' => ($typeMessage == 'button')));
                            }
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

                        $isOnline = erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                            'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                            'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
                            'exclude_bot' => true
                        ));

                        if ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT && is_object($responder) && $responder->offline_message != '' && $isOnline === false) {
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
                                $msgResponder->meta_msg = json_encode(['content' => ['auto_responder' => true]]);
                                erLhcoreClassChat::getSession()->save($msgResponder);

                                $chat->last_msg_id = $msgResponder->id;
                            }
                        } elseif ($renotify == true && $chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT && is_object($responder) && $responder->wait_message != '' && $isOnline === true) {

                            $chatVariables['iwh_timeout'] = time();
                            $chat->chat_variables_array = $chatVariables;
                            $chat->chat_variables = json_encode($chatVariables);

                            $msgResponder = new erLhcoreClassModelmsg();
                            $msgResponder->msg = trim($responder->wait_message);
                            $msgResponder->chat_id = $chat->id;
                            $msgResponder->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                            $msgResponder->user_id = -2;
                            $msgResponder->time = time() + 1;
                            $msgResponder->meta_msg = json_encode(['content' => ['auto_responder' => true]]);
                            erLhcoreClassChat::getSession()->save($msgResponder);

                            $chat->last_msg_id = $msgResponder->id;
                        }

                        if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && $chat->auto_responder !== false) {
                            if ($chat->auto_responder->active_send_status != 0 && $last_user_msg_time < $chat->last_op_msg_time) {
                                $chat->auto_responder->active_send_status = 0;
                                $chat->auto_responder->saveThis();
                            }
                        }
                    }

                    if (($chat->nick == 'Visitor' || $chat->nick == '') && isset($conditions['nick']) && $conditions['nick'] != '') {
                        $nick_params = explode('|||',$conditions['nick']);
                        $nickParts = [];
                        foreach ($nick_params as $nick_param) {
                            $conditions['nick'] = $nick_param;
                            $nickParts[] = self::extractAttribute('nick', $conditions, $payloadMessage, '');
                        }
                        $chat->nick = trim(implode(' ',array_filter($nickParts)));
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

                        $country_code = self::extractAttribute('country_code', $conditions, $payloadMessage, $chat->country_code);
                        
                        if (!empty($country_code)) {
                            $chat->country_code = strtolower($country_code);
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
                        'dep_id',
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
                                'meta_msg_meta' => $metaMessage,
                                'type' => 'trigger',
                                'payload' => $payloadParts[1] . '__' . $payloadParts[2],
                                'msg_last_id' => $chat->last_msg_id // Message visitor is clicking is not necessary the last message
                            ));
                        } else if (strpos($buttonPayload, 'bpayload__') === 0) {
                            $payloadParts = explode('__',$buttonPayload);
                            $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                            self::sendBotResponse($chat, $message, array(
                                'meta_msg_meta' => $metaMessage,
                                'type' => 'payload',
                                'payload' => $payloadParts[1] . '__' . $payloadParts[2],
                                'msg_last_id' => $chat->last_msg_id // Message visitor is clicking is not necessary the last message
                            ));
                        } else {

                            $msg_last_id = $chat->last_msg_id;

                            $event = erLhcoreClassGenericBotWorkflow::findEvent($buttonPayload, $chat->gbot_id, 0, array(), array('dep_id' => $chat->dep_id));

                            if (!($event instanceof erLhcoreClassModelGenericBotTriggerEvent)){
                                $event = erLhcoreClassGenericBotWorkflow::findTextMatchingEvent($buttonPayload, $chat->gbot_id, array(), array('dep_id' => $chat->dep_id));
                            }

                            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent){
                                erLhcoreClassGenericBotWorkflow::processTrigger($chat, $event->trigger);
                            } else {
                                // Send default message for unknown button click
                                $bot = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);

                                if (is_object($bot)) {

                                    if ($buttonPayload == 'GET_STARTED') {
                                        $filterButtonEvent = array('default' => 1);
                                    } else {
                                        $filterButtonEvent = array('default_unknown_btn' => 1);
                                    }

                                    $trigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $bot->getBotIds()), 'filter' => $filterButtonEvent));

                                    if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                        erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, false, array('args' => array('msg_text' => $buttonPayload)));
                                    }

                                } else {
                                    $chat->gbot_id = 0;
                                }
                            }

                            self::sendBotResponse($chat, $msg, array('msg_last_id' => ($msg->id > 0 ? $msg->id : $msg_last_id), 'init' => true));
                        }
                    }

                } elseif ($msg->user_id === 0) {
                    self::sendBotResponse($chat, $msg, array('msg_last_id' => ($msg->id > 0 ? $msg->id : $chat->last_msg_id), 'init' => $renotify));
                }

                // If we are not in bot status error messages are not sent.
                if (!empty($errorMessages) && !($chat->gbot_id > 0 && (!isset($chat->chat_variables_array['gbot_disabled']) || $chat->chat_variables_array['gbot_disabled'] == 0))) {
                    foreach ($errorMessages as $errorMessage) {
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                            'chat' => & $chat,
                            'msg' => $errorMessage,
                            'source'=> 'webhook',
                            'no_auto_events' => true    // Some triggers updates last message and webhooks them self sends this event, we want to avoid that
                        ));
                    }
                }

                if ($chat->status != erLhcoreClassModelChat::STATUS_BOT_CHAT && $chat->has_unread_messages != 1) {
                    $chat->has_unread_messages = 1;
                    $chat->updateThis(array('update' => array('has_unread_messages')));
                }

                // Standard event on unread chat messages
                if ($chat->has_unread_messages == 1 && $last_user_msg_time < (time() - 5)) {
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
                        'msg' => $msg,
                        'source' => 'webhook',
                        'webhook' => & $incomingWebhook,
                        'webhook_data' => $payloadAll,
                        'webhook_msg' => $payloadMessage
                    ));

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.addmsguser_webhook', array(
                        'chat' => & $chat,
                        'msg' => $msg,
                        'source' => 'webhook',
                        'webhook' => & $incomingWebhook,
                        'webhook_data' => $payloadAll,
                        'webhook_msg' => $payloadMessage
                    ));

                    // If operator has closed a chat we need force back office sync
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.nodjshelper_notify_delay', array(
                        'chat' => & $chat,
                        'msg' => $msg,
                        'source' => 'webhook',
                        'webhook' => & $incomingWebhook,
                        'webhook_data' => $payloadAll,
                        'webhook_msg' => $payloadMessage
                    ));

                    if ($renotify == true) {
                        // General module signal that it has received an sms
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.restart_chat',array(
                            'chat' => & $chat,
                            'msg' => $msg,
                            'source' => 'webhook',
                            'webhook' => & $incomingWebhook,
                            'webhook_data' => $payloadAll,
                            'webhook_msg' => $payloadMessage
                        ));
                    }
                }

                self::$chatInstance = $chat;

            } else {

                try {

                    $db->beginTransaction();

                    // Save chat
                    $chat = new erLhcoreClassModelChat();

                    $chat->nick = 'Visitor';

                    if (isset($conditions['nick']) && $conditions['nick'] != '') {
                        $nick_params = explode('|||',$conditions['nick']);
                        $nickParts = [];
                        foreach ($nick_params as $nick_param) {
                            $conditions['nick'] = $nick_param;
                            $nickParts[] = self::extractAttribute('nick', $conditions, $payloadMessage, '');
                        }
                        $nickPotentional = trim(implode(' ',array_filter($nickParts)));
                        if (!empty($nickPotentional)) {
                            $chat->nick = $nickPotentional;
                        }
                    }

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

                        $country_code = self::extractAttribute('country_code', $conditions, $payloadMessage, $chat->country_code);

                        if (!empty($country_code)) {
                            $chat->country_code = strtolower($country_code);
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
                        $field_params = explode('|||',$conditions['add_field_value']);
                        $nickParts = [];
                        foreach ($field_params as $nick_param) {
                            $conditions['add_field_value'] = $nick_param;
                            $nickParts[] = self::extractAttribute('add_field_value', $conditions, $payloadMessage, '');
                        }
                        $nickPotentional = trim(implode(' ',array_filter($nickParts)));
                        if (!empty($nickPotentional)) {
                            $chatVariables['iwh_field'] = $nickPotentional;
                        }
                    }

                    if (isset($conditions['add_field_2_value']) && $conditions['add_field_2_value'] != '') {
                        $chatVariables['iwh_field_2'] = self::extractAttribute('add_field_2_value', $conditions, $payloadMessage, '');
                        if (empty($chatVariables['iwh_field_2']) && isset($_GET[$conditions['add_field_2_value']])) {
                            $chatVariables['iwh_field_2'] = (string)$_GET[$conditions['add_field_2_value']];
                        }
                    }

                    if (!empty($chatVariables)) {
                        $chat->chat_variables = json_encode($chatVariables);
                    }

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.webhook_incoming_chat_before_save', array(
                        'webhook' => & $incomingWebhook,
                        'data' => & $payloadAll,
                        'webhook_msg' => $payloadMessage,
                        'chat' => & $chat
                    ));

                    $chat->saveThis();

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.webhook_incoming_chat_started', array(
                        'webhook' => & $incomingWebhook,
                        'data' => & $payloadAll,
                        'webhook_msg' => $payloadMessage,
                        'chat' => & $chat
                    ));

                    if ($typeMessage == 'img' || $typeMessage == 'img_2' || $typeMessage == 'img_3' || $typeMessage == 'img_4' || $typeMessage == 'img_5' || $typeMessage == 'img_6' || $typeMessage == 'attachments') {
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
                                'file_name_attr' => (isset($conditions['msg_cond_' . $typeMessage . '_file_name']) ? $conditions['msg_cond_' . $typeMessage . '_file_name'] : ''),
                                'file_size_attr' => (isset($conditions['msg_cond_' . $typeMessage . '_file_size']) ? $conditions['msg_cond_' . $typeMessage . '_file_size'] : '')
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

                            $headers = [];
                            if ((isset($conditions['msg_cond_' . $typeMessage . '_url_headers_content']) ? $conditions['msg_cond_' . $typeMessage . '_url_headers_content'] : '')) {
                                $paramsHeader = $payloadMessage;
                                $paramsHeader['incoming_webhook'] = $incomingWebhook;
                                $paramsHeader['incoming_webhook'] ->chat = $chat;
                                $headersParsed = self::extractMessageBody(trim($conditions['msg_cond_' . $typeMessage . '_url_headers_content']), $paramsHeader);
                                $headersItems = explode("\n",trim($headersParsed));
                                foreach ($headersItems as $header) {
                                    $headers[] = $header;
                                }
                            }

                            if ((isset($conditions['msg_cond_' . $typeMessage . '_mime_type']) ? $conditions['msg_cond_' . $typeMessage . '_mime_type'] : '')){
                                $fileNameAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $conditions['msg_cond_' . $typeMessage . '_mime_type'], '.');
                                if ($fileNameAttribute['found'] == true && is_string($fileNameAttribute['value']) && $fileNameAttribute['value'] !='') {
                                    $overrideAttributes['mime_type'] = $fileNameAttribute['value'];
                                }
                            }

                            $file = self::parseFiles(
                                self::extractAttribute(
                                    'msg_cond_' . $typeMessage . '_body',
                                    $conditions,
                                    $payloadMessage,
                                    (isset($payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']]) ? $payloadMessage[$conditions['msg_cond_' . $typeMessage . '_body']] : '')),
                                $chat,
                                $headers,
                                $overrideAttributes);

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

                    $externalMessageId = self::extractAttribute('message_id', $conditions, $payloadMessage, '');

                    $metaMessage = [];
                    if (!empty($externalMessageId)) {
                        $metaMessage = ['iwh_msg_id' => $externalMessageId];

                        if (isset($conditions['message_id_uniq']) && $conditions['message_id_uniq'] == 1 && erLhcoreClassModelmsg::getCount(['filter' => ['chat_id' => $chat->id], 'customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.iwh_msg_id\') = ' . ezcDbInstance::get()->quote($externalMessageId)]]) > 0) {
                            throw new Exception('Message with external id of '.$externalMessageId.' already exists!');
                        }
                    }

                    $replyToMessageId = self::extractAttribute('message_id_reply', $conditions, $payloadMessage, '');
                    if (!empty($replyToMessageId)) {
                        $metaReplyMessage = ['reply_to' => ['iwh_msg_id' => $replyToMessageId]];
                        $msgReplyTo = erLhcoreClassModelmsg::findOne(['filter' => ['chat_id' => $chat->id],'customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.iwh_msg_id\') = ' . ezcDbInstance::get()->quote($replyToMessageId)]]);
                        if (is_object($msgReplyTo)) {
                            $metaReplyMessage['reply_to']['db_msg_id'] = $msgReplyTo->id;
                        }
                        $metaMessage['content'] = $metaReplyMessage;
                    }

                    if (!empty($metaMessage)) {
                        $msg->meta_msg = json_encode($metaMessage);
                        $msg->meta_msg_array = $metaMessage;
                    }

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

                    $chatIdSwitch = self::isValidCondition('chat_id_switch', $conditions, $payloadMessage);

                    $chatIdFirst = $chatIdSwitch === true ? 'chat_id_2' : 'chat_id';
                    $chatIdLast = $chatIdSwitch === true ? 'chat_id' : 'chat_id_2';

                    $chatIdExternal2 = self::extractAttribute($chatIdLast, $conditions, $payloadMessage);

                    $eChat->chat_external_id = self::extractAttribute($chatIdFirst, $conditions, $payloadMessage);

                    if ($eChat->chat_external_id == '') {
                        $eChat->chat_external_id = self::extractAttribute($chatIdFirst, $conditions, $payloadAll);
                    }

                    if ($chatIdExternal2 == '') {
                        $chatIdExternal2 = self::extractAttribute($chatIdLast, $conditions, $payloadAll);
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

                    $errorMessages = [];
                    if (!empty(self::$staticErrors)) {
                        foreach (self::$staticErrors as $staticError) {
                            $msgError = new erLhcoreClassModelmsg();
                            $msgError->msg = $staticError;
                            $msgError->chat_id = $chat->id;
                            $msgError->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
                            $msgError->user_id = -2;
                            $msgError->time = time() + 1;
                            erLhcoreClassChat::getSession()->save($msgError);
                            $chat->last_msg_id = $msgError->id;
                            $errorMessages[] = $msgError;
                        }
                    }

                    $chat->incoming_chat = $eChat;

                    $department = $chat->department;

                    if ($department !== false) {
                        $chat->priority = $department->priority;
                    }

                    $priority = \erLhcoreClassChatValidator::getPriorityByAdditionalData($chat, array('detailed' => true));

                    if ($priority !== false && $priority['priority'] > $chat->priority) {
                        $chat->priority = $priority['priority'];
                    }

                    if ($priority !== false && $priority['dep_id'] > 0) {
                        $chat->dep_id = $priority['dep_id'];
                        $chat->department = $department = erLhcoreClassModelDepartament::fetch($chat->dep_id);
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

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.webhook_incoming_chat_before_update_new', array(
                        'webhook' => & $incomingWebhook,
                        'data' => & $payloadAll,
                        'webhook_msg' => $payloadMessage,
                        'chat' => & $chat,
                        'echat' => $eChat
                    ));

                    $chat->updateThis(['update' => [
                        'last_msg_id',
                        'last_user_msg_time',
                        'dep_id',
                        'priority',
                        'transfer_if_na',
                        'transfer_timeout_ts',
                        'transfer_timeout_ac'
                    ]]);

                    // Check that payload context message exists
                    $ignore_default = false;

                    if ($typeMessage == 'button') {
                        $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $buttonBody, '.');
                        if ($messageData['found'] == true && $messageData['value'] != '') {
                            $buttonPayload = $messageData['value'];
                            $payloadParts = explode('__',$buttonPayload);
                            $ignore_default = isset($payloadParts[3]) && is_object(erLhcoreClassModelmsg::fetch($payloadParts[3]));
                            if ($buttonPayload == 'GET_STARTED') {
                                $msgNotice = new erLhcoreClassModelmsg();
                                $msgNotice->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Chat started by') . ' "' . 'GET_STARTED' . '" ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','payload!');
                                $msgNotice->chat_id = $chat->id;
                                $msgNotice->user_id = -1;
                                $msgNotice->time = time();
                                $msgNotice->saveThis();
                            }
                        }
                    }

                    // Set bot
                    if ($msg->id > 0) {
                        erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg, 'ignore_default' => $ignore_default));
                    } else {

                        $argsBots = array('ignore_default' => $ignore_default);

                        if ($ignore_default === false && isset($buttonPayload)) {
                            $argsBots['msg_text'] = $buttonPayload;
                        }

                        erLhcoreClassChatValidator::setBot($chat, $argsBots);
                    }

                    $db->commit();

                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }

                // Release eChat record
                $db->commit();

                if ($typeMessage == 'button' && $ignore_default === true) {
                    $messageData = erLhcoreClassGenericBotActionRestapi::extractAttribute($payloadMessage, $buttonBody, '.');
                    if ($messageData['found'] == true && $messageData['value'] != '') {
                        $buttonPayload = $messageData['value'];
                        if (strpos($buttonPayload, 'trigger__') === 0) {
                            $payloadParts = explode('__',$buttonPayload);
                            $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                            self::sendBotResponse($chat, $message, array(
                                'meta_msg_meta' => $metaMessage,
                                'init' => true,
                                'type' => 'trigger',
                                'payload' => $payloadParts[1] . '__' . $payloadParts[2],
                                'msg_last_id' => $chat->last_msg_id // Message visitor is clicking is not necessary the last message
                            ));
                        } else if (strpos($buttonPayload, 'bpayload__') === 0) {
                            $payloadParts = explode('__',$buttonPayload);
                            $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                            self::sendBotResponse($chat, $message, array(
                                'meta_msg_meta' => $metaMessage,
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

                                if (is_object($bot)) {
                                    if ($buttonPayload == 'GET_STARTED') {
                                        $filterButtonEvent = array('default' => 1);
                                    } else {
                                        $filterButtonEvent = array('default_unknown_btn' => 1);
                                    }

                                    $trigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $bot->getBotIds()), 'filter' => $filterButtonEvent));

                                    if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                                        erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, array('args' => array('msg_text' => $buttonPayload)));
                                    }
                                    
                                } else {
                                    $chat->gbot_id = 0;
                                }
                            }

                            self::sendBotResponse($chat, $msg, array('msg_last_id' => ($msg->id > 0 ? $msg->id : $chat->last_msg_id), 'init' => true));
                        }
                    }

                } elseif ($msg->user_id === 0) {
                    // Commented out. Because if there is no user message stored, means all responses are bot responses.
                    self::sendBotResponse($chat, $msg, array('msg_last_id' => ($msg->id > 0 ? $msg->id :  0 /*$chat->last_msg_id*/), 'init' => true));
                }

                // If we are not in bot status error messages are not sent.
                if (!empty($errorMessages) && !($chat->gbot_id > 0 && (!isset($chat->chat_variables_array['gbot_disabled']) || $chat->chat_variables_array['gbot_disabled'] == 0))) {
                    foreach ($errorMessages as $errorMessage) {
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                            'chat' => & $chat,
                            'msg' => $errorMessage,
                            'source'=> 'webhook',
                            'no_auto_events' => true    // Some triggers updates last message and webhooks them self sends this event, we want to avoid that
                        ));
                    }
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
                        $isOnline = erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                            'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                            'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
                            'exclude_bot' => true
                        ));

                        if ($responder->offline_message != '' && $isOnline === false) {
                            $msg = new erLhcoreClassModelmsg();
                            $msg->msg = trim($responder->offline_message);
                            $msg->chat_id = $chat->id;
                            $msg->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
                            $msg->user_id = -2;
                            $msg->time = time() + 1;
                            $msg->meta_msg = '{"content":{"attr_options":{"wh_delay":4}}}';
                            erLhcoreClassChat::getSession()->save($msg);

                            $messageResponder = $msg;

                            if ($chat->last_msg_id < $msg->id) {
                                $chat->last_msg_id = $msg->id;
                            }

                            $chatVariables['iwh_timeout'] = time();
                            $chat->chat_variables_array = $chatVariables;
                            $chat->chat_variables = json_encode($chatVariables);

                        } elseif ($responder->wait_message != '' && $isOnline === true) {

                            $chatVariables['iwh_timeout'] = time();
                            $chat->chat_variables_array = $chatVariables;
                            $chat->chat_variables = json_encode($chatVariables);

                            $msg = new erLhcoreClassModelmsg();
                            $msg->msg = trim($responder->wait_message);
                            $msg->chat_id = $chat->id;
                            $msg->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                            $msg->user_id = -2;
                            $msg->time = time() + 1;
                            $msg->meta_msg = '{"content":{"attr_options":{"wh_delay":4}}}';

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
                    'msg' => $msg,
                    'webhook_msg' => $payloadMessage,
                    'webhook_data' => $payloadAll,
                    'webhook' => & $incomingWebhook
                ));

                self::$chatInstance = $chat;
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

            // We enable button click payloads for new chats, but not regular message processes.
            //if (!isset($params['init']) || $params['init'] == false) {
                if (isset($params['type']) && $params['type'] == 'payload' && $msg instanceof erLhcoreClassModelmsg) {
                    erLhcoreClassGenericBotWorkflow::processButtonClick($chat, $msg, $params['payload'], array('meta_msg_meta' => (isset($params['meta_msg_meta']) ? $params['meta_msg_meta'] : []), 'processed' => false));
                } else if (isset($params['type']) && $params['type'] == 'trigger' && $msg instanceof erLhcoreClassModelmsg) {
                    erLhcoreClassGenericBotWorkflow::processTriggerClick($chat, $msg, $params['payload'], array('meta_msg_meta' => (isset($params['meta_msg_meta']) ? $params['meta_msg_meta'] : []), 'processed' => false));
                } elseif (!isset($params['init']) || $params['init'] == false) {
                    erLhcoreClassGenericBotWorkflow::userMessageAdded($chat, $msg);
                }
            //}

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
                    'source'=> 'webhook',
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

        $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

        if ((isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) || (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1)) {
            if (isset($overrideAttributes['file_size']) && is_numeric($overrideAttributes['file_size']) && $overrideAttributes['file_size'] > $fileData['fs_max']*1024) {
                self::$staticErrors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File is to big! Maximum') . ' - ' .$fileData['fs_max'] . ' Kb.';
                return erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File is to big!') . ' ' . round($overrideAttributes['file_size'] / 1024, 2) . ' Kb.';
            }
        } else {
            self::$staticErrors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File upload is not for this chat!');
            return erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File upload is not enabled for the visitor!');
        }

        if (!is_array($url)) {

            $mediaContent = erLhcoreClassModelChatOnlineUser::executeRequest(str_replace(' ','%20',trim($url)), $headers, ['timeout' => 60]);

            // File name
            $partsFilename = explode('/',strtok($url, '?'));
            $upload_name = (isset($overrideAttributes['upload_name']) && $overrideAttributes['upload_name'] != '') ? $overrideAttributes['upload_name'] : array_pop($partsFilename);

            // File extension
            $partsExtension = explode('.',strtok($url, '?'));
            $file_extension = array_pop($partsExtension);

            // We want to validate is extension valid one from our defined one
            if (self::getExtensionByMime($file_extension, true) === false) {
                $file_extension = 'bin';
            }

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

            if (isset($overrideAttributes['mime_type']) && !empty($overrideAttributes['mime_type'])) {
                $mimeType = $overrideAttributes['mime_type'];
            } elseif (!is_array($url) || !isset($url['mime'])) {
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
        } else {
            self::$staticErrors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There was a problem with your uploaded file!');
            return erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','We could not download a file!');
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
            'bin' => 'application/octet-stream',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );

        if ($getMime == false) {
            return array_search($mimeType,$mime_types);
        } else {
            return $mime_types[$mimeType] ?? null;
        }
    }

    public static function parseFilesBase64($params, $chat) {

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

        // Set chat for internal variables
        $params['incoming_webhook']->chat = $chat;

        $params['msg']['incoming_webhook'] = $params['incoming_webhook'];

        $bodyPOST = self::extractMessageBody($params['body_post'], $params['msg'], true);
        $headers = [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
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

        $responseLocationArguments = explode('||',$params['response_location']);

        $bodyRaw = erLhcoreClassGenericBotActionRestapi::extractAttribute($responseBody, $responseLocationArguments[0], '.');

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

                if (isset($params['file_size_attr']) && $params['file_size_attr'] != '') {
                    $fileNameAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($params['msg'], $params['file_size_attr'], '.');
                    if ($fileNameAttribute['found'] == true && is_numeric($fileNameAttribute['value']) && $fileNameAttribute['value'] !='') {
                        $overrideAttributes['file_size'] = $fileNameAttribute['value'];
                    }
                }

                if (isset($responseLocationArguments[1]) && !empty($responseLocationArguments[1])){
                    $responseLocationArguments[1] = str_replace('{{response}}',$bodyRaw['value'],$responseLocationArguments[1]);
                    $bodyRaw['value'] = self::extractMessageBody($responseLocationArguments[1], $params['msg'], false);
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
        } else {
            self::$staticErrors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There was a problem with your uploaded file!');
            return erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Media attribute could not be found or there was an error:') . ' ' . $content;
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
