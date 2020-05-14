<?php

class erLhcoreClassModelGroupChatMember
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_group_chat_member';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'group_id' => $this->group_id,
            'last_activity' => $this->last_activity,
            'jtime' => $this->jtime
        );
    }

    public function __get($var)
    {

        switch ($var) {
            case 'last_activity_front':
                if (date('Ymd') == date('Ymd', $this->last_activity)) {
                    $this->last_activity_front = date(erLhcoreClassModule::$dateHourFormat, $this->last_activity);
                } else {
                    $this->last_activity_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->last_activity);
                }
                return $this->last_activity_front;
                break;

            case 'jtime_front':
                if (date('Ymd') == date('Ymd', $this->jtime)) {
                    $this->jtime_front = date(erLhcoreClassModule::$dateHourFormat, $this->jtime);
                } else {
                    $this->jtime_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->jtime);
                }
                return $this->jtime_front;
                break;


            default:
                break;
        }
    }

    public $id = null;
    public $user_id = 0;
    public $group_id = 0;
    public $last_activity = 0;
    public $jtime = 0;

}

?>