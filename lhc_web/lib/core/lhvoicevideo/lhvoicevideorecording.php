<?php

/**
 * Call recording using self hosted LiveKit Egress https://docs.livekit.io/home/egress/overview/
 *
 * Egress writes files into its output directory (recording_egress_path). The same directory has to be
 * available to Live Helper Chat (recording_storage_dir), e.g. shared docker volume or network mount.
 * Recordings are served only through Live Helper Chat with permission checks.
 */
class erLhcoreClassVoiceVideoRecording {

    const MODE_OFF = 'off';
    const MODE_MANUAL = 'manual';
    const MODE_AUTO = 'auto';

    public static function getMode()
    {
        $settings = erLhcoreClassVoiceVideo::getSettings();
        return in_array($settings['recording_mode'], array(self::MODE_MANUAL, self::MODE_AUTO)) ? $settings['recording_mode'] : self::MODE_OFF;
    }

    public static function isEnabled()
    {
        return self::getMode() != self::MODE_OFF && erLhcoreClassVoiceVideo::isConfigured();
    }

    /**
     * Server side LiveKit API URL. Defaults to signalling URL with http(s) scheme.
     */
    public static function getApiUrl()
    {
        $settings = erLhcoreClassVoiceVideo::getSettings();
        $url = $settings['livekit_api_url'] != '' ? $settings['livekit_api_url'] : $settings['livekit_url'];
        return rtrim(preg_replace(array('/^wss:\/\//i', '/^ws:\/\//i'), array('https://', 'http://'), $url), '/');
    }

    /**
     * Calls LiveKit Twirp API
     */
    public static function apiRequest($service, $method, array $payload, array $grant)
    {
        $settings = erLhcoreClassVoiceVideo::getSettings();

        $now = time();
        $token = erLhcoreClassVoiceVideo::buildJWT(array(
            'iss' => $settings['livekit_api_key'],
            'sub' => 'lhc_server',
            'nbf' => $now - 10,
            'iat' => $now,
            'exp' => $now + 60,
            'video' => $grant
        ), $settings['livekit_api_secret']);

        $ch = curl_init(self::getApiUrl() . '/twirp/livekit.' . $service . '/' . $method);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            )
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('LiveKit API is not reachable: ' . $curlError);
        }

        $data = json_decode($response, true);

        if ($httpCode != 200) {
            throw new Exception('LiveKit API error (' . $httpCode . '): ' . (isset($data['msg']) ? $data['msg'] : substr($response, 0, 200)));
        }

        return is_array($data) ? $data : array();
    }

    public static function getActiveRecording($chatId)
    {
        return erLhcoreClassModelChatVoiceVideoRecording::findOne(array(
            'filter' => array('chat_id' => $chatId),
            'filterin' => array('status' => array(erLhcoreClassModelChatVoiceVideoRecording::STATUS_STARTING, erLhcoreClassModelChatVoiceVideoRecording::STATUS_ACTIVE)),
            'sort' => 'id DESC'
        ));
    }

    public static function isRecording($chatId)
    {
        try {
            return self::getActiveRecording($chatId) instanceof erLhcoreClassModelChatVoiceVideoRecording;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Starts room composite recording. Returns recording or throws exception.
     */
    public static function start(erLhcoreClassModelChat $chat, $userId = 0)
    {
        if (!self::isEnabled()) {
            throw new Exception('Recording is disabled');
        }

        $active = self::getActiveRecording($chat->id);
        if ($active instanceof erLhcoreClassModelChatVoiceVideoRecording) {
            return $active;
        }

        $settings = erLhcoreClassVoiceVideo::getSettings();
        $session = erLhcoreClassVoiceVideo::getActiveSession($chat->id);

        $recording = new erLhcoreClassModelChatVoiceVideoRecording();
        $recording->chat_id = $chat->id;
        $recording->session_id = $session instanceof erLhcoreClassModelChatVoiceVideoSession ? $session->id : 0;
        $recording->user_id = (int)$userId;
        $recording->audio_only = $settings['recording_audio_only'] == true || !($session instanceof erLhcoreClassModelChatVoiceVideoSession && $session->video == 1) ? 1 : 0;
        $recording->ctime = time();
        $recording->saveThis();

        $roomName = erLhcoreClassVoiceVideo::getRoomName($chat);
        $relativePath = date('Y/m/d', $recording->ctime) . '/chat-' . $chat->id . '-' . $recording->id . '-' . $recording->ctime . ($recording->audio_only == 1 ? '.ogg' : '.mp4');

        try {
            $info = self::apiRequest('Egress', 'StartRoomCompositeEgress', array(
                'room_name' => $roomName,
                'layout' => 'grid',
                'audio_only' => $recording->audio_only == 1,
                'file_outputs' => array(
                    array(
                        'file_type' => $recording->audio_only == 1 ? 'OGG' : 'MP4',
                        'filepath' => rtrim($settings['recording_egress_path'], '/') . '/' . $relativePath
                    )
                )
            ), array('roomRecord' => true));

            $recording->egress_id = isset($info['egressId']) ? $info['egressId'] : (isset($info['egress_id']) ? $info['egress_id'] : '');
            $recording->file_path = $relativePath;
            $recording->updateThis(array('update' => array('egress_id', 'file_path')));

        } catch (Exception $e) {
            $recording->status = erLhcoreClassModelChatVoiceVideoRecording::STATUS_FAILED;
            $recording->error = mb_substr($e->getMessage(), 0, 250);
            $recording->ended_at = time();
            $recording->updateThis(array('update' => array('status', 'error', 'ended_at')));
            throw $e;
        }

        erLhcoreClassLog::write('Call recording started in chat ' . $chat->id . ' by user ' . (int)$userId,
            ezcLog::SUCCESS_AUDIT,
            array(
                'source' => 'lhc',
                'category' => 'voice_call_recording',
                'line' => __LINE__,
                'file' => __FILE__,
                'object_id' => $chat->id
            )
        );

        return $recording;
    }

    public static function stop(erLhcoreClassModelChatVoiceVideoRecording $recording)
    {
        if (!$recording->is_active) {
            return;
        }

        try {
            if ($recording->egress_id != '') {
                self::apiRequest('Egress', 'StopEgress', array('egress_id' => $recording->egress_id), array('roomRecord' => true));
            }
        } catch (Exception $e) {
            // Egress might be already finished. Final status arrives by webhook.
        }

        if ($recording->egress_id == '') {
            $recording->status = erLhcoreClassModelChatVoiceVideoRecording::STATUS_FAILED;
            $recording->ended_at = time();
            $recording->updateThis(array('update' => array('status', 'ended_at')));
        }
    }

    public static function stopByChatId($chatId)
    {
        try {
            foreach (erLhcoreClassModelChatVoiceVideoRecording::getList(array(
                'filter' => array('chat_id' => $chatId),
                'filterin' => array('status' => array(erLhcoreClassModelChatVoiceVideoRecording::STATUS_STARTING, erLhcoreClassModelChatVoiceVideoRecording::STATUS_ACTIVE))
            )) as $recording) {
                self::stop($recording);
            }
        } catch (Exception $e) {

        }
    }

    /**
     * Starts recording automatically if configured so.
     */
    public static function autoStart(erLhcoreClassModelChat $chat, $userId = 0)
    {
        if (self::getMode() == self::MODE_AUTO && self::isEnabled()) {
            try {
                self::start($chat, $userId);
            } catch (Exception $e) {
                // Call continues without recording. Failure is visible in call history.
            }
        }
    }

    /**
     * Updates recording from LiveKit egress_* webhook
     */
    public static function handleEgressEvent(array $egressInfo)
    {
        $egressId = isset($egressInfo['egressId']) ? $egressInfo['egressId'] : (isset($egressInfo['egress_id']) ? $egressInfo['egress_id'] : '');

        if ($egressId == '') {
            return false;
        }

        $recording = erLhcoreClassModelChatVoiceVideoRecording::findOne(array('filter' => array('egress_id' => $egressId)));

        if (!($recording instanceof erLhcoreClassModelChatVoiceVideoRecording)) {
            return false;
        }

        $status = isset($egressInfo['status']) ? $egressInfo['status'] : '';

        if ($status == 'EGRESS_ACTIVE') {
            $recording->status = erLhcoreClassModelChatVoiceVideoRecording::STATUS_ACTIVE;
        } elseif (in_array($status, array('EGRESS_COMPLETE', 'EGRESS_ENDING'))) {
            $recording->status = $status == 'EGRESS_COMPLETE' ? erLhcoreClassModelChatVoiceVideoRecording::STATUS_COMPLETED : $recording->status;
        } elseif (in_array($status, array('EGRESS_FAILED', 'EGRESS_ABORTED', 'EGRESS_LIMIT_REACHED'))) {
            $recording->status = $status == 'EGRESS_LIMIT_REACHED' ? erLhcoreClassModelChatVoiceVideoRecording::STATUS_COMPLETED : erLhcoreClassModelChatVoiceVideoRecording::STATUS_FAILED;
            $recording->error = mb_substr(isset($egressInfo['error']) ? $egressInfo['error'] : $status, 0, 250);
        }

        $fileResults = isset($egressInfo['fileResults']) ? $egressInfo['fileResults'] : (isset($egressInfo['file_results']) ? $egressInfo['file_results'] : array());
        if (empty($fileResults) && isset($egressInfo['file'])) {
            $fileResults = array($egressInfo['file']);
        }

        if (!empty($fileResults) && is_array($fileResults[0])) {
            $file = $fileResults[0];
            if (isset($file['size'])) {
                $recording->file_size = (int)$file['size'];
            }
            if (isset($file['duration'])) {
                // Nanoseconds
                $recording->duration = (int)round(((float)$file['duration']) / 1000000000);
            }
            if (isset($file['filename']) && $file['filename'] != '') {
                $relative = self::toRelativePath($file['filename']);
                if ($relative !== false) {
                    $recording->file_path = $relative;
                }
            }
        }

        if (!$recording->is_active && $recording->ended_at == 0) {
            $recording->ended_at = time();
        }

        $recording->updateThis(array('update' => array('status', 'error', 'file_size', 'duration', 'file_path', 'ended_at')));

        return true;
    }

    /**
     * Egress output path to path relative to recordings directory
     */
    public static function toRelativePath($filename)
    {
        $settings = erLhcoreClassVoiceVideo::getSettings();
        $prefix = rtrim($settings['recording_egress_path'], '/') . '/';

        if (strpos($filename, $prefix) === 0) {
            $filename = substr($filename, strlen($prefix));
        }

        $filename = ltrim($filename, '/');

        if ($filename == '' || strpos($filename, '..') !== false || !preg_match('/^[a-zA-Z0-9_\-\.\/]+$/', $filename)) {
            return false;
        }

        return $filename;
    }

    /**
     * Absolute path of recording file on Live Helper Chat server or false.
     */
    public static function getLocalFile(erLhcoreClassModelChatVoiceVideoRecording $recording)
    {
        $settings = erLhcoreClassVoiceVideo::getSettings();

        if ($settings['recording_storage_dir'] == '' || $recording->file_path == '' || self::toRelativePath($recording->file_path) === false) {
            return false;
        }

        return rtrim($settings['recording_storage_dir'], '/') . '/' . $recording->file_path;
    }

    /**
     * Removes recordings older than retention days. Returns number of removed recordings.
     */
    public static function applyRetention()
    {
        $settings = erLhcoreClassVoiceVideo::getSettings();
        $days = (int)$settings['recording_retention_days'];

        $removed = 0;

        // Recordings which never received final status
        foreach (erLhcoreClassModelChatVoiceVideoRecording::getList(array(
            'limit' => 500,
            'filterin' => array('status' => array(erLhcoreClassModelChatVoiceVideoRecording::STATUS_STARTING, erLhcoreClassModelChatVoiceVideoRecording::STATUS_ACTIVE)),
            'filterlt' => array('ctime' => time() - 12 * 3600)
        )) as $recording) {
            $recording->status = erLhcoreClassModelChatVoiceVideoRecording::STATUS_FAILED;
            $recording->error = 'No final status received from egress';
            $recording->ended_at = time();
            $recording->updateThis(array('update' => array('status', 'error', 'ended_at')));
        }

        if ($days <= 0) {
            return $removed;
        }

        do {
            $items = erLhcoreClassModelChatVoiceVideoRecording::getList(array(
                'limit' => 200,
                'filterlt' => array('ctime' => time() - $days * 86400),
                'filternotin' => array('status' => array(erLhcoreClassModelChatVoiceVideoRecording::STATUS_STARTING, erLhcoreClassModelChatVoiceVideoRecording::STATUS_ACTIVE))
            ));

            foreach ($items as $item) {
                $item->removeThis();
                $removed++;
            }
        } while (count($items) == 200);

        return $removed;
    }

    public static function deleteByChatId($chatId)
    {
        try {
            foreach (erLhcoreClassModelChatVoiceVideoRecording::getList(array('limit' => false, 'filter' => array('chat_id' => $chatId))) as $recording) {
                $recording->removeThis();
            }
        } catch (Exception $e) {

        }
    }
}

?>
