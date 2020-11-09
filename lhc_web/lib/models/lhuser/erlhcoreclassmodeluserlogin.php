<?php

class erLhcoreClassModelUserLogin {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_users_login';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'ctime' => $this->ctime,
            'status' => $this->status,
            'msg' => $this->msg,
            'ip' => $this->ip
        );
    }

    public function __get($var)
    {
        switch ($var) {
            case 'ctime_front':
                $this->ctime_front = date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                return $this->ctime_front;
        }
    }

    public static function logUserAction($params){
        $userLogin = new erLhcoreClassModelUserLogin();
        $userLogin->ctime = time();
        $userLogin->user_id = $params['user_id'];
        $userLogin->type = $params['type'];
        $userLogin->msg = $params['msg'];
        $userLogin->ip = isset($params['ip']) ? $params['ip'] : erLhcoreClassIPDetect::getIP();
        $userLogin->saveThis();
    }

    public static function disableIfRequired($user) {
        $logList= self::getList(array('id' => 'desc', 'limit' => 15,'filterin' => array('type' => array(self::TYPE_LOGGED,self::TYPE_LOGIN_ATTEMPT)), 'filter' => array('user_id' => $user->id)));
        $attempts = 0;
        foreach ($logList as $log) {
            if ($log->type == self::TYPE_LOGIN_ATTEMPT) {
                $attempts++;
            } elseif ($log->type == self::TYPE_LOGGED) {
                break;
            }
        }

        if ($attempts > 0) {
            $passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;
            if (isset($passwordData['max_attempts']) && is_numeric($passwordData['max_attempts']) && $passwordData['max_attempts'] > 0 && $attempts >= $passwordData['max_attempts']) {
                self::logUserAction(array(
                    'user_id' => $user->id,
                    'type' => self::TYPE_BAN_TO_MANY_FAIL,
                    'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Account disabled because of to many failed logins!'),
                ));
                $user->disabled = 1;
                $user->saveThis();
            }
        }
    }

    const TYPE_LOGIN_ATTEMPT = 0;
    const TYPE_LOGGED = 1;
    const TYPE_PASSWORD_RESET_REQUEST = 2;
    const TYPE_PASSWORD_UPDATED = 3;
    const TYPE_BAN_TO_MANY_FAIL = 4;

    const STATUS_PENDING = 0;
    const STATUS_COMPLETED = 1;

    public $id = null;
    public $user_id = null;
    public $ip = '';
    public $type = self::TYPE_LOGIN_ATTEMPT;
    public $ctime = null;
    public $status = self::STATUS_PENDING;
    public $msg = '';
}

?>