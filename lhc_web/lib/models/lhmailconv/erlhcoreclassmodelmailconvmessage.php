<?php

class erLhcoreClassModelMailconvMessage
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_message';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'status' => $this->status,
            'conversation_id' => $this->conversation_id,

            'body' => $this->body,
            'alt_body' => $this->alt_body,

            'message_id' => $this->message_id,
            'in_reply_to' => $this->in_reply_to,
            'subject' => $this->subject,
            'references' => $this->references,

            'ctime' => $this->ctime,
            'date' => $this->date,
            'udate' => $this->udate,
            'from' => $this->from,
            'to' => $this->to,
            'draft' => $this->draft,
            'seen' => $this->seen,
            'deleted' => $this->deleted,
            'answered' => $this->answered,
            'flagged' => $this->flagged,
            'recent' => $this->recent,
            'msgno' => $this->msgno,
            'uid' => $this->uid,
            'size' => $this->size,

            'from_host' => $this->from_host,
            'from_name' => $this->from_name,
            'from_address' => $this->from_address,

            'sender_host' => $this->sender_host,
            'sender_name' => $this->sender_name,
            'sender_address' => $this->sender_address,

            'toaddress' => $this->toaddress,
            'fromaddress' => $this->fromaddress,
            'reply_toaddress' => $this->reply_toaddress,
            'senderaddress' => $this->senderaddress,

            'to_data' => $this->to_data,
            'from_data' => $this->from_data,
            'reply_to_data' => $this->reply_to_data,
            'sender_data' => $this->sender_data,

            // Recipients
        );
    }

    public function __toString()
    {
        return $this->subject;
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

            case 'msg_time_from':
                return date('Ymd') == date('Ymd', $this->msg_time) ? date(erLhcoreClassModule::$dateHourFormat, $this->msg_time) : date(erLhcoreClassModule::$dateDateHourFormat, $this->msg_time);
                break;

            default:
                ;
                break;
        }
    }

    public $id = NULL;
    public $status = 0;
    public $conversation_id = 0;
    public $message_id = '';
    public $in_reply_to = '';
    public $references = '';
    public $subject = '';
    public $body = '';
    public $alt_body = '';
    public $ctime = 0;
    public $date = '';
    public $udate = 0;
    public $from = '';
    public $to = '';
    public $size = 0;
    public $uid = 0;
    public $msgno = 0;
    public $recent = 0;
    public $flagged = 0;
    public $answered = 0;
    public $deleted = 0;
    public $seen = 0;
    public $draft = 0;

    public $from_host = '';
    public $from_name = '';
    public $from_address = '';

    public $sender_host = '';
    public $sender_name = '';
    public $sender_address = '';

    public $toaddress = '';
    public $fromaddress = '';
    public $reply_toaddress = '';
    public $senderaddress = '';

    public $to_data = '';
    public $from_data = '';
    public $reply_to_data = '';
    public $sender_data = '';

}

?>