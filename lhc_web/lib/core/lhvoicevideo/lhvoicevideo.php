<?php

/**
 * Voice & Video & ScreenShare helper.
 *
 * Supported media providers
 *  - agora   - Agora.io cloud (legacy default)
 *  - livekit - LiveKit open source (Apache-2.0) self hosted SFU https://livekit.io
 */
class erLhcoreClassVoiceVideo {

    const PROVIDER_AGORA = 'agora';
    const PROVIDER_LIVEKIT = 'livekit';

    private static $settings = null;

    public static function getSettings()
    {
        if (self::$settings === null) {
            $data = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data;
            self::$settings = array_merge(array(
                'provider' => self::PROVIDER_AGORA,
                'voice' => false,
                'video' => false,
                'screenshare' => false,
                'agora_app_id' => '',
                'agora_app_token' => '',
                'livekit_url' => '',
                'livekit_api_key' => '',
                'livekit_api_secret' => '',
                'token_ttl' => 0,
                'log_calls' => true,
            ), $data);

            if (self::$settings['provider'] == '') {
                self::$settings['provider'] = self::PROVIDER_AGORA;
            }
        }

        return self::$settings;
    }

    public static function resetSettings()
    {
        self::$settings = null;
    }

    public static function isEnabled()
    {
        $settings = self::getSettings();
        return isset($settings['voice']) && $settings['voice'] == true;
    }

    public static function getProvider()
    {
        $settings = self::getSettings();
        return $settings['provider'] == self::PROVIDER_LIVEKIT ? self::PROVIDER_LIVEKIT : self::PROVIDER_AGORA;
    }

    public static function getTokenTTL()
    {
        $settings = self::getSettings();

        if (is_numeric($settings['token_ttl']) && (int)$settings['token_ttl'] >= 60) {
            return (int)$settings['token_ttl'];
        }

        // LiveKit refreshes tokens of connected participants itself, token is used only for the initial connect.
        return self::getProvider() == self::PROVIDER_LIVEKIT ? 600 : 300;
    }

    /**
     * Room (channel) name for a chat.
     * For LiveKit room name is not guessable even if visitor hash is known, access still requires a signed token.
     */
    public static function getRoomName(erLhcoreClassModelChat $chat)
    {
        if (self::getProvider() == self::PROVIDER_LIVEKIT) {
            $settings = self::getSettings();
            return 'lhc_' . $chat->id . '_' . substr(hash_hmac('sha256', $chat->id . '_' . $chat->hash, (string)$settings['livekit_api_secret']), 0, 16);
        }

        return $chat->id . '_' . $chat->hash;
    }

    public static function getVisitorIdentity(erLhcoreClassModelChat $chat)
    {
        return 'visitor_' . $chat->id;
    }

    public static function getOperatorIdentity($userId)
    {
        return 'operator_' . (int)$userId;
    }

    /**
     * Builds access token for a participant.
     */
    public static function buildToken(erLhcoreClassModelChat $chat, $identity, $name = '')
    {
        $settings = self::getSettings();
        $expireTs = time() + self::getTokenTTL();

        if (self::getProvider() == self::PROVIDER_LIVEKIT) {
            if ($settings['livekit_api_key'] == '' || $settings['livekit_api_secret'] == '') {
                return '';
            }

            return self::buildLiveKitToken($settings['livekit_api_key'], $settings['livekit_api_secret'], self::getRoomName($chat), $identity, $name, $expireTs, array(
                'voice' => $settings['voice'] == true,
                'video' => $settings['video'] == true,
                'screenshare' => $settings['screenshare'] == true,
            ));
        }

        if ($settings['agora_app_token'] == '') {
            // Agora project without App Certificate works without tokens
            return '';
        }

        include_once 'lib/core/lhvoicevideo/RtcTokenBuilder.php';
        return AgoraIO\RtcTokenBuilder::buildTokenWithUserAccount($settings['agora_app_id'], $settings['agora_app_token'], self::getRoomName($chat), null, AgoraIO\RtcTokenBuilder::RoleAttendee, $expireTs);
    }

    /**
     * LiveKit access token (JWT HS256)
     * https://docs.livekit.io/home/get-started/authentication/
     */
    public static function buildLiveKitToken($apiKey, $apiSecret, $room, $identity, $name, $expireTs, $options = array())
    {
        $sources = array();

        if (!isset($options['voice']) || $options['voice'] == true) {
            $sources[] = 'microphone';
        }

        if (isset($options['video']) && $options['video'] == true) {
            $sources[] = 'camera';
        }

        if (isset($options['screenshare']) && $options['screenshare'] == true) {
            $sources[] = 'screen_share';
            $sources[] = 'screen_share_audio';
        }

        $now = time();

        $payload = array(
            'iss' => $apiKey,
            'sub' => $identity,
            'name' => $name,
            'nbf' => $now - 10,
            'iat' => $now,
            'exp' => $expireTs,
            'jti' => $identity . '_' . bin2hex(random_bytes(8)),
            'video' => array(
                'room' => $room,
                'roomJoin' => true,
                'canPublish' => true,
                'canSubscribe' => true,
                'canPublishData' => true,
                'canPublishSources' => $sources,
            ),
        );

        $segments = array(
            self::base64UrlEncode(json_encode(array('alg' => 'HS256', 'typ' => 'JWT'))),
            self::base64UrlEncode(json_encode($payload)),
        );

        $segments[] = self::base64UrlEncode(hash_hmac('sha256', implode('.', $segments), $apiSecret, true));

        return implode('.', $segments);
    }

    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Parameters passed to the call javascript application
     */
    public static function getClientParams(erLhcoreClassModelChat $chat, $isVisitor)
    {
        $settings = self::getSettings();

        $params = array(
            'id' => $chat->id,
            'hash' => $chat->hash,
            'isVisitor' => $isVisitor,
            'provider' => self::getProvider(),
            'room' => self::getRoomName($chat),
            'appid' => self::getProvider() == self::PROVIDER_AGORA ? $settings['agora_app_id'] : '',
            'url' => self::getProvider() == self::PROVIDER_LIVEKIT ? $settings['livekit_url'] : '',
            'options' => array(
                'video' => $settings['video'] == true,
                'screenshare' => $settings['screenshare'] == true,
            )
        );

        if ($isVisitor === false) {
            $params['csrf'] = erLhcoreClassUser::instance()->getCSFRToken();
        }

        return $params;
    }

    /**
     * Call state returned to the javascript application. Token is never read from the shared call record,
     * it's issued per participant only when participant is allowed to be in the room.
     */
    public static function getCallState(erLhcoreClassModelChatVoiceVideo $vvcall, $token = '')
    {
        $state = $vvcall->getState();
        $state['token'] = $token;
        return $state;
    }

    public static function canVisitorJoin(erLhcoreClassModelChatVoiceVideo $vvcall)
    {
        return $vvcall->status == erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRMED &&
            $vvcall->vi_status == erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED;
    }

    public static function getVisitorToken(erLhcoreClassModelChat $chat, erLhcoreClassModelChatVoiceVideo $vvcall)
    {
        if (!self::canVisitorJoin($vvcall)) {
            return '';
        }

        return self::buildToken($chat, self::getVisitorIdentity($chat), (string)$chat->nick);
    }

    public static function getOperatorToken(erLhcoreClassModelChat $chat, erLhcoreClassModelChatVoiceVideo $vvcall, $userId, $name = '')
    {
        if ($vvcall->op_status != erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED) {
            return '';
        }

        return self::buildToken($chat, self::getOperatorIdentity($userId), (string)$name);
    }

    /*
     * Call history
     * All history calls are safe - if database is not yet updated calls continue to work.
     */

    public static function getActiveSession($chatId)
    {
        return erLhcoreClassModelChatVoiceVideoSession::findOne(array(
            'filter' => array('chat_id' => $chatId),
            'filterin' => array('status' => array(
                erLhcoreClassModelChatVoiceVideoSession::STATUS_RINGING,
                erLhcoreClassModelChatVoiceVideoSession::STATUS_ACTIVE
            )),
            'sort' => 'id DESC'
        ));
    }

    public static function trackCallStart(erLhcoreClassModelChat $chat, erLhcoreClassModelChatVoiceVideo $vvcall, $userId = 0)
    {
        try {
            $session = self::getActiveSession($chat->id);

            if (!($session instanceof erLhcoreClassModelChatVoiceVideoSession)) {
                $session = new erLhcoreClassModelChatVoiceVideoSession();
                $session->chat_id = $chat->id;
                $session->dep_id = (int)$chat->dep_id;
                $session->provider = self::getProvider();
                $session->ctime = time();
                $session->initiator = $userId > 0 ? erLhcoreClassModelChatVoiceVideoSession::INITIATOR_OPERATOR : erLhcoreClassModelChatVoiceVideoSession::INITIATOR_VISITOR;
            }

            if ($userId > 0) {
                $session->user_id = (int)$userId;
            }

            $session->voice = max((int)$session->voice, (int)$vvcall->voice);
            $session->video = max((int)$session->video, (int)$vvcall->video);
            $session->saveThis();

            return $session;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function trackCallAnswered(erLhcoreClassModelChat $chat, erLhcoreClassModelChatVoiceVideo $vvcall, $userId = 0)
    {
        try {
            $session = self::trackCallStart($chat, $vvcall, $userId);
            if ($session instanceof erLhcoreClassModelChatVoiceVideoSession && $session->answered_at == 0) {
                $session->answered_at = time();
                $session->status = erLhcoreClassModelChatVoiceVideoSession::STATUS_ACTIVE;
                $session->updateThis(array('update' => array('answered_at', 'status')));
            }
        } catch (Exception $e) {

        }
    }

    public static function trackCallEnd(erLhcoreClassModelChat $chat, $reason, $logMessage = true)
    {
        try {
            $session = self::getActiveSession($chat->id);

            if (!($session instanceof erLhcoreClassModelChatVoiceVideoSession)) {
                return;
            }

            $session->ended_at = time();
            $session->end_reason = $reason;

            if ($session->answered_at > 0) {
                $session->duration = max(0, $session->ended_at - $session->answered_at);
                $session->status = erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED;
            } else {
                $session->status = erLhcoreClassModelChatVoiceVideoSession::STATUS_MISSED;
            }

            $session->updateThis(array('update' => array('ended_at', 'end_reason', 'duration', 'status')));

            $settings = self::getSettings();

            if ($logMessage === true && $settings['log_calls'] == true) {
                self::logCallMessage($chat, $session);
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('voicevideo.call_ended', array('chat' => & $chat, 'session' => & $session));

        } catch (Exception $e) {

        }
    }

    public static function trackCallEndByChatId($chatId, $reason)
    {
        try {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('UPDATE `lh_chat_voice_video_session` SET `ended_at` = :ended_at, `end_reason` = :end_reason, `status` = IF(`answered_at` > 0, :status_ended, :status_missed), `duration` = IF(`answered_at` > 0, :ended_at_duration - `answered_at`, 0) WHERE `chat_id` = :chat_id AND `status` IN (' . erLhcoreClassModelChatVoiceVideoSession::STATUS_RINGING . ',' . erLhcoreClassModelChatVoiceVideoSession::STATUS_ACTIVE . ')');
            $stmt->bindValue(':ended_at', time(), PDO::PARAM_INT);
            $stmt->bindValue(':ended_at_duration', time(), PDO::PARAM_INT);
            $stmt->bindValue(':end_reason', $reason, PDO::PARAM_STR);
            $stmt->bindValue(':status_ended', erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED, PDO::PARAM_INT);
            $stmt->bindValue(':status_missed', erLhcoreClassModelChatVoiceVideoSession::STATUS_MISSED, PDO::PARAM_INT);
            $stmt->bindValue(':chat_id', $chatId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {

        }
    }

    public static function logCallMessage(erLhcoreClassModelChat $chat, erLhcoreClassModelChatVoiceVideoSession $session)
    {
        $trans = erTranslationClassLhTranslation::getInstance();

        if ($session->status == erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED) {
            $text = ($session->video == 1 ? $trans->getTranslation('chat/voice_video', 'Video call ended') : $trans->getTranslation('chat/voice_video', 'Voice call ended')) . '. ' . $trans->getTranslation('chat/voice_video', 'Duration') . ': ' . $session->duration_front;
        } else {
            $text = $trans->getTranslation('chat/voice_video', 'Call was not answered');
        }

        $msg = new erLhcoreClassModelmsg();
        $msg->msg = $text;
        $msg->chat_id = $chat->id;
        $msg->user_id = -1;
        $msg->time = time();
        $msg->saveThis();

        $chat->last_msg_id = $chat->last_msg_id < $msg->id ? $msg->id : $chat->last_msg_id;
        $chat->updateThis(array('update' => array('last_msg_id')));
    }

    public static function formatDuration($seconds)
    {
        $seconds = (int)$seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return ($hours > 0 ? $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) : $minutes) . ':' . str_pad($secs, 2, '0', STR_PAD_LEFT);
    }
}

?>
