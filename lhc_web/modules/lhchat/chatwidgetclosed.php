<?php

// For IE to support headers if chat is installed on different domain
erLhcoreClassRestAPIHandler::setHeaders();
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time() + 60 * 60 * 8) . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');


// Check is there online user instance and user has messsages from operator in that case he have seen message from operator
if (erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1) {

    $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'vid' => $Params['user_parameters_unordered']['vid']));

    if ($userInstance !== false && $userInstance->has_message_from_operator == true) {

        // Finish conversion
        if ($userInstance->conversion_id > 0) {
            $conversionUser = erLhAbstractModelProactiveChatCampaignConversion::fetch($userInstance->conversion_id);
            if ($conversionUser instanceof erLhAbstractModelProactiveChatCampaignConversion) {
                $conversionUser->invitation_status = erLhAbstractModelProactiveChatCampaignConversion::INV_SEEN;
                $conversionUser->con_time = time();
                $conversionUser->saveThis();
            }
        }

        $onlineAttributes = $userInstance->online_attr_system_array;

        if ($userInstance->invitation !== false && isset($userInstance->invitation->design_data_array['show_everytime']) && $userInstance->invitation->design_data_array['show_everytime'] == true) {
            $userInstance->operator_message = '';
            $userInstance->message_seen = 0;
            $userInstance->message_seen_ts = 0;
            $onlineAttributes['qinv'] = 1; // Next time show quite invitation
        } else {
            $userInstance->message_seen = 1;
            $userInstance->message_seen_ts = time();

            if (isset($onlineAttributes['qinv'])) {
                unset($onlineAttributes['qinv']); // Next time show normal invitation
            }
        }

        $userInstance->online_attr_system = json_encode($onlineAttributes);

        $userInstance->conversion_id = 0;
        $userInstance->saveThis();
    }
}

if ($Params['user_parameters_unordered']['hash'] != '') {
    list($chatID, $hash) = explode('_', $Params['user_parameters_unordered']['hash']);
    try {

        $db = ezcDbInstance::get();
        $db->beginTransaction();

        $chat = erLhcoreClassModelChat::fetchAndLock($chatID);

        $explicitClosed = false;

        if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $hash && $Params['user_parameters_unordered']['eclose'] == 'survey' && $chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED) {
            $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED;
            $chat->updateThis(array('update' => array(
                'status_sub'
            )));
            
            $explicitClosed = true;
        }

        if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $hash && $chat->user_status != 1) {

            // User closed chat
            $chat->user_status = erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT;
            $chat->support_informed = 1;
            $chat->user_closed_ts = time();

            if ($chat->user_typing < (time() - 12)) {
                $chat->user_typing = time() - 5;// Show for shorter period these status messages
                $chat->is_user_typing = 1;
                $chat->user_typing_txt = ($chat->nick != 'Visitor' ? $chat->nick : htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'Visitor'), ENT_QUOTES)) .' '.htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'has left the chat!'), ENT_QUOTES);
            }

            // User Closed Chat
            if ($Params['user_parameters_unordered']['eclose'] == 't') {

                if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT || $Params['user_parameters_unordered']['close'] == '1') {
                    erLhcoreClassChat::lockDepartment($chat->dep_id, $db);

                    $informVisitorLeft = false;
                    $surveyRedirect = false;

                    if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED) {
                        $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT;
                        $informVisitorLeft = true;
                    }

                    // It is close widget action with permanent close
                    // In that case we set as it was closed as survey completed
                    if ($Params['user_parameters_unordered']['close'] == '1') {
                        $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED;
                        $surveyRedirect = true;
                    }

                    if ($informVisitorLeft == true) {
                        $msg = new erLhcoreClassModelmsg();
                        $msg->msg = '[level=system-warning exit-visitor]' . ($chat->nick != 'Visitor' ? $chat->nick : htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'Visitor'), ENT_QUOTES)) .' '.htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'has closed the chat explicitly!'), ENT_QUOTES).'[/level] [button_action=send_manual_message]'.htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'invite to chat'), ENT_QUOTES).'[/button_action]';
                        $msg->chat_id = $chat->id;
                        $msg->user_id = -1;
                        $msg->time = time();

                        erLhcoreClassChat::getSession()->save($msg);

                        // Set last message ID
                        if ($chat->last_msg_id < $msg->id) {
                            $chat->last_msg_id = $msg->id;
                        }
                    }

                    $chat->removePendingEvents();
                }

                if ($chat->wait_time == 0) {
                    if ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                        $chat->pnd_time = time();
                        $chat->wait_time = 1;
                    } else {
                        $chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
                    }
                }

                $explicitClosed = true;
            }

            if (($onlineuser = $chat->online_user) !== false) {
                $onlineuser->reopen_chat = 0;
                $onlineuser->saveThis();
            }

            $chat->updateThis(array('update' => array(
                'wait_time',
                'pnd_time',
                'last_msg_id',
                'status_sub',
                'user_status',
                'support_informed',
                'user_closed_ts',
                'user_typing',
                'is_user_typing',
                'user_typing_txt',
            )));


            if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat', array('chat' => & $chat));
            }

            if ($explicitClosed == true) {

                if ($chat->user_id > 0) {
                    erLhcoreClassChat::updateActiveChats($chat->user_id);
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.explicitly_closed', array('chat' => & $chat, 'msg' => (isset($msg) ? $msg : null)));
            } else {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.visitor_regular_closed', array('chat' => & $chat));
            }


        } elseif ($chat instanceof erLhcoreClassModelChat && $chat->hash == $hash && $Params['user_parameters_unordered']['eclose'] == 't' && $chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) {

            erLhcoreClassChat::lockDepartment($chat->dep_id, $db);

            if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED) {

                // From now chat will be closed explicitly
                $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT;

                $surveyRedirect = false;
                if ($Params['user_parameters_unordered']['close'] == '1') {
                    $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED;
                    $surveyRedirect = true;
                }

                $msg = new erLhcoreClassModelmsg();
                $msg->msg = '[level=system-warning exit-visitor]'.($chat->nick != 'Visitor' ? $chat->nick : htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'Visitor'), ENT_QUOTES)) .' '.htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'has closed the chat explicitly!'), ENT_QUOTES).'[/level] [button_action=send_manual_message]' . htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat', 'invite to chat'), ENT_QUOTES) . '[/button_action]';
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;
                $msg->time = time();

                erLhcoreClassChat::getSession()->save($msg);

                // Set last message ID
                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                $chat->updateThis(array('update' => array(
                    'status_sub',
                    'last_msg_id'
                )));
            }

            if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat', array('chat' => & $chat));
            }

            if ($chat->user_id > 0) {
                erLhcoreClassChat::updateActiveChats($chat->user_id);
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.explicitly_closed', array('chat' => & $chat, 'msg' => (isset($msg) ? $msg : null)));
        }

        $db->commit();

    } catch (Exception $e) {
        $db->rollback();
        // Do nothing
    }
}

exit;

?>