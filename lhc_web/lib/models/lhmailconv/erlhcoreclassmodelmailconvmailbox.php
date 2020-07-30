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
            'imap' => $this->imap,
            'last_sync_time' => $this->last_sync_time,
            'last_sync_log' => $this->last_sync_log,
            'mailbox_sync' => $this->mailbox_sync,
            'sync_status' => $this->sync_status,
            'sync_interval' => $this->sync_interval,
            'signature' => $this->signature,
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

            case 'last_sync_log_array':
                $this->last_sync_log_array = array();
                if ($this->last_sync_log != '') {
                    $this->last_sync_log_array = json_decode($this->last_sync_log, true);
                }
                return $this->last_sync_log_array;

            case 'mailbox_sync_array':
                $this->mailbox_sync_array = array();
                if ($this->mailbox_sync != '') {
                    $this->mailbox_sync_array = json_decode($this->mailbox_sync, true);
                }
                return $this->mailbox_sync_array;

            case 'name':
                return $this->mail;

            case 'last_sync_time_ago':
                $this->last_sync_time_ago = erLhcoreClassChat::formatSeconds(time() - $this->last_sync_time);
                return $this->last_sync_time_ago;
                
            default:
                break;
        }
    }

    const SYNC_PENDING = 0;
    const SYNC_PROGRESS = 1;

    public $id = NULL;
    public $mail = '';
    public $username = '';
    public $password = '';
    public $host = '';
    public $port = '';
    public $imap = '';
    public $active = 1;
    public $last_sync_time = 0;
    public $last_sync_log = '';
    public $mailbox_sync = '';
    public $signature = '';
    public $sync_status = 0;
    public $sync_interval = 60;
}

?>