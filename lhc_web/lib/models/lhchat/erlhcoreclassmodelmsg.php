<?php
/**
 *
 * user_id - administartor user_id,
 * If 0 web user
 *
 * */

class erLhcoreClassModelmsg
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_msg';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'msg' => $this->msg,
            'meta_msg' => $this->meta_msg,
            'time' => $this->time,
            'chat_id' => $this->chat_id,
            'user_id' => $this->user_id,
            'name_support' => $this->name_support,
            'del_st' => $this->del_st
        );
    }

    public function __get($var)
    {

        switch ($var) {
            case 'time_front':
                if (date('Ymd') == date('Ymd', $this->time)) {
                    $this->time_front = date(erLhcoreClassModule::$dateHourFormat, $this->time);
                } else {
                    $this->time_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->time);
                }
                return $this->time_front;
                break;

            case 'meta_msg_array':
                $this->meta_msg_array = array();
                if ($this->meta_msg != '')
                {
                    $jsonData = json_decode($this->meta_msg,true);
                    if ($jsonData !== null) {
                        $this->meta_msg_array = $jsonData;
                    }
                }
                return $this->meta_msg_array;
                break;

            default:
                break;
        }
    }

    const STATUS_PENDING = 0;   // <span class="material-symbols-outlined">radio_button_unchecked</span>
    const STATUS_SENT = 1;      // <span class="material-symbols-outlined">done</span>
    const STATUS_DELIVERED = 2; // <span class="material-symbols-outlined">done_all</span>
    const STATUS_READ = 3;      // <span class="material-symbols-outlined">unpublished</span>
    const STATUS_REJECTED = 4; // <span class="material-symbols-outlined">unpublished</span>

    public $id = null;
    public $time = '';
    public $chat_id = null;
    public $user_id = null;
    public $name_support = '';
    public $msg = '';
    public $meta_msg = '';
    public $del_st = SELF::STATUS_PENDING;
}

?>