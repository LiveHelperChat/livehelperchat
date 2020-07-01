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

    if ($user->has_photo) {
        $outputResponse['photo'] = $user->photo_path;
        $outputResponse['photo_title'] = $user->name_support;
    }

} else {
    $outputResponse['extra_profile'] = $onlineUser->operator_user !== false ? htmlspecialchars($onlineUser->operator_user->name_support) : (!empty($onlineUser->operator_user_proactive) ? htmlspecialchars($onlineUser->operator_user_proactive) : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support'));
}

$outputResponse['invitation_id'] = $onlineUser->invitation_id;

$outputResponse['qinv'] = isset($onlineUser->online_attr_system_array['qinv']);

$outputResponse['message'] = erLhcoreClassBBCode::make_clickable(htmlspecialchars($onlineUser->operator_message_front));

$outputResponse['play_sound'] = erLhcoreClassModelChatConfig::fetch('sound_invitation')->current_value == 1;

$outputResponse['bubble'] = false;

if (isset($payload['theme']) && $payload['theme'] > 0) {
    $theme = erLhAbstractModelWidgetTheme::fetch($payload['theme']);
    if ($theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['bubble_style_profile']) && $theme->bot_configuration_array['bubble_style_profile'] == 1) {
        $outputResponse['bubble'] = true;
    }

    if (!isset($outputResponse['photo']) && $theme instanceof erLhAbstractModelWidgetTheme && $theme->operator_image_url !== false) {
        $outputResponse['photo'] = $theme->operator_image_url;
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

            $outputResponse['extra_profile'] = $bot->nick;
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
    }
}



erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit;
?>