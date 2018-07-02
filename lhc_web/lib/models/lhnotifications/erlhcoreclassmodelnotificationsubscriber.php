<?php

class erLhcoreClassModelNotificationSubscriber{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_notification_subscriber';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassNotifications::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'         	    => $this->id,
            'chat_id'           => $this->chat_id,
            'online_user_id'    => $this->online_user_id,
            'dep_id'    	    => $this->dep_id,
            'ctime'    		    => $this->ctime,
            'utime'    		    => $this->utime,
            'status'            => $this->status,
            'params'            => $this->params,
            'subscriber_hash'  	=> $this->subscriber_hash,
            'last_error'  	    => $this->last_error,
            'device_type'  	    => $this->device_type,
            'uagent'  	        => $this->uagent,
            'ip'  	            => $this->ip,
            'theme_id'  	    => $this->theme_id,
        );
    }

    public function __get($var){

        switch ($var) {

            case 'chat':
                $this->chat = false;
                if ($this->chat_id > 0){
                    try {
                        $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                    } catch (Exception $e) {
                        $this->chat = new erLhcoreClassModelChat();
                    }
                }
                return $this->chat;
                ;
                break;

            case 'department':
                $this->department = false;
                if ($this->dep_id > 0) {
                    try {
                        $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id,true);
                    } catch (Exception $e) {

                    }
                }

                return $this->department;
                break;

            case 'theme':
                $this->theme = false;
                if ($this->theme_id > 0) {
                    try {
                        $this->theme = erLhAbstractModelWidgetTheme::fetch($this->theme_id,true);
                    } catch (Exception $e) {

                    }
                }
                return $this->theme;
                break;

            case 'ctime_front':
                $this->ctime_front = date(erLhcoreClassModule::$dateDateHourFormat,$this->ctime);
                return $this->ctime_front;
                break;

            case 'utime_front':
                $this->utime_front = date(erLhcoreClassModule::$dateDateHourFormat,$this->utime);
                return $this->utime_front;
                break;

            default:
                ;
                break;
        }
    }

    const STATUS_SUBSCRIBED = 0;

    public $id = null;
    public $chat_id = 0;
    public $online_user_id = 0;
    public $dep_id = 0;
    public $ctime = 0;
    public $utime = 0;
    public $status = self::STATUS_SUBSCRIBED;
    public $params = '';
    public $subscriber_hash = '';
    public $last_error = '';

    // 0 - PC, 1 - mobile, 2 - tablet
    public $device_type = 0;
    public $uagent = '';
    public $ip = '';

    public $theme_id = 0;
}

?>