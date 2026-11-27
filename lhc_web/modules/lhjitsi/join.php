<?php

$tpl = erLhcoreClassTemplate::getInstance('lhjitsi/join.tpl.php');

$config = erLhcoreClassJitsi::getConfig();
$isEmbedded = !empty($Params['user_parameters_unordered']['embed']);
$isMinimal = $isEmbedded && !empty($Params['user_parameters_unordered']['minimal']);
$tpl->set('is_embedded', $isEmbedded);
$tpl->set('is_minimal', $isMinimal);

if (!erLhcoreClassJitsi::isEnabled($config)) {
    $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Calling is currently unavailable.'));
} elseif (!is_numeric($Params['user_parameters']['chat_id']) || !is_numeric($Params['user_parameters']['msg_id'])) {
    $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Invalid call link.'));
} else {
    $db = ezcDbInstance::get();
    $db->beginTransaction();
    $transactionOpen = true;

    try {
        $chat = erLhcoreClassModelChat::fetchAndLock((int)$Params['user_parameters']['chat_id']);
        $hash = (string)($Params['user_parameters']['hash'] ?? '');

        if (!erLhcoreClassJitsi::isVisitorChatValid($chat, $hash)) {
            $db->rollback();
            $transactionOpen = false;
            $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'This chat session is no longer active.'));
        } else {
            $message = erLhcoreClassModelmsg::fetch((int)$Params['user_parameters']['msg_id']);

            if (!($message instanceof erLhcoreClassModelmsg) || (int)$message->chat_id !== (int)$chat->id || !isset($message->meta_msg_array['content']['jitsi'])) {
                $db->rollback();
                $transactionOpen = false;
                $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Call invitation was not found.'));
            } else {
                $jitsiMeta = $message->meta_msg_array['content']['jitsi'];
                $status = $jitsiMeta['status'] ?? '';

                if (in_array($status, [erLhcoreClassJitsi::STATUS_DENIED, erLhcoreClassJitsi::STATUS_CANCELED, erLhcoreClassJitsi::STATUS_ENDED], true)) {
                    $db->rollback();
                    $transactionOpen = false;
                    $tpl->set('error_message', erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'This call invitation is no longer active.'));
                    $tpl->set('call_status', $status);
                } else {
                    $callChanged = false;
                    // Atomically mark accepted if visitor opened the join page directly
                    if ($status === erLhcoreClassJitsi::STATUS_INVITED) {
                        $transition = erLhcoreClassJitsi::applyStateTransition($jitsiMeta, 'accept', 'visitor');
                        if ($transition['changed']) {
                            $jitsiMeta = $transition['meta'];
                            erLhcoreClassJitsi::updateCallMessage($chat, $message, $jitsiMeta, true, true);
                            $callChanged = true;
                        }
                    }

                    $db->commit();
                    $transactionOpen = false;

                    if ($callChanged) {
                        erLhcoreClassJitsi::dispatchCallUpdated($chat, $message);
                    }

                    $room = (string)$jitsiMeta['room'];
                    $mode = erLhcoreClassJitsi::normalizeMode((string)($jitsiMeta['mode'] ?? ''), $config);
                    $visitorName = trim((string)$chat->nick) !== ''
                        ? (string)$chat->nick
                        : erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Visitor');

                    $canJoinNow = erLhcoreClassJitsi::canVisitorJoinRoom($jitsiMeta);
                    $visitorRoleOpts = array_merge(
                        erLhcoreClassJitsi::getDefaultRoleOptions(false),
                        isset($config['visitor_options']) && is_array($config['visitor_options']) ? $config['visitor_options'] : []
                    );
                    $isAudioOnly = ($mode === erLhcoreClassJitsi::MODE_AUDIO);
                    $prewarmAudio = empty($visitorRoleOpts['start_with_audio_muted']);
                    $prewarmVideo = !$isAudioOnly && !empty($config['video_enabled']) && empty($visitorRoleOpts['start_with_video_muted']);

                    $visitorEmail = erLhcoreClassJitsi::shouldHideProfilePic($config, false) ? '' : (string)$chat->email;

                    $tpl->set('jitsi_ready', true);
                    $tpl->set('waiting_for_operator', !$canJoinNow);
                    $tpl->set('prewarm_audio', $prewarmAudio);
                    $tpl->set('prewarm_video', $prewarmVideo);
                    $tpl->set('is_operator', false);
                    $tpl->set('chat', $chat);
                    $tpl->set('msg', $message);
                    $tpl->set('jitsi_meta', $jitsiMeta);
                    $tpl->set('display_name', $visitorName);
                    $tpl->set('user_email', $visitorEmail);
                    $tpl->set('status_url_base', erLhcoreClassDesign::baseurl('jitsi/status') . '/' . $chat->id . '/' . $chat->hash . '/' . $message->id);
                    $tpl->set('csrf_token', '');

                    // Only expose room details and JWT once operator has joined first
                    if ($canJoinNow) {
                        $clientConfig = erLhcoreClassJitsi::buildClientConfig($config, $mode, false);
                        $jwt = erLhcoreClassJitsi::generateJwt($config, $room, [
                            'id' => 'vi_' . $chat->id,
                            'name' => $visitorName,
                            'email' => $visitorEmail,
                        ], false);

                        $tpl->set('domain', erLhcoreClassJitsi::getEffectiveDomain($config));
                        $tpl->set('external_api_url', erLhcoreClassJitsi::getExternalApiUrl($config));
                        $tpl->set('direct_room_url', erLhcoreClassJitsi::getDirectRoomUrl($room, $config));
                        $tpl->set('full_room_name', erLhcoreClassJitsi::getFullRoomName($room, $config));
                        $tpl->set('jwt', $jwt);
                        $tpl->set('client_config', $clientConfig);
                    } else {
                        $tpl->set('domain', '');
                        $tpl->set('external_api_url', '');
                        $tpl->set('direct_room_url', '');
                        $tpl->set('full_room_name', '');
                        $tpl->set('jwt', null);
                        $tpl->set('client_config', ['configOverwrite' => [], 'interfaceConfigOverwrite' => []]);
                    }
                }
            }
        }
    } catch (Exception $e) {
        if ($transactionOpen) {
            $db->rollback();
        }
        $tpl->set('error_message', htmlspecialchars($e->getMessage(), ENT_QUOTES));
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'jitsi';
$Result['is_embedded'] = $isEmbedded;
$Result['anonymous'] = true;
