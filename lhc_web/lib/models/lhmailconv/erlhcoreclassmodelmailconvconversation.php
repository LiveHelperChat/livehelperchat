<?php

class erLhcoreClassModelMailconvConversation
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_conversation';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'udate DESC';

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
            'message_id' => $this->message_id,
            'mailbox_id' => $this->mailbox_id,
            'udate' => $this->udate,
            'date' => $this->date,
            'total_messages' => $this->total_messages,
            'match_rule_id' => $this->match_rule_id,
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

        $this->total_messages = erLhcoreClassModelMailconvMessage::getCount(['filter' => ['conversation_id' => $this->id]]);
    }

    public function __get($var)
    {
        switch ($var) {
            case 'ctime_front':
                return date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);

            case 'udate_front':
                return date('Ymd') == date('Ymd', $this->udate) ? date(erLhcoreClassModule::$dateHourFormat, $this->udate) : date(erLhcoreClassModule::$dateDateHourFormat, $this->udate);

            default:
                ;
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CLOSED = 2;

    public $id = NULL;
    public $dep_id = null;
    public $user_id = 0;
    public $status = 0;
    public $subject = '';
    public $body = '';
    public $from_name = '';
    public $from_address = '';
    public $last_message_id = 0;
    public $message_id = 0;
    public $ctime = 0;
    public $udate = 0;
    public $date = '';
    public $priority = 0;
    public $mailbox_id = 0;
    public $total_messages = 0;
    public $match_rule_id = 0;
}

?>