<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (!empty($_GET) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $requestPayload = $_GET;
} else {
    $requestPayload = json_decode(file_get_contents('php://input'),true);
}

try {
    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $chat = erLhcoreClassModelChat::fetchAndLock($requestPayload['id']);

    // Chat does not exists
    if (!($chat instanceof erLhcoreClassModelChat)) {
        echo erLhcoreClassRestAPIHandler::outputResponse(array(
            'operator' => 'operator',
            'messages' => [],
            'closed' => true,
            'status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT,
            'status_sub' => 0,
            'chat_ui' => ['sync_interval' => 2500]
        ));
        exit;
    }

    erLhcoreClassChat::setTimeZoneByChat($chat);

    if ($chat->hash == $requestPayload['hash'])
    {
        // User online
        if ($chat->user_status != 0) {
            $chat->support_informed = 1;
            $chat->user_typing = time();// Show for shorter period these status messages
            $chat->is_user_typing = 1;
            if (($refererSite = erLhcoreClassModelChatOnlineUser::getReferer()) != '') {
                if (strlen($refererSite) > 50) {
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

        $data = erLhcoreClassModelChatConfig::fetch('mobile_options')->data_value;

        if (isset($data['notifications']) && $data['notifications'] == true) {
            $outputResponse['chat_ui']['mn'] = 1;
        }

        if ((int)erLhcoreClassModelChatConfig::fetch('bbc_button_visible')->value != 1) {
            $outputResponse['chat_ui']['bbc_btnh'] = true;
        }

        $outputResponse['chat_ui']['header_buttons'] = array(
            array(
                'pos' => 'left',
                'btn' => 'min'
            ),
            array(
                'pos' => 'right',
                'btn' => 'close',
            ),
            array(
                'pos' => 'right',
                'btn' => 'popup'
            )
        );

        if (isset($requestPayload['theme']) && $requestPayload['theme'] > 0) {

            $theme = erLhAbstractModelWidgetTheme::fetch($requestPayload['theme']);

            if ($theme instanceof erLhAbstractModelWidgetTheme) {

                $theme->translate();

                foreach (array('placeholder_message','cnew_msgh','cnew_msg','cscroll_btn','cnew_msgm','min_text','popup_text','end_chat_text') as $attrTranslate) {
                    if (isset($theme->bot_configuration_array[$attrTranslate]) && !empty($theme->bot_configuration_array[$attrTranslate])) {
                        $outputResponse['chat_ui'][$attrTranslate] = $theme->bot_configuration_array[$attrTranslate];
                    }
                }

                if (isset($theme->bot_configuration_array['hide_status']) && $theme->bot_configuration_array['hide_status'] == true) {
                    $outputResponse['chat_ui']['hide_status'] = true;
                }

                if (isset($theme->bot_configuration_array['embed_closed']) && !empty($theme->bot_configuration_array['embed_closed'])) {
                    $outputResponse['chat_ui']['embed_cls'] = (int)$theme->bot_configuration_array['embed_closed'];
                }

                if (isset($theme->bot_configuration_array['msg_expand']) && $theme->bot_configuration_array['msg_expand'] == true) {
                    $outputResponse['chat_ui']['msg_expand'] = true;
                }

                if (isset($theme->bot_configuration_array['font_size']) && $theme->bot_configuration_array['font_size'] == true) {
                    $outputResponse['chat_ui']['font_size'] = true;
                }

                // Theme configuration overrides default settings
                if (isset($theme->bot_configuration_array['hide_bb_code']) && $theme->bot_configuration_array['hide_bb_code'] == true) {
                    $outputResponse['chat_ui']['bbc_btnh'] = true;
                } elseif (isset($outputResponse['chat_ui']['bbc_btnh'])) {
                    unset($outputResponse['chat_ui']['bbc_btnh']);
                }

                if ($theme->hide_popup == 1) {
                    $outputResponse['chat_ui']['hide_popup'] = true;
                }

                if ($theme->hide_close == 1) {
                    $outputResponse['chat_ui']['hide_close'] = true;
                }

                if ($theme->popup_image_url != '') {
                    $outputResponse['chat_ui']['img_icon_popup'] = $theme->popup_image_url;
                }

                if ($theme->close_image_url != '') {
                    $outputResponse['chat_ui']['img_icon_close'] = $theme->close_image_url;
                }

                if ($theme->minimize_image_url != '') {
                    $outputResponse['chat_ui']['img_icon_min'] = $theme->minimize_image_url;
                }

                if (isset($theme->bot_configuration_array['survey_button']) && $theme->bot_configuration_array['survey_button'] == true) {
                    $outputResponse['chat_ui']['survey_button'] = true;
                }

                if (isset($theme->bot_configuration_array['start_on_close']) && $theme->bot_configuration_array['start_on_close'] == true) {
                    $outputResponse['chat_ui']['start_on_close'] = true;
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

                if (isset($theme->bot_configuration_array['close_in_status']) && $theme->bot_configuration_array['close_in_status'] == true) {
                    $outputResponse['chat_ui']['clinst'] = true;
                }

                if (isset($theme->bot_configuration_array['msg_snippet']) && $theme->bot_configuration_array['msg_snippet'] == true) {
                    $outputResponse['chat_ui']['msg_snippet'] = true;
                }
                
                if (isset($theme->bot_configuration_array['custom_html_header']) && $theme->bot_configuration_array['custom_html_header'] != '') {
                    $outputResponse['chat_ui']['custom_html_header'] = $theme->bot_configuration_array['custom_html_header'];
                }

                if (isset($theme->bot_configuration_array['custom_html_header_body']) && $theme->bot_configuration_array['custom_html_header_body'] != '') {
                    $outputResponse['chat_ui']['custom_html_header_body'] = $theme->bot_configuration_array['custom_html_header_body'];
                }

                if (isset($theme->bot_configuration_array['prev_msg']) && $theme->bot_configuration_array['prev_msg'] == true) {
                    if ($chat->online_user instanceof erLhcoreClassModelChatOnlineUser) {

                        $previousChat = erLhcoreClassModelChat::findOne(array('sort' => 'id DESC', 'limit' => 1, 'filternot' => array('id' => $chat->id), 'filter' => array('online_user_id' => $chat->online_user->id)));

                        if ($previousChat instanceof erLhcoreClassModelChat){
                            $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/previous_chat.tpl.php');
                            $tpl->set('messages', erLhcoreClassChat::getPendingMessages((int)$previousChat->id,  0, true));
                            $tpl->set('chat',$previousChat);
                            $tpl->set('sync_mode','');
                            $tpl->set('async_call',true);
                            $tpl->set('theme',$theme);
                            $tpl->set('react',true);
                            $outputResponse['chat_ui']['prev_chat'] = $tpl->fetch();
                        }
                    }
                }

                if (isset($theme->bot_configuration_array['icons_order']) && $theme->bot_configuration_array['icons_order'] != '') {
                    $icons = explode(',',str_replace(' ','',$theme->bot_configuration_array['icons_order']));
                    $outputResponse['chat_ui']['header_buttons'] = array();
                    foreach ($icons as $icon) {
                        $paramsIcon = explode('_',$icon);
                        $outputResponse['chat_ui']['header_buttons'][] = array(
                            'pos' => $paramsIcon[0],
                            'btn' => $paramsIcon[1],
                            'print' => isset($paramsIcon[2]) && $paramsIcon[2] == 'print',
                        );
                    }
                }
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

        if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) {
            $outputResponse['chat_ui']['mail'] = true;
        }

        $outputResponse['status_sub'] = $chat->status_sub;
        $outputResponse['status'] = $chat->status;

        if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT || $chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM || $chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW || $chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) {
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

        $outputResponse['chat_ui']['open_timeout'] = (int)erLhcoreClassModelChatConfig::fetch('open_closed_chat_timeout')->current_value;

        $outputResponse['chat_ui']['max_length'] = (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value - 1;

        $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

        $chatVariables = $chat->chat_variables_array;

        if ((isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) || (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1)) {
            $outputResponse['chat_ui']['file'] = true;
            $outputResponse['chat_ui']['file_options'] = array(
                'fs' => $fileData['fs_max']*1024,
                'ft_us' => $fileData['ft_us'],
            );
            
            if (isset($fileData['one_file_upload']) && $fileData['one_file_upload'] == true) {
                $outputResponse['chat_ui']['file_options']['one_file_upload'] = true;
            }
        }

        if (isset($chatVariables['lhc_ds'])) {
            if ((int)$chatVariables['lhc_ds'] == 0) {
                if (isset($outputResponse['chat_ui']['survey_id'])) {
                    unset($outputResponse['chat_ui']['survey_id']);
                }
            } else {
                $outputResponse['chat_ui']['survey_id'] = (int)$chatVariables['lhc_ds'];
            }
        }

        if (isset($fileData['sound_messages']) && $fileData['sound_messages'] == true) {
            $outputResponse['chat_ui']['voice_message'] = $fileData['sound_length'];
        }

        $voiceData = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data;

        if (isset($voiceData['voice']) && $voiceData['voice'] == true) {
            $outputResponse['chat_ui']['voice'] = true;
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