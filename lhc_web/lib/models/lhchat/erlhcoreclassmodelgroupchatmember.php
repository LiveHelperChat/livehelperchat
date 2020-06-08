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
            'last_msg_id' => $this->last_msg_id, // Last message operator has fetched
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

         case 'last_activity_ago':
            $this->last_activity_ago = erLhcoreClassChat::getAgoFormat($this->last_activity);

            return $this->last_activity_ago;
            break;

        case 'hide_online':
                if ($this->user !== false) {
                    $this->hide_online = (string)$this->user->hide_online != 0;
                }
                return $this->hide_online;
            break;

        case 'user':
            $this->user = false;
            if ($this->user_id > 0) {
                try {
                    $this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
                } catch (Exception $e) {
                    $this->user = false;
                }
            }
            return $this->user;
            break;

        case 'n_off_full':
            $this->n_off_full = null;

            if ($this->user !== false) {
                $this->n_off_full = (string)$this->user->name_official;
            }

            return $this->n_off_full;
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
    public $last_msg_id = 0;
    public $jtime = 0;

}

?>