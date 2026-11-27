<?php

$tpl = erLhcoreClassTemplate::getInstance('lhjitsi/join.tpl.php');

$config = erLhcoreClassJitsi::getConfig();

if (!erLhcoreClassJitsi::isEnabled($config)) {
    $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Jitsi calling is not enabled.'));
} elseif (!is_numeric($Params['user_parameters']['chat_id']) || !is_numeric($Params['user_parameters']['msg_id'])) {
    $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Invalid call parameters.'));
} else {
    $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['chat_id']);
    $message = erLhcoreClassModelmsg::fetch((int)$Params['user_parameters']['msg_id']);

    if (!($chat instanceof erLhcoreClassModelChat) || !erLhcoreClassChat::hasAccessToRead($chat)) {
        $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'You do not have permission to join this call.'));
    } elseif ((int)$chat->status === (int)erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
        $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Cannot join a call in a closed chat.'));
    } elseif (!($message instanceof erLhcoreClassModelmsg) || (int)$message->chat_id !== (int)$chat->id || !isset($message->meta_msg_array['content']['jitsi'])) {
        $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'Call session was not found.'));
    } else {
        $jitsiMeta = $message->meta_msg_array['content']['jitsi'];
        $status = $jitsiMeta['status'] ?? '';

        if (in_array($status, [erLhcoreClassJitsi::STATUS_DENIED, erLhcoreClassJitsi::STATUS_CANCELED, erLhcoreClassJitsi::STATUS_ENDED], true)) {
            $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/admin', 'This call has already ended.'));
            $tpl->set('call_status', $status);
        } else {
            $userData = $currentUser->getUserData();
            $room = (string)$jitsiMeta['room'];
            $mode = erLhcoreClassJitsi::normalizeMode((string)($jitsiMeta['mode'] ?? ''), $config);
            $clientConfig = erLhcoreClassJitsi::buildClientConfig($config, $mode, true);

            try {
                $operatorEmail = erLhcoreClassJitsi::shouldHideProfilePic($config, true) ? '' : (string)$userData->email;
                $jwt = erLhcoreClassJitsi::generateJwt($config, $room, [
                    'id' => 'op_' . $userData->id,
                    'name' => !empty($jitsiMeta['operator_name']) ? $jitsiMeta['operator_name'] : $userData->name_support,
                    'email' => $operatorEmail,
                ], true);

                $tpl->set('jitsi_ready', true);
                $tpl->set('waiting_for_operator', false);
                $tpl->set('prewarm_audio', false);
                $tpl->set('prewarm_video', false);
                $tpl->set('is_embedded', false);
                $tpl->set('is_minimal', false);
                $tpl->set('is_operator', true);
                $tpl->set('chat', $chat);
                $tpl->set('msg', $message);
                $tpl->set('jitsi_meta', $jitsiMeta);
                $tpl->set('domain', erLhcoreClassJitsi::getEffectiveDomain($config));
                $tpl->set('external_api_url', erLhcoreClassJitsi::getExternalApiUrl($config));
                $tpl->set('direct_room_url', erLhcoreClassJitsi::getDirectRoomUrl($room, $config));
                $tpl->set('full_room_name', erLhcoreClassJitsi::getFullRoomName($room, $config));
                $tpl->set('jwt', $jwt);
                $tpl->set('display_name', !empty($jitsiMeta['operator_name']) ? $jitsiMeta['operator_name'] : $userData->name_support);
                $tpl->set('user_email', $operatorEmail);
                $tpl->set('client_config', $clientConfig);
                $tpl->set('status_url_base', erLhcoreClassDesign::baseurl('jitsi/statusop') . '/' . $chat->id . '/' . $message->id);
                $tpl->set('csrf_token', $currentUser->getCSFRToken());
            } catch (Exception $e) {
                $tpl->set('error_message', htmlspecialchars($e->getMessage(), ENT_QUOTES));
            }
        }
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'jitsi';
