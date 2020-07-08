<?php

class erLhcoreClassModelMailconvMailbox
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_mailbox';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'mail' => $this->mail,
            'username' => $this->username,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'active' => $this->active,
        );
    }

    public function __toString()
    {
        return $this->mail;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return date('Ymd') == date('Ymd', $this->mtime) ? date(erLhcoreClassModule::$dateHourFormat, $this->mtime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->mtime);
                break;

            default:
                ;
                break;
        }
    }

    public $id = NULL;
    public $mail = '';
    public $username = '';
    public $password = '';
    public $host = '';
    public $port = '';
    public $active = 1;
}

?>