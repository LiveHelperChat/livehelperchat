<?php

class erLhcoreClassModelMailconvMessage
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_msg';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'status' => $this->status,
            'conversation_id' => $this->conversation_id,
            'mailbox_id' => $this->mailbox_id,

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

            'to_data' => $this->to_data,
            'reply_to_data' => $this->reply_to_data,
            'cc_data' => $this->cc_data,
            'bcc_data' => $this->bcc_data,

            // These attributes in compare to conversation is set only once and never changes afterwards
            'response_time' => $this->response_time,
            'cls_time' => $this->cls_time,
            'wait_time' => $this->wait_time,
            'accept_time' => $this->accept_time,
            'interaction_time' => $this->interaction_time,
            'lr_time' => $this->lr_time,

            'user_id' => $this->user_id,
            'response_type' => $this->response_type,
            'dep_id' => $this->dep_id,
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

    public function beforeRemove()
    {
        $files = erLhcoreClassModelMailconvFile::getList(['filter' => ['message_id' => $this->id]]);

        foreach ($files as $file) {
            $file->removeThis();
        }
    }

    public function __get($var)
    {
        switch ($var) {

            case 'ctime_front':
            case 'udate_front':
            case 'accept_time_front':
            case 'cls_time_front':
            case 'lr_time_front':
                $varObj = str_replace('_front','',$var);
                $this->$var = date('Ymd') == date('Ymd', $this->{$varObj}) ? date(erLhcoreClassModule::$dateHourFormat, $this->{$varObj}) : date(erLhcoreClassModule::$dateDateHourFormat, $this->{$varObj});
                return $this->$var;

            case 'udate_ago':
                $varObj = str_replace('_ago','',$var);
                $this->$var = erLhcoreClassChat::formatSeconds(time() - $this->$varObj, true);
                break;
                
            case 'department':
                $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->department;

            case 'conversation':
                return $this->conversation = erLhcoreClassModelMailconvConversation::fetch($this->conversation_id);

            case 'body_front':
                if ($this->body != '') {

                    $body = $this->body;

                    foreach ($this->files as $file) {
                        if ($file->content_id != '') {
                            $body = str_replace('cid:' . $file->content_id,'https://' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('mailconv/inlinedownload') .'/' . $file->id, $body);
                        }
                    }

                    $this->body_front = erLhcoreClassMailconvHTMLParser::getHTMLPreview($body);

                } else {
                    $this->body_front = $this->alt_body;
                }
                return $this->body_front;

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

            case 'mailbox':
                $this->mailbox = null;
                if ($this->mailbox_id > 0) {
                        $this->mailbox = erLhcoreClassModelMailconvMailbox::fetch($this->mailbox_id, true);
                }
                return $this->mailbox;

            case 'plain_user_name':
                $this->plain_user_name = false;
                if ($this->user !== false) {
                    $this->plain_user_name = (string)$this->user->name_support;
                }
                return $this->plain_user_name;

            case 'wait_time_pending':
                $this->wait_time_pending = $this->wait_time > 0 ? erLhcoreClassChat::formatSeconds($this->wait_time) : erLhcoreClassChat::formatSeconds(time() - $this->ctime);
                return $this->wait_time_pending;

            case 'wait_time_response':
                $this->wait_time_response = $this->response_time > 0 ? erLhcoreClassChat::formatSeconds($this->response_time) : erLhcoreClassChat::formatSeconds(time() - $this->accept_time);
                return $this->wait_time_response;

            case 'interaction_time_duration':
                $this->interaction_time_duration = $this->interaction_time > 0 ? erLhcoreClassChat::formatSeconds($this->interaction_time) : null;
                return $this->interaction_time_duration;

            case 'files':
                $this->files = erLhcoreClassModelMailconvFile::getList(['filter' => ['message_id' => $this->id]]);
                return $this->files;

            case 'attachments':
                $this->attachments = [];
                foreach ($this->files as $file) {
                    if ($file->disposition == 'ATTACHMENT') {
                        if ($file->content_id == '' || !in_array($file->extension,['jpg','jpeg','png','bmp','gif']) || strpos($this->body,'cid:' . $file->content_id) === false) {
                            $this->attachments[] = [
                                'id' => $file->id,
                                'name' => $file->name,
                                'description' => $file->description,
                                'download_url' => erLhcoreClassDesign::baseurl('mailconv/inlinedownload') . '/' . $file->id,
                            ];
                        }
                    }
                }
                return $this->attachments;

            case 'subjects':
                $messageSubjects = erLhcoreClassModelMailconvMessageSubject::getList(['filter' => ['message_id' => $this->id]]);
                $ids = [];
                $this->subjects = [];
                foreach ($messageSubjects as $messageSubject) {
                    $ids[] = $messageSubject->subject_id;
                }
                if (!empty($ids)) {
                    $this->subjects = array_values(erLhAbstractModelSubject::getList(['filterin' => ['id' => $ids]]));
                }

                return $this->subjects;

            case 'to_data_front':
            case 'reply_to_data_front':
            case 'cc_data_front':
            case 'bcc_data_front':
                $varObj = str_replace('_front','',$var);
                $this->$var = '';
                $data = $this->$varObj;
                if ($data != '') {
                    $items = json_decode($data,true);
                    $itemsFormatted = [];
                    foreach ($items as $mail => $mailTitle) {
                        $itemsFormatted[] = trim($mailTitle . ' <' .$mail . '>');
                    }
                    $this->$var = implode(', ', $itemsFormatted);
                }
                return $this->$var;

            default:
                ;
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_RESPONDED = 2;

    public $id = NULL;
    public $status = self::STATUS_PENDING;
    public $mailbox_id = 0;
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

    public $to_data = '';
    public $reply_to_data = '';
    public $cc_data = '';
    public $bcc_data = '';

    // Logical attributes
    public $response_time = 0; // How long chat was in accepted state before it was responded.
    public $cls_time = 0; // Time conversation was closed.
    public $wait_time = 0; // how long chat was in pending before it was accepted. pnd_time - accept_time
    public $accept_time = 0; // Time when chat was accepted.
    public $interaction_time = 0; // Is time between the agent accepting a and closing e-chat.
    public $lr_time = 0;          // Last response time by operator
    public $user_id = 0; // User who has accepted
    public $dep_id = 0; // User who has accepted

    const RESPONSE_UNRESPONDED = 0;    // Normal response by sending mail back.
    const RESPONSE_NOT_REQUIRED = 1;   // Visitor just send thank you message.
    const RESPONSE_INTERNAL = 2;       // We have send this message
    const RESPONSE_NORMAL = 3;         // We have send this message

    public $response_type = self::RESPONSE_UNRESPONDED; // Normal mail based response

}

?>