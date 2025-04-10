<?php

namespace LiveHelperChat\Models\Notifications;

class OperatorSubscriber {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_notification_op_subscriber';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'params' => $this->params,
            'device_type' => $this->device_type,
            'last_error' => $this->last_error,
            'subscriber_hash' => $this->subscriber_hash,
            'ctime' => $this->ctime,
            'utime' => $this->utime,
            'status' => $this->status,
            'pchat' => $this->pchat, // Receive pending chat notification
            'achat' => $this->achat  // Receive assigned chat notification
        );

        return $stateArray;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'user':
                if (!isset($this->attributes['user'])) {
                    $this->attributes['user'] = false;
                    if ($this->user_id > 0) {
                        try {
                            $this->attributes['user'] = \erLhcoreClassModelUser::fetch($this->user_id, true);
                        } catch (\Exception $e) {
                            $this->attributes['user'] = false;
                        }
                    }
                }
                return $this->attributes['user'];

            case 'user_name':
                if (!isset($this->attributes['user_name'])) {
                    $this->attributes['user_name'] = (string)$this->user;
                }
                return $this->attributes['user_name'];

            case 'plain_user_name':
                if (!isset($this->attributes['plain_user_name'])) {
                    $this->attributes['plain_user_name'] = false;

                    if ($this->user !== false) {
                        $this->attributes['plain_user_name'] = (string)$this->user->name_support;
                    }
                }
                return $this->attributes['plain_user_name'];

            case 'n_official':
                if (!isset($this->attributes['n_office'])) {
                    $this->attributes['n_office'] = false;

                    if ($this->user !== false) {
                        $this->attributes['n_office'] = (string)$this->user->name;
                        if ($this->attributes['n_office'] == '') {
                            $this->attributes['n_office'] = $this->plain_user_name;
                        }
                    }
                }
                return $this->attributes['n_office'];

            case 'ctime_front':
                if (!isset($this->attributes['ctime_front'])) {
                    $this->attributes['ctime_front'] = date(\erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                }
                return $this->attributes['ctime_front'];

            case 'utime_front':
                if (!isset($this->attributes['utime_front'])) {
                    $this->attributes['utime_front'] = date(\erLhcoreClassModule::$dateDateHourFormat, $this->utime);
                }
                return $this->attributes['utime_front'];

            default:
                break;
        }
    }

    protected $attributes = [];

    const STATUS_SUBSCRIBED = 0;

    public $id = null;
    public $chat_id = 0;
    public $ctime = 0;
    public $utime = 0;
    public $status = self::STATUS_SUBSCRIBED;
    public $params = '';
    public $subscriber_hash = '';
    public $last_error = '';
    public $achat = 0;
    public $pchat = 0;

    // 0 - PC, 1 - mobile, 2 - tablet
    public $device_type = 0;
}