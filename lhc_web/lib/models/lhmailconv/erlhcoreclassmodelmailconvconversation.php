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
            'sender' => $this->sender,
            'ctime' => $this->ctime,
        );
    }

    public function __toString()
    {
        return $this->mail;
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
    public $status = '';
    public $subject = '';
    public $sender = '';
    public $ctime = 0;
}

?>