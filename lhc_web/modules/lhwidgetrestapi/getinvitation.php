<?php

erLhcoreClassRestAPIHandler::setHeaders();

$payload = json_decode(file_get_contents('php://input'),true);

if (isset($payload['vid_id']) && is_numeric($payload['vid_id'])) {
    $onlineUser = erLhcoreClassModelChatOnlineUser::fetch($payload['vid_id']);
} else {
    $onlineUser = erLhcoreClassModelChatOnlineUser::fetchByVid($payload['vid']);
}

if (!($onlineUser instanceof erLhcoreClassModelChatOnlineUser) || $onlineUser->vid != $payload['vid']) {
    erLhcoreClassRestAPIHandler::outputResponse(array('status' => false));
    exit;
}

if (is_numeric($payload['invitation']) && $payload['invitation'] > 0/*&& ($onlineUser->invitation_id == 0 || $onlineUser->invitation_id != $payload['invitation'])*/) {
    erLhAbstractModelProactiveChatInvitation::setInvitation($onlineUser, (int)$payload['invitation']);
}

// Make conversion if any exists
if ($onlineUser->conversion_id > 0) {
    $conversionUser = erLhAbstractModelProactiveChatCampaignConversion::fetch($onlineUser->conversion_id);
    if ($conversionUser instanceof erLhAbstractModelProactiveChatCampaignConversion && $conversionUser->invitation_status != erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN) {
        $conversionUser->invitation_status = erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN;
        $conversionUser->con_time = time();
        $conversionUser->saveThis();
    }
}

$outputResponse = array('status' => true);

if (($user = $onlineUser->operator_user) !== false) {

    $outputResponse['name_support'] = $user->name_support;
    $outputResponse['extra_profile'] = $user->job_title != '' ? htmlspecialchars($user->job_title) : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Personal assistant');

    if (isset($onlineUser->online_attr_system_array['lhc_full_widget'])) {
        $outputResponse['full_widget'] = 1;
    }

    if ($user->has_photo) {
        $outputResponse['photo'] = $user->photo_path;
        $outputResponse['photo_title'] = $user->name_support;
    }

} else {
    $outputResponse['name_support'] = $onlineUser->operator_user !== false ? htmlspecialchars($onlineUser->operator_user->name_support) : (!empty($onlineUser->operator_user_proactive) ? htmlspecialchars($onlineUser->operator_user_proactive) : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support'));
}

$outputResponse['invitation_id'] = $onlineUser->invitation_id;

$outputResponse['qinv'] = isset($onlineUser->online_attr_system_array['qinv']);

$outputResponse['message'] = erLhcoreClassBBCode::make_clickable(htmlspecialchars($onlineUser->operator_message_front));

$outputResponse['play_sound'] = erLhcoreClassModelChatConfig::fetch('sound_invitation')->current_value == 1;

$outputResponse['bubble'] = false;

if (isset($payload['theme']) && $payload['theme'] > 0) {
    $theme = erLhAbstractModelWidgetTheme::fetch($payload['theme']);

    if ($theme instanceof erLhAbstractModelWidgetTheme)
    {
        $theme->translate();

        if (isset($theme->bot_configuration_array['bubble_style_profile']) && $theme->bot_configuration_array['bubble_style_profile'] == 1) {
            $outputResponse['bubble'] = true;
        }

        if (!isset($outputResponse['photo']) && $theme->operator_image_url !== false) {
            $outputResponse['photo'] = $theme->operator_image_url;
        }

        if ($theme->intro_operator_text != '') {
            $outputResponse['extra_profile'] = $theme->intro_operator_text;
        }
    }
}

// Bot message as full widget body
if ($outputResponse['invitation_id'] > 0) {

    $invitation = erLhAbstractModelProactiveChatInvitation::fetch($outputResponse['invitation_id']);

    if ($invitation instanceof erLhAbstractModelProactiveChatInvitation && isset($invitation->design_data_array['append_bot']) && $invitation->design_data_array['append_bot'] == 1 && $invitation->bot_id > 0 && $invitation->trigger_id > 0) {

        $bot = erLhcoreClassModelGenericBotBot::fetch($invitation->bot_id);

        if ($bot instanceof erLhcoreClassModelGenericBotBot)
        {
            if ($bot->has_photo) {
                $outputResponse['photo'] = $bot->photo_path;
            }

            $outputResponse['name_support'] = $bot->nick;
        }

        $tpl = new erLhcoreClassTemplate('lhchat/part/render_intro.tpl.php');

        if (isset($theme)) {
            $tpl->set('theme',$theme);
        }

        $chat = new erLhcoreClassModelChat();
        $chat->bot = $bot;
        $chat->gbot_id = $bot->id;

        $tpl->set('chat',$chat);
        $tpl->set('react',true);
        $tpl->set('no_wrap_intro',true);
        $tpl->set('no_br',true);
        $tpl->set('triggerMessageId',$invitation->trigger_id);

        $outputResponse['message_full'] = $tpl->fetch();

        if (isset($invitation->design_data_array['append_intro_bot']) && $invitation->design_data_array['append_intro_bot'] == 1) {
            $outputResponse['bot_intro'] = true;
        }
    }

    if ($invitation instanceof erLhAbstractModelProactiveChatInvitation) {

        if (isset($invitation->design_data_array['close_above_msg']) && $invitation->design_data_array['close_above_msg'] == 1) {
            $outputResponse['close_above_msg'] = true;
        }

        if (isset($invitation->design_data_array['full_on_invitation']) && $invitation->design_data_array['full_on_invitation'] == true) {
            $outputResponse['full_widget'] = true;
        }

        if (isset($invitation->design_data_array['photo_left_column']) && $invitation->design_data_array['photo_left_column'] == true) {
            $outputResponse['photo_left_column'] = true;
        }
        
        if (isset($invitation->design_data_array['hide_op_name']) && $invitation->design_data_array['hide_op_name'] == true) {
            $outputResponse['hide_op_name'] = true;
        }
        
        if (isset($invitation->design_data_array['message_width']) && is_numeric($invitation->design_data_array['message_width']) && $invitation->design_data_array['message_width'] > 0) {
            $outputResponse['message_width'] = (int)$invitation->design_data_array['message_width'];
        }
    }
}

if (strpos($outputResponse['message'],'{operator}') !== false) {
    $outputResponse['message'] = str_replace('{operator}',$outputResponse['name_support'], $outputResponse['message']);

    // Update operator message so once chat is started it will have correct message.
    $onlineUser->operator_message = str_replace('{operator}', $outputResponse['name_support'], $onlineUser->operator_message);
    $onlineUser->updateThis(['update' => ['operator_message']]);
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.getinvitation',array('output' => & $outputResponse, 'ou' => $onlineUser));

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit;
?>