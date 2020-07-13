<?php

class erLhcoreClassModelMailconvConversation
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_conversation';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'subject' => $this->subject,
            'from_name' => $this->from_name,
            'from_address' => $this->from_address,
            'body' => $this->body,
            'ctime' => $this->ctime,
            'priority' => $this->priority,
            'last_message_id' => $this->last_message_id,
        );
    }

    public function __toString()
    {
        return $this->mail;
    }

    public function beforeSave()
    {
        if ($this->ctime == 0) {
            $this->ctime = time();
        }
    }

    public function __get($var)
    {
        switch ($var) {
            case 'ctime_front':
                return date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                break;

            default:
                ;
                break;
        }
    }

    public $id = NULL;
    public $dep_id = null;
    public $user_id = 0;
    public $status = 0;
    public $subject = '';
    public $body = '';
    public $from_name = '';
    public $from_address = '';
    public $last_message_id = 0;
    public $ctime = 0;
    public $priority = 0;
}

?>