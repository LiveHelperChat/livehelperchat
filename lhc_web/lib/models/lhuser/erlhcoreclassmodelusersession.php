<?php

class erLhcoreClassModelUserSession
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_users_session';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';

    public static $dbSortOrder = 'DESC';
    
    public function getState()
    {
        return array(
            'id' => $this->id,
            'token' => $this->token,
            'device_token' => $this->device_token,
            'device_type' => $this->device_type,
            'user_id' => $this->user_id,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
            'expires_on' => $this->expires_on,
            'error' => $this->error,
            'last_error' => $this->last_error,
            'notifications_status' => $this->notifications_status,
        );
    }

    public function __get($var)
    {
        switch ($var) {

            case 'user':
                $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                return $this->user;
                break;

            case 'updated_on_front':
            case 'created_on_front':
                $varReplaced = str_replace('_front','',$var);
                $this->{$var} = date('Ymd') == date('Ymd',$this->{$varReplaced}) ? date(erLhcoreClassModule::$dateHourFormat,$this->{$varReplaced}) : date(erLhcoreClassModule::$dateDateHourFormat,$this->{$varReplaced});
                return $this->{$var};

            default:
                ;
                break;
        }
    }

    const DEVICE_TYPE_UNKNOWN = 0;
    const DEVICE_TYPE_ANDROID = 1;
    const DEVICE_TYPE_IOS = 2;
    
    public $id = null;

    public $token = '';
    
    public $device_token = '';

    public $device_type = 0;

    public $user_id = '';

    public $created_on = '';

    public $updated_on = '';

    public $expires_on = '';

    public $error = 0;

    public $last_error = '';

    public $notifications_status = 1;
}

?>