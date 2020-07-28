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

            'cls_time' => $this->cls_time,
            'pnd_time' => $this->pnd_time,
            'wait_time' => $this->wait_time,
            'accept_time' => $this->accept_time,
            'response_time' => $this->response_time,
            'interaction_time' => $this->interaction_time,
            'lr_time' => $this->lr_time,
            'tslasign' => $this->tslasign,
            'start_type' => $this->start_type,
            'transfer_uid' => $this->transfer_uid,
            'remarks' => $this->remarks,
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

        if ($this->id > 0) {
            $this->total_messages = erLhcoreClassModelMailconvMessage::getCount(['filter' => ['conversation_id' => $this->id]]);
        }
    }

    public function beforeRemove()
    {
        $messages = erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $this->id]]);

        foreach ($messages as $message) {
            $message->removeThis();
        }
    }

    public function __get($var)
    {
        switch ($var) {

            case 'pnd_time_front':
            case 'ctime_front':
            case 'udate_front':
            case 'accept_time_front':
            case 'cls_time_front':
            case 'lr_time_front':
                $varObj = str_replace('_front','',$var);
                $this->$var = date('Ymd') == date('Ymd', $this->{$varObj}) ? date(erLhcoreClassModule::$dateHourFormat, $this->{$varObj}) : date(erLhcoreClassModule::$dateDateHourFormat, $this->{$varObj});
                return $this->$var;

            case 'department':
                $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->department;

            case 'department_name':
                return $this->department_name = (string)$this->department;

            case 'wait_time_pending':
                $this->wait_time_pending = $this->wait_time > 0 ? erLhcoreClassChat::formatSeconds($this->wait_time) : erLhcoreClassChat::formatSeconds(time() - $this->pnd_time);
                return $this->wait_time_pending;

            case 'wait_time_response':
                $this->wait_time_response = $this->response_time > 0 ? erLhcoreClassChat::formatSeconds($this->response_time) : erLhcoreClassChat::formatSeconds(time() - $this->accept_time);
                return $this->wait_time_response;

            case 'user':
                $this->user = false;
                if ($this->user_id > 0) {
                    try {
                        $this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
                    } catch (Exception $e) {
                        $this->user = false;
                    }
                }
                return $this->user;

            case 'plain_user_name':
                $this->plain_user_name = false;
                if ($this->user !== false) {
                    $this->plain_user_name = (string)$this->user->name_support;
                }
                return $this->plain_user_name;

            case 'interaction_time_duration':
                $this->interaction_time_duration = $this->interaction_time > 0 ? erLhcoreClassChat::formatSeconds($this->interaction_time) : null;
                return $this->interaction_time_duration;

            default:
                ;
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CLOSED = 2;

    const START_IN = 0;
    const START_OUT = 1;

    public $id = NULL;
    public $dep_id = null;
    public $user_id = 0;
    public $status = 0;

    public $start_type = self::START_IN;

    public $subject = '';
    public $body = '';
    public $from_name = '';
    public $from_address = '';
    public $remarks = '';
    public $last_message_id = 0;
    public $message_id = 0;
    // Create record time
    public $ctime = 0;

    // Mail time from mail server
    public $udate = 0;

    // User who transfered a chat
    public $transfer_uid = 0;

    // Date
    public $date = '';
    public $priority = 0;
    public $mailbox_id = 0;
    public $total_messages = 0;
    public $match_rule_id = 0;

    // Assignment workflow attribute
    public $tslasign = 0;       // Time when last auto assignment happened
    public $cls_time = 0;         // Close time when conversation was closed.
    public $pnd_time = 0;         // Time when mail became pending
    public $wait_time = 0;        // How long chat was in pending before it was accepted. accept_time - pnd_time
    public $accept_time = 0;      // Time when chat was accepted.
    public $response_time = 0;    // How long chat was in active state before it was responded.
    public $lr_time = 0;          // Last response time by operator
    public $interaction_time = 0; // is time between the agent accepting a and closing e-chat.
}

?>