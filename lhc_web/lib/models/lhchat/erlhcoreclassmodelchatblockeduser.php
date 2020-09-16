<?php

class erLhcoreClassModelChatBlockedUser
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_blocked_user';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'ip' => $this->ip,
            'user_id' => $this->user_id,
            'datets' => $this->datets,
            'chat_id' => $this->chat_id,
            'dep_id' => $this->dep_id,
            'nick' => $this->nick,
        );
    }

    public function __get($var)
    {
        switch ($var) {
            case 'datets_front':
                return date(erLhcoreClassModule::$dateDateHourFormat, $this->datets);

            case 'user':
                try {
                    $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                } catch (Exception $e) {
                    $this->user = '-';
                }
                return $this->user;

            case 'department':
                $this->department = null;
                if ($this->dep_id > 0) {
                    $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id);
                }
                return $this->department;

            default:
                break;
        }
    }

    public function beforeSave()
    {
        $this->datets = time();
    }

    public $id = null;
    public $ip = '';
    public $user_id = 0;
    public $datets = null;
    public $chat_id = 0;
    public $dep_id = 0;
    public $nick = '';
}

?>