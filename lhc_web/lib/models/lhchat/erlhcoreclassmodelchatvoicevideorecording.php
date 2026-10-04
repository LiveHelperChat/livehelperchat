<?php

/**
 * Call recording made by self hosted LiveKit Egress.
 */
class erLhcoreClassModelChatVoiceVideoRecording {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_voice_video_recording';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'            => $this->id,
            'chat_id'       => $this->chat_id,
            'session_id'    => $this->session_id,
            'user_id'       => $this->user_id,
            'egress_id'     => $this->egress_id,
            'status'        => $this->status,
            'audio_only'    => $this->audio_only,
            'file_path'     => $this->file_path,
            'file_size'     => $this->file_size,
            'duration'      => $this->duration,
            'ctime'         => $this->ctime,
            'ended_at'      => $this->ended_at,
            'error'         => $this->error,
        );
    }

    public function __get($var) {
        switch ($var) {
            case 'ctime_front':
                $this->ctime_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                return $this->ctime_front;

            case 'duration_front':
                $this->duration_front = erLhcoreClassVoiceVideo::formatDuration($this->duration);
                return $this->duration_front;

            case 'file_size_front':
                $this->file_size_front = $this->file_size > 1048576 ? round($this->file_size / 1048576, 1) . ' MB' : round($this->file_size / 1024) . ' KB';
                return $this->file_size_front;

            case 'local_file':
                $this->local_file = erLhcoreClassVoiceVideoRecording::getLocalFile($this);
                return $this->local_file;

            case 'is_active':
                return in_array($this->status, array(self::STATUS_STARTING, self::STATUS_ACTIVE));

            default:
                break;
        }
    }

    public function beforeRemove()
    {
        $file = $this->local_file;
        if ($file !== false && is_file($file)) {
            unlink($file);
        }
    }

    const STATUS_STARTING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;

    public $id = null;
    public $chat_id = 0;
    public $session_id = 0;
    public $user_id = 0;
    public $egress_id = '';
    public $status = self::STATUS_STARTING;
    public $audio_only = 0;
    public $file_path = '';
    public $file_size = 0;
    public $duration = 0;
    public $ctime = 0;
    public $ended_at = 0;
    public $error = '';
}

?>
