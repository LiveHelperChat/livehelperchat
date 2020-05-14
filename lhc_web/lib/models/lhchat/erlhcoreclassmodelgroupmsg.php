<?php
/**
 *
 * user_id - administartor user_id,
 * If 0 web user
 *
 * */

class erLhcoreClassModelGroupMsg
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_group_msg';

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
            'name_support' => $this->name_support
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

    public $id = null;
    public $time = '';
    public $chat_id = null;
    public $user_id = null;
    public $name_support = '';
    public $msg = '';
    public $meta_msg = '';
}

?>