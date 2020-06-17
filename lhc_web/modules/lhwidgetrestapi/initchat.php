<?php

erLhcoreClassRestAPIHandler::setHeaders();
$requestPayload = json_decode(file_get_contents('php://input'),true);

try {
    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $chat = erLhcoreClassModelChat::fetchAndLock($requestPayload['id']);

    erLhcoreClassChat::setTimeZoneByChat($chat);

    if ($chat->hash == $requestPayload['hash'])
    {
        // User online
        if ($chat->user_status != 0) {
            $chat->support_informed = 1;
            $chat->user_typing = time();// Show for shorter period these status messages
            $chat->is_user_typing = 1;
            if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){

                $refererSite = $_SERVER['HTTP_REFERER'];

                if ($refererSite != '' && strlen($refererSite) > 50) {
                    if ( function_exists('mb_substr') ) {
                        $refererSite = mb_substr($refererSite, 0, 50);
                    } else {
                        $refererSite = substr($refererSite, 0, 50);
                    }
                }

                $chat->user_typing_txt = $refererSite;
            } else {
                $chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userjoined','Visitor has joined the chat!'),ENT_QUOTES);
            }

            if ($chat->user_status == erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN && ($onlineuser = $chat->online_user) !== false) {
                $onlineuser->reopen_chat = 0;
                $onlineuser->saveThis();
            }

            $chat->unread_op_messages_informed = 0;
            $chat->has_unread_op_messages = 0;
            $chat->unanswered_chat = 0;

            $chat->user_status = erLhcoreClassModelChat::USER_STATUS_JOINED_CHAT;

            $chat->updateThis(array('update' => array(
                'unanswered_chat',
                'user_status',
                'has_unread_op_messages',
                'unread_op_messages_informed',
                'user_typing_txt',
                'is_user_typing',
                'user_typing',
                'support_informed',
            )));
        }

        $db->commit();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chatwidgetchat',array('params' => & $Params, 'chat' => & $chat));

        $outputResponse = array(
            'operator' => 'operator',
            'messages' => [],
            'chat_ui' => array(

            )
        );

        if (isset($requestPayload['theme']) && $requestPayload['theme'] > 0) {
            $theme = erLhAbstractModelWidgetTheme::fetch($requestPayload['theme']);
            if (isset($theme->bot_configuration_array['placeholder_message']) && !empty($theme->bot_configuration_array['placeholder_message'])) {
                $outputResponse['chat_ui']['placeholder_message'] = $theme->bot_configuration_array['placeholder_message'];
            }

            if (isset($theme->bot_configuration_array['hide_status']) && $theme->bot_configuration_array['hide_status'] == true) {
                $outputResponse['chat_ui']['hide_status'] = true;
            }

            if (isset($theme->bot_configuration_array['msg_expand']) && $theme->bot_configuration_array['msg_expand'] == true) {
                $outputResponse['chat_ui']['msg_expand'] = true;
            }

            if ($theme->hide_popup == 1) {
                $outputResponse['chat_ui']['hide_popup'] = true;
            }

            if ($theme->hide_close == 1) {
                $outputResponse['chat_ui']['hide_close'] = true;
            }

            if (isset($theme->bot_configuration_array['survey_button']) && $theme->bot_configuration_array['survey_button'] == true) {
                $outputResponse['chat_ui']['survey_button'] = true;
            }

            if (isset($theme->bot_configuration_array['confirm_close']) && $theme->bot_configuration_array['confirm_close'] == true) {
                $outputResponse['chat_ui']['confirm_close'] = true;
            }

            if (isset($theme->bot_configuration_array['close_on_unload']) && $theme->bot_configuration_array['close_on_unload'] == true) {
                $outputResponse['chat_ui']['close_on_unload'] = true;
            }

            if (isset($theme->bot_configuration_array['switch_to_human']) && is_numeric($theme->bot_configuration_array['switch_to_human'])) {
                $outputResponse['chat_ui']['switch_to_human'] = (int)$theme->bot_configuration_array['switch_to_human'];
            }
        }

        if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW) {
            if ($chat->status_sub_arg != '') {
                $args = json_decode($chat->status_sub_arg, true);
                if (isset($args['survey_id'])) {
                    $outputResponse['chat_ui']['survey_id'] = (int)$args['survey_id'];
                }
            }
        }

        if (!isset($outputResponse['chat_ui']['survey_id']) && isset($chat->department->bot_configuration_array['survey_id']) && $chat->department->bot_configuration_array['survey_id'] > 0) {
            $outputResponse['chat_ui']['survey_id'] = $chat->department->bot_configuration_array['survey_id'];
        };

        $soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data_value;
        $outputResponse['chat_ui']['sync_interval'] = (int)($soundData['chat_message_sinterval']*1000);

        $outputResponse['status_sub'] = $chat->status_sub;
        $outputResponse['status'] = $chat->status;

        if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT || $chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW || $chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) {
            $outputResponse['closed'] = true;
        } else {
            $outputResponse['closed'] = false;
        }

        if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 0) {
            $outputResponse['chat_ui']['print'] = true;
        }

        $notificationsSettings = erLhcoreClassModelChatConfig::fetch('notifications_settings')->data_value;

        if (isset($notificationsSettings['enabled']) && $notificationsSettings['enabled'] == 1 && (!isset($theme) || $theme === false || (isset($theme->notification_configuration_array['notification_enabled']) && $theme->notification_configuration_array['notification_enabled'] == 1))) {
            $outputResponse['chat_ui']['notifications'] = true;
            $outputResponse['chat_ui']['notifications_pk'] = $notificationsSettings['public_key'];
        }

        if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) {
            $outputResponse['chat_ui']['transcript'] = true;
        }

        if ((int)erLhcoreClassModelChatConfig::fetch('hide_button_dropdown')->current_value == 0) {
            $outputResponse['chat_ui']['close_btn'] = true;
        }

        $outputResponse['chat_ui']['max_length'] = (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value - 1;

        $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

        $chatVariables = $chat->chat_variables_array;

        if ((isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) || (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1)) {
            $outputResponse['chat_ui']['file'] = true;
            $outputResponse['chat_ui']['file_options'] = array(
                'fs' => $fileData['fs_max']*1024,
                'ft_us' => $fileData['ft_us'],
            );
        }

        if (isset($fileData['sound_messages']) && $fileData['sound_messages'] == true) {
            $outputResponse['chat_ui']['voice_message'] = $fileData['sound_length'];
        }

        $outputResponse['chat_ui']['fbst'] = $chat->fbst;

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.initchat', array('output' => & $outputResponse, 'chat' => $chat));

        echo erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
        
    } else {
        //$tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
    $db->rollback();
    //$tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}
exit;

?>