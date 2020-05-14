<?php

class erLhcoreClassModelGroupChat
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_group_chat';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'time' => $this->time,
            'user_id' => $this->user_id,
            'last_msg_op_id' => $this->last_msg_op_id,
            'last_msg' => $this->last_msg,
            'last_user_msg_time' => $this->last_user_msg_time,
            'last_msg_id' => $this->last_msg_id
        );
    }

    public function __get($var)
    {

        switch ($var) {

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    try {
                        $this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
                    } catch (Exception $e) {
                        $this->user = null;
                    }
                }
                return $this->user;
                break;

            case 'last_user_msg_time_front':
                if (date('Ymd') == date('Ymd', $this->last_user_msg_time)) {
                    $this->last_user_msg_time_front = date(erLhcoreClassModule::$dateHourFormat, $this->last_user_msg_time);
                } else {
                    $this->last_user_msg_time_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->last_user_msg_time);
                }
                return $this->last_user_msg_time_front;
                break;

            case 'time_front':
                if (date('Ymd') == date('Ymd', $this->time)) {
                    $this->time_front = date(erLhcoreClassModule::$dateHourFormat, $this->time);
                } else {
                    $this->time_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->time);
                }
                return $this->time_front;
                break;

            default:
                break;
        }
    }

    public $id = null;
    public $name = '';
    public $status = 0;
    public $time = null;
    public $user_id = '';
    public $last_msg_op_id = '';
    public $last_msg = '';
    public $last_user_msg_time = 0;
    public $last_msg_id = 0;
}

?>