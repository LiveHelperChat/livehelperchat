<?php

class erLhcoreClassModelChatVoiceVideo {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_voice_video';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'             => $this->id,
            'chat_id'        => $this->chat_id,
            'user_id'        => $this->user_id,
            'op_status'      => $this->op_status,
            'vi_status'      => $this->vi_status,
            'voice'          => $this->voice,
            'video'          => $this->video,
            'screen_share'   => $this->screen_share,
            'token'          => $this->token,
            'status'         => $this->status,
            'ctime'          => $this->ctime,
        );
    }

    public static function getInstance($chatId, $store = true) {
        $vvcall = self::findOne(array('filter' => array('chat_id' => $chatId)));
        if (!($vvcall instanceof erLhcoreClassModelChatVoiceVideo)) {
            
            $vvcall = new self();
            $vvcall->ctime = time();
            $vvcall->chat_id = $chatId;

            if ($store == true) {
                $vvcall->saveThis();
            }
        }
        return $vvcall;
    }

    public function __get($var) {
        switch ($var) {
            case 'ctime_front':
                $this->ctime_front = date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                return $this->ctime_front;
            default:
                break;
        }
    }

    const STATUS_OP_PENDING = 0;
    const STATUS_OP_JOINED = 1;

    const STATUS_VI_PENDING = 0;
    const STATUS_VI_REQUESTED = 1;
    const STATUS_VI_JOINED = 2;

    const STATUS_PENDING = 0;
    const STATUS_CONFIRM = 1;
    const STATUS_CONFIRMED = 2;

    public $id = null;
    public $chat_id = 0;
    public $user_id = 0;
    public $ctime = 0;

    public $status = self::STATUS_PENDING;

    public $op_status = self::STATUS_OP_PENDING;
    public $vi_status = self::STATUS_VI_PENDING;

    public $voice = 0;
    public $video = 0;
    public $screen_share = 0;

    public $token = '';
}

?>