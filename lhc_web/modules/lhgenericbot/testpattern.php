<?php

if (isset($_POST['mail'])){
    $response = "";

    $chat = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conversation = $chat->conversation;

    if ($conversation instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conversation)) {

        if (isset($_POST['extract_action'])) {
            $patterns = [];
            foreach ($chat->getState() as $stateKey => $stateAttr) {
                $patterns[] = '{args.chat.' . $stateKey .'} = ' . $stateAttr;
            }

            $subAttributes = [
                'conversation' => ['mail_variables_array','customer_email','interaction_time_duration','plain_user_name','user','last_mail_front',
                    'conv_duration_front','wait_time_response','wait_time_pending','department_name','can_delete','subject_front','mailbox','mailbox_front','department',
                    'opened_at_front', 'pnd_time_front' , 'ctime_front', 'udate_front', 'accept_time_front', 'cls_time_front'
                    , 'lr_time_front', 'pnd_time_front_ago', 'ctime_front_ago', 'udate_front_ago', 'accept_time_front_ago', 'cls_time_front_ago' , 'lr_time_front_ago'],
                'department' => ['bot_configuration_array','is_overloaded','is_online'],
            ];

            foreach (['conversation','ctime_front','udate_front','accept_time_front','cls_time_front','lr_time_front','opened_at_front','udate_ago',',conv_duration_front','user','mailbox',
                         'wait_time_pending','wait_time_response','interaction_time_duration','files','attachments','subjects', 'to_data_array' , 'reply_to_data_array'
                         , 'cc_data_array', 'bcc_data_array', 'delivery_status_keyed', 'to_data_keyed', 'reply_to_data_keyed', 'cc_data_keyed'
                         , 'bcc_data_keyed', 'to_data_front', 'reply_to_data_front', 'cc_data_front', 'bcc_data_front'] as $dynamicAttr) {
                if (is_object($chat->{$dynamicAttr})) {
                    foreach ($chat->{$dynamicAttr}->getState() as $stateKey => $stateAttr) {
                        $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $stateKey .'} = ' . ((is_array($stateAttr) || is_object($stateAttr)) ? json_encode($stateAttr) : $stateAttr);
                    }
                    if (isset($subAttributes[$dynamicAttr])){
                        foreach ($subAttributes[$dynamicAttr] as $subStateAttr) {
                            foreach ($chat->{$dynamicAttr}->{$subStateAttr} as $stateSubKey => $subStateAttrVal) {
                                $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $subStateAttr . '.' . $stateSubKey . '} = ' . ((is_array($subStateAttrVal) || is_object($subStateAttrVal)) ? json_encode($subStateAttrVal) : $subStateAttrVal);
                            }
                        }
                    }
                } elseif (is_array($chat->{$dynamicAttr})) {
                    foreach ($chat->{$dynamicAttr} as $stateKey => $stateAttr) {
                        $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $stateKey .'} = ' . ((is_array($stateAttr) || is_object($stateAttr)) ? json_encode($stateAttr) : $stateAttr);
                    }
                } elseif (is_string($chat->{$dynamicAttr}) || is_numeric($chat->{$dynamicAttr}) || is_bool($chat->{$dynamicAttr})) {
                    $patterns[] = '{args.chat.' . $dynamicAttr .'} = ' . ((is_array($chat->{$dynamicAttr}) || is_object($chat->{$dynamicAttr})) ? json_encode($chat->{$dynamicAttr}) : $chat->{$dynamicAttr});
                }
            }

            $response = implode("\n",$patterns);

        } elseif (isset($_POST['text_pattern'])) {

            if ($_POST['test_pattern'][0] === "{") {
                $attribute = erLhcoreClassGenericBotWorkflow::translateMessage($_POST['test_pattern'], array('as_json' => true, 'chat' => $chat, 'args' => ['chat' => $chat]));
            } else {
                $attribute = $_POST['test_pattern'];
            }

            $response = var_export(erLhcoreClassChatValidator::conditionsMatches([['field' => $attribute, 'comparator' => $_POST['comparator'], 'value' => $_POST['text_pattern']]],[]), true);

        } else {
            $response = erLhcoreClassGenericBotWorkflow::translateMessage($_POST['test_pattern'], array('as_json' => true, 'chat' => $chat, 'args' => ['chat' => $chat]));
            if (strpos($response,'{') === 0) {
                $response = json_encode(json_decode($response,true), JSON_PRETTY_PRINT);
            }
        }
    } else {
        $response = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Mail message does not exists or you do not have permission to access it!');
    }

} elseif (isset($_POST['raw_value'])) {
    $response = var_export(erLhcoreClassChatValidator::conditionsMatches([['field' => $_POST['raw_value'], 'comparator' => $_POST['comparator'], 'value' => $_POST['text_pattern']]],[]), true);
} else {
    $chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['id']);

    $response = "";

    if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat)) {

        if (isset($_POST['priority_id'])) {

            $dataTest = erLhcoreClassChatValidator::getPriorityByAdditionalData($chat, array('priority_id' => $_POST['priority_id'], 'detailed' => true, 'log_if_needed' => true));

            if (isset(erLhcoreClassChatValidator::$routingActions['chat_chat'])){
                unset(erLhcoreClassChatValidator::$routingActions['chat_chat']);
            }

            if ($dataTest !== false) {
                $response = '✔️' . "\n" . json_encode($dataTest, JSON_PRETTY_PRINT) . "\n" . json_encode(erLhcoreClassChatValidator::$routingActions, JSON_PRETTY_PRINT);
            } else {
                $response = '❌️' . "\n" . json_encode(erLhcoreClassChatValidator::$routingActions, JSON_PRETTY_PRINT);
            }

            echo htmlspecialchars($response);
            exit;

        } elseif (isset($_POST['translation_id'])) {
            $trItem = erLhcoreClassModelGenericBotTrItem::fetch($_POST['translation_id']);
            $response = erLhcoreClassGenericBotWorkflow::translateMessage('{' . $trItem->identifier . '__Not found}', array('chat' => $chat, 'args' => ['chat' => $chat]));
            echo htmlspecialchars($response);
            exit;
        } elseif (isset($_POST['replaceable_id'])) {
            $replaceable = erLhcoreClassModelCannedMsgReplace::fetch($_POST['replaceable_id']);
            $response = $replaceable->getValueReplace(['chat' => $chat, 'user' => $chat->user]);
            if (empty($response)){
                $response = 'n/a';
            }
        } elseif (isset($_POST['condition_id'])) {
            $conditionToValidate = \LiveHelperChat\Models\Bot\Condition::fetch($_POST['condition_id']);
            $response = $conditionToValidate->isValid(['chat' => $chat]) === true ? '✔️' : '❌';
            $response .= "\n".json_encode(erLhcoreClassGenericBotWorkflow::$triggerNameDebug, JSON_PRETTY_PRINT);
            echo htmlspecialchars($response);
            exit;

        } elseif (isset($_POST['extract_action'])) {
            $patterns = [];
            foreach ($chat->getState() as $stateKey => $stateAttr) {
                $patterns[] = '{args.chat.' . $stateKey .'} = ' . $stateAttr;
            }

            $subAttributes = [
                'online_user' => ['online_attr_system_array','online_attr_array','current_page_params','previous_chat','chat'],
                'department' => ['bot_configuration_array','is_overloaded','is_online'],
                'iwh' => ['conditions_array'],
                'incoming_chat' => ['incoming']
            ];

            foreach (['referrer_params','abnd','drpd','subject_ids','subject_ids_list','department','chat_variables_array','user','online_user',
                         'wait_time_pending','incoming_chat','iwh','bot','user_status_front','chat_dynamic_array','aalert','aicons','msg_v','additional_data_array','user_tz_identifier_time',
                         'unread_time','screenshot','number_in_queue','department_name','department_role','auto_responder','n_off_full','n_official','hum','plain_user_name','user_name','chat_duration_front','pnd_rsp',
                         'wait_time_pending','last_user_msg_time_front','start_last_action_front','wait_time_front','last_msg_time_front','last_msg_time','wait_time_seconds','is_user_typing','can_edit_chat','is_operator_typing',
                         'user_closed_ts_front','cls_time_front','pnd_time_front','time_created_front','last_msg'
                     ] as $dynamicAttr) {
                if ($dynamicAttr == 'abnd') {
                    $debugString = ' ((' . $chat->lsync .'[lsync] < (' . $chat->pnd_time .'[pnd_time]+' . $chat->wait_time . '[wait_time]) &&' . $chat->wait_time .'[wait_time]> 1) || (' . $chat->lsync . '[lsync] >  (' . $chat->pnd_time . '[pnd_time]+' . $chat->wait_time . '[wait_time]) && ' . $chat->wait_time. '[wait_time] > 1 && ' . $chat->user_id . '[user_id] == 0) | Visitor left before chat was accepted';
                    $patterns[] = '{debug.'.$dynamicAttr .'} = ' . (($chat->lsync < ($chat->pnd_time + $chat->wait_time) && $chat->wait_time > 1) || ($chat->lsync > ($chat->pnd_time + $chat->wait_time) && $chat->wait_time > 1 && $chat->user_id == 0) ? 1 : 0) . $debugString;
                } elseif ($dynamicAttr == 'drpd') {
                    $debugString = ' ('.$chat->lsync .'[lsync] >  (' . $chat->pnd_time . '[pnd_time] + ' . $chat->wait_time . '[wait_time]) && ' . $chat->has_unread_op_messages.'[has_unread_op_messages] == 1 && ' . $chat->user_id. '[user_id] > 0 )';
                    $patterns[] = '{debug.'.$dynamicAttr .'} = ' . ($chat->lsync > ($chat->pnd_time + $chat->wait_time) && $chat->has_unread_op_messages == 1 && $chat->user_id > 0 ? 1 : 0) . $debugString . ' Visitor was online while chat was accepted, but left before operator replied';
                } elseif (is_object($chat->{$dynamicAttr})) {
                    foreach ($chat->{$dynamicAttr}->getState() as $stateKey => $stateAttr) {
                        if (is_array($stateAttr) || is_object($stateAttr)) {
                            foreach ($stateAttr as $stateAttrKey => $stateAttrValue) {
                                if (is_array($stateAttrValue) || is_object($stateAttrValue)){
                                    foreach ($stateAttrValue as $stateSubAttrValueKey => $stateSubAttrValue){
                                        $patterns[] = '{args.chat.' . $dynamicAttr . '.' . $stateKey . ' .' . $stateAttrKey . '.' . $stateSubAttrValueKey . '} = ' . ((is_array($stateSubAttrValue) || is_object($stateSubAttrValue)) ? json_encode($stateSubAttrValue) : $stateSubAttrValue);
                                    }
                                } else {
                                    $patterns[] = '{args.chat.' . $dynamicAttr . '.' . $stateKey . ' .' . $stateAttrKey .' } = ' . ((is_array($stateAttrValue) || is_object($stateAttrValue)) ? json_encode($stateAttrValue) : $stateAttrValue);
                                }
                            }
                        } else {
                            $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $stateKey .'} = ' . $stateAttr;
                        }
                    }
                    if (isset($subAttributes[$dynamicAttr])){
                        foreach ($subAttributes[$dynamicAttr] as $subStateAttr) {
                            foreach ($chat->{$dynamicAttr}->{$subStateAttr} as $stateSubKey => $subStateAttrVal) {
                                $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $subStateAttr . '.' . $stateSubKey . '} = ' . ((is_array($subStateAttrVal) || is_object($subStateAttrVal)) ? json_encode($subStateAttrVal) : $subStateAttrVal);
                            }
                        }
                    }
                } elseif (is_array($chat->{$dynamicAttr})) {
                    foreach ($chat->{$dynamicAttr} as $stateKey => $stateAttr) {
                        $patterns[] = '{args.chat.' . $dynamicAttr .'.' . $stateKey .'} = ' . ((is_array($stateAttr) || is_object($stateAttr)) ? json_encode($stateAttr) : $stateAttr);
                    }
                } elseif (is_string($chat->{$dynamicAttr}) || is_numeric($chat->{$dynamicAttr}) || is_bool($chat->{$dynamicAttr})) {
                    $patterns[] = '{args.chat.' . $dynamicAttr .'} = ' . ((is_array($chat->{$dynamicAttr}) || is_object($chat->{$dynamicAttr})) ? json_encode($chat->{$dynamicAttr}) : $chat->{$dynamicAttr});
                }
            }

            $response = implode("\n",$patterns);

        } elseif (isset($_POST['text_pattern'])) {

            if ($_POST['test_pattern'][0] === "{") {
                $attribute = erLhcoreClassGenericBotWorkflow::translateMessage($_POST['test_pattern'], array('as_json' => true, 'chat' => $chat, 'args' => ['chat' => $chat]));
            } else {
                $attribute = $_POST['test_pattern'];
            }

            $response = var_export(erLhcoreClassChatValidator::conditionsMatches([['field' => $attribute, 'comparator' => $_POST['comparator'], 'value' => $_POST['text_pattern']]],[]), true);

        } else {
            $response = erLhcoreClassGenericBotWorkflow::translateMessage($_POST['test_pattern'], array('as_json' => true, 'chat' => $chat, 'args' => ['chat' => $chat]));
            if (strpos($response,'{') === 0) {
                $response = json_encode(json_decode($response,true), JSON_PRETTY_PRINT);
            }
        }
    } else {
        $response = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Chat does not exists or you do not have permission to access it!');
    }
}

echo nl2br(htmlspecialchars($response));
exit;