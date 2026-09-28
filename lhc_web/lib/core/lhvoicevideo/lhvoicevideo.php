<?php

/**
 * Voice & Video & ScreenShare helper.
 *
 * Media is handled by self hosted LiveKit open source (Apache-2.0) SFU https://livekit.io
 * No third party cloud service is involved.
 */
class erLhcoreClassVoiceVideo {

    const PROVIDER_LIVEKIT = 'livekit';

    private static $settings = null;

    public static function getSettings()
    {
        if (self::$settings === null) {
            $data = (array)erLhcoreClassModelChatConfig::fetch('vvsh_configuration')->data;
            self::$settings = array_merge(array(
                'voice' => false,
                'video' => false,
                'screenshare' => false,
                'livekit_url' => '',
                'livekit_api_key' => '',
                'livekit_api_secret' => '',
                'token_ttl' => 0,
                'ring_timeout' => 60,
                'log_calls' => true,
            ), $data);

            // Settings of removed cloud provider are not used anymore
            unset(self::$settings['agora_app_id'], self::$settings['agora_app_token']);
            self::$settings['provider'] = self::PROVIDER_LIVEKIT;
        }

        return self::$settings;
    }

    public static function resetSettings()
    {
        self::$settings = null;
    }

    /**
     * Calls are available only if enabled and media server is configured.
     */
    public static function isEnabled()
    {
        $settings = self::getSettings();
        return $settings['voice'] == true && self::isConfigured();
    }

    public static function isConfigured()
    {
        $settings = self::getSettings();
        return $settings['livekit_url'] != '' && $settings['livekit_api_key'] != '' && $settings['livekit_api_secret'] != '';
    }

    public static function getProvider()
    {
        return self::PROVIDER_LIVEKIT;
    }

    public static function getTokenTTL()
    {
        $settings = self::getSettings();

        if (is_numeric($settings['token_ttl']) && (int)$settings['token_ttl'] >= 60) {
            return (int)$settings['token_ttl'];
        }

        // LiveKit refreshes tokens of connected participants itself, token is used only for the initial connect.
        return 600;
    }

    /**
     * Room name for a chat. Not guessable even if visitor hash is known, access still requires a signed token.
     */
    public static function getRoomName(erLhcoreClassModelChat $chat)
    {
        $settings = self::getSettings();
        return 'lhc_' . $chat->id . '_' . substr(hash_hmac('sha256', $chat->id . '_' . $chat->hash, (string)$settings['livekit_api_secret']), 0, 16);
    }

    public static function getVisitorIdentity(erLhcoreClassModelChat $chat)
    {
        return 'visitor_' . $chat->id;
    }

    public static function getOperatorIdentity($userId)
    {
        return 'operator_' . (int)$userId;
    }

    public static function getSupervisorIdentity($userId)
    {
        return 'supervisor_' . (int)$userId;
    }

    /**
     * Seconds visitor waits for operator to answer. 0 - wait forever.
     */
    public static function getRingTimeout()
    {
        $settings = self::getSettings();
        return is_numeric($settings['ring_timeout']) ? max(0, (int)$settings['ring_timeout']) : 60;
    }

    /**
     * Resolves chat from room name. Returns false if room name is not valid for the chat.
     */
    public static function getChatByRoomName($roomName)
    {
        if (!preg_match('/^lhc_([0-9]+)_[a-f0-9]{16}$/', (string)$roomName, $matches)) {
            return false;
        }

        $chat = erLhcoreClassModelChat::fetch((int)$matches[1], false);

        if (!($chat instanceof erLhcoreClassModelChat) || !hash_equals(self::getRoomName($chat), $roomName)) {
            return false;
        }

        return $chat;
    }

    /**
     * Builds access token for a participant.
     */
    public static function buildToken(erLhcoreClassModelChat $chat, $identity, $name = '', $canPublish = true)
    {
        $settings = self::getSettings();
        $expireTs = time() + self::getTokenTTL();

        if (!self::isConfigured()) {
            return '';
        }

        return self::buildLiveKitToken($settings['livekit_api_key'], $settings['livekit_api_secret'], self::getRoomName($chat), $identity, $name, $expireTs, array(
            'voice' => $settings['voice'] == true,
            'video' => $settings['video'] == true,
            'screenshare' => $settings['screenshare'] == true,
            'can_publish' => $canPublish,
        ));
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
                'canPublish' => !isset($options['can_publish']) || $options['can_publish'] == true,
                'canSubscribe' => true,
                'canPublishData' => !isset($options['can_publish']) || $options['can_publish'] == true,
                'canPublishSources' => (!isset($options['can_publish']) || $options['can_publish'] == true) ? $sources : array(),
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
            'room' => self::getRoomName($chat),
            'url' => $settings['livekit_url'],
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

    /**
     * Listen only token for supervisor. Issued only while call is in progress.
     */
    public static function getSupervisorToken(erLhcoreClassModelChat $chat, erLhcoreClassModelChatVoiceVideo $vvcall, $userId, $name = '')
    {
        if ($vvcall->op_status != erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED && $vvcall->vi_status != erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED) {
            return '';
        }

        return self::buildToken($chat, self::getSupervisorIdentity($userId), (string)$name, false);
    }

    /**
     * Visitor requested a call, but nobody answered within ring timeout.
     * Returns true if call request was cancelled.
     */
    public static function checkRingTimeout(erLhcoreClassModelChat $chat, erLhcoreClassModelChatVoiceVideo $vvcall)
    {
        $timeout = self::getRingTimeout();

        if ($timeout == 0 || $vvcall->vi_status != erLhcoreClassModelChatVoiceVideo::STATUS_VI_REQUESTED || $vvcall->status == erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRMED) {
            return false;
        }

        try {
            $session = self::getActiveSession($chat->id);
        } catch (Exception $e) {
            return false;
        }

        if (!($session instanceof erLhcoreClassModelChatVoiceVideoSession) || $session->answered_at > 0 || $session->ctime > time() - $timeout) {
            return false;
        }

        $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING;
        $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_PENDING;
        $vvcall->updateThis(array('update' => array('vi_status', 'status')));

        self::trackCallEnd($chat, 'no_answer');

        $chat->operation_admin = "lhinst.updateVoteStatus(" . $chat->id . ");";
        $chat->updateThis(array('update' => array('operation_admin')));

        return true;
    }

    /**
     * Verifies LiveKit webhook. Authorization header holds JWT signed with API secret with sha256 claim of the body.
     * https://docs.livekit.io/home/server/webhooks/
     */
    public static function verifyLiveKitWebhook($body, $authorization)
    {
        $settings = self::getSettings();

        if ($settings['livekit_api_key'] == '' || $settings['livekit_api_secret'] == '') {
            return false;
        }

        $authorization = trim(preg_replace('/^Bearer\s+/i', '', (string)$authorization));
        $parts = explode('.', $authorization);

        if (count($parts) != 3) {
            return false;
        }

        $header = json_decode(self::base64UrlDecode($parts[0]), true);
        if (!is_array($header) || !isset($header['alg']) || $header['alg'] !== 'HS256') {
            return false;
        }

        $signature = self::base64UrlEncode(hash_hmac('sha256', $parts[0] . '.' . $parts[1], $settings['livekit_api_secret'], true));

        if (!hash_equals($signature, $parts[2])) {
            return false;
        }

        $claims = json_decode(self::base64UrlDecode($parts[1]), true);

        if (!is_array($claims) || !isset($claims['iss']) || $claims['iss'] !== $settings['livekit_api_key']) {
            return false;
        }

        $now = time();

        if ((isset($claims['exp']) && $claims['exp'] < $now - 60) || (isset($claims['nbf']) && $claims['nbf'] > $now + 60)) {
            return false;
        }

        return isset($claims['sha256']) && hash_equals(base64_encode(hash('sha256', $body, true)), $claims['sha256']);
    }

    private static function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
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
