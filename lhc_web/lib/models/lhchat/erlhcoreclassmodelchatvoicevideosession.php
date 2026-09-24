<?php

/**
 * Voice & Video call history record. One record per call attempt.
 * Unlike lh_chat_voice_video (live call state) records are kept after chat close.
 */
class erLhcoreClassModelChatVoiceVideoSession {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_voice_video_session';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'            => $this->id,
            'chat_id'       => $this->chat_id,
            'user_id'       => $this->user_id,
            'dep_id'        => $this->dep_id,
            'provider'      => $this->provider,
            'initiator'     => $this->initiator,
            'voice'         => $this->voice,
            'video'         => $this->video,
            'status'        => $this->status,
            'ctime'         => $this->ctime,
            'answered_at'   => $this->answered_at,
            'ended_at'      => $this->ended_at,
            'duration'      => $this->duration,
            'end_reason'    => $this->end_reason,
        );
    }

    public function __get($var) {
        switch ($var) {
            case 'ctime_front':
            case 'answered_at_front':
            case 'ended_at_front':
                $attr = str_replace('_front', '', $var);
                $this->$var = $this->$attr > 0 ? date(erLhcoreClassModule::$dateDateHourFormat, $this->$attr) : '';
                return $this->$var;

            case 'duration_front':
                $this->duration_front = erLhcoreClassVoiceVideo::formatDuration($this->duration);
                return $this->duration_front;

            case 'wait_time':
                $this->wait_time = $this->answered_at > 0 ? max(0, $this->answered_at - $this->ctime) : 0;
                return $this->wait_time;

            case 'user':
                $this->user = $this->user_id > 0 ? erLhcoreClassModelUser::fetch($this->user_id, true) : false;
                return $this->user;

            case 'department':
                $this->department = $this->dep_id > 0 ? erLhcoreClassModelDepartament::fetch($this->dep_id, true) : false;
                return $this->department;

            case 'status_front':
                $trans = erTranslationClassLhTranslation::getInstance();
                $statuses = array(
                    self::STATUS_RINGING => $trans->getTranslation('chat/voice_video', 'Ringing'),
                    self::STATUS_ACTIVE => $trans->getTranslation('chat/voice_video', 'In progress'),
                    self::STATUS_ENDED => $trans->getTranslation('chat/voice_video', 'Completed'),
                    self::STATUS_MISSED => $trans->getTranslation('chat/voice_video', 'Not answered'),
                );
                $this->status_front = isset($statuses[$this->status]) ? $statuses[$this->status] : '';
                return $this->status_front;

            default:
                break;
        }
    }

    const STATUS_RINGING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ENDED = 2;
    const STATUS_MISSED = 3;

    const INITIATOR_VISITOR = 0;
    const INITIATOR_OPERATOR = 1;

    public $id = null;
    public $chat_id = 0;
    public $user_id = 0;
    public $dep_id = 0;
    public $provider = '';
    public $initiator = self::INITIATOR_VISITOR;
    public $voice = 0;
    public $video = 0;
    public $status = self::STATUS_RINGING;
    public $ctime = 0;
    public $answered_at = 0;
    public $ended_at = 0;
    public $duration = 0;
    public $end_reason = '';
}

?>
