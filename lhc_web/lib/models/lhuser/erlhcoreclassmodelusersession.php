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
            'expires_on' => $this->expires_on
        );
    }

    const DEVICE_TYPE_UNKNOWN = 0;
    const DEVICE_TYPE_ANDROID = 1;
    const DEVICE_TYPE_IOS = 2;
    
    public $id = null;

    public $token = '';
    
    public $device_token = '';

    // 0 - unknown, 1 - iphone, 2 - android
    public $device_type = 0;

    public $user_id = '';

    public $created_on = '';

    public $updated_on = '';

    public $expires_on = '';
}

?>