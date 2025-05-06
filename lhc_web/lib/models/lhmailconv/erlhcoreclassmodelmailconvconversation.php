<?php
#[\AllowDynamicProperties]
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
            'from_address_clean' => $this->from_address_clean,
            'body' => $this->body,
            'ctime' => $this->ctime,
            'priority' => $this->priority,
            'priority_asc' => $this->priority_asc,
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
            'conv_duration' => $this->conv_duration,
            'mail_variables' => $this->mail_variables,
            'has_attachment' => $this->has_attachment,
            'follow_up_id' => $this->follow_up_id,
            'undelivered' => $this->undelivered,
            'pending_sync' => $this->pending_sync,
            'lang' => $this->lang,
            'opened_at' => $this->opened_at,
            'phone' => $this->phone,
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

        if ($this->from_address_clean == '' && $this->from_address != '') {
            $atPos = strrpos($this->from_address, "@");
            $name =  str_replace('.','',substr($this->from_address, 0, $atPos));
            $domain = substr($this->from_address, $atPos);
            $this->from_address_clean = strtolower($name . $domain);
        }
        
        // For reverse index
        $this->priority_asc = $this->priority * -1;
    }

    public function beforeUpdate()
    {
        $this->priority_asc = $this->priority * -1;
    }

    public function afterSave($params = array())
    {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation.after_save',array(
            'conversation' => & $this
        ));
    }

    public function afterUpdate($params = array())
    {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation.after_update',array(
            'conversation' => & $this
        ));
    }

    public function afterRemove() {
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation.after_remove',array(
            'conversation' => & $this
        ));

        erLhcoreClassChat::updateActiveChats($this->user_id);

        if ($this->department !== false) {
            erLhcoreClassChat::updateDepartmentStats($this->department);
        }
    }

    public function beforeRemove()
    {
        $messages = $this->is_archive === false ? erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $this->id]]) : \LiveHelperChat\Models\mailConv\Archive\Message::getList(['filter' => ['conversation_id' => $this->id]]);

        foreach ($messages as $message) {
            $message->ignore_imap = $this->ignore_imap;
            $message->removeThis();
        }

        $messagesInternal = $this->is_archive === false ? erLhcoreClassModelMailconvMessageInternal::getList(['filter' => ['chat_id' => $this->id]]) : \LiveHelperChat\Models\mailConv\Archive\MessageInternal::getList(['filter' => ['chat_id' => $this->id]]);

        foreach ($messagesInternal as $messageInternal) {
            $messageInternal->removeThis();
        }

        $db = ezcDbInstance::get();
        $q = $db->createDeleteQuery();
        $q->deleteFrom("lh_transfer")->where(
            $q->expr->eq( 'chat_id', $this->id ),
            $q->expr->eq( 'transfer_scope', 1 )
        );
        $stmt = $q->prepare();
        $stmt->execute();
    }

    public function __get($var)
    {
        switch ($var) {

            case 'opened_at_front':
            case 'pnd_time_front':
            case 'ctime_front':
            case 'udate_front':
            case 'accept_time_front':
            case 'cls_time_front':
            case 'lr_time_front':
                $varObj = str_replace('_front','',$var);
                $this->$var = date('Ymd') == date('Ymd', $this->{$varObj}) ? date(erLhcoreClassModule::$dateHourFormat, $this->{$varObj}) : date(erLhcoreClassModule::$dateFormat, $this->{$varObj});
                return $this->$var;

            case 'pnd_time_front_ago':
            case 'ctime_front_ago':
            case 'udate_front_ago':
            case 'accept_time_front_ago':
            case 'cls_time_front_ago':
            case 'lr_time_front_ago':
                $varObj = str_replace('_front_ago','',$var);
                $this->$var = erLhcoreClassChat::formatSeconds(time() - $this->{$varObj});
                return $this->$var;

            case 'department':
                $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->department;

            case 'mailbox_front':
                $this->mailbox_front = [
                    'name' => '',
                    'mail' => '',
                ];

                if ($this->mailbox instanceof erLhcoreClassModelMailconvMailbox) {
                    $this->mailbox_front['name'] = $this->mailbox->name;
                    $this->mailbox_front['mail'] = $this->mailbox->mail;
                }

                return $this->mailbox_front;

            case 'mailbox':
                $this->mailbox = erLhcoreClassModelMailconvMailbox::fetch($this->mailbox_id);
                return $this->mailbox;

            case 'subject_front':
                $this->subject_front = $this->subject != '' ? $this->subject : ($this->from_name != '' ? $this->from_name : $this->id.' '.$this->from_address);
                return $this->subject_front;

            case 'can_delete':
                $this->can_delete = erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','delete_conversation');
                return $this->can_delete;

            case 'department_name':
                return $this->department_name = (string)$this->department;

            case 'wait_time_pending':
                $this->wait_time_pending = $this->wait_time > 0 ? erLhcoreClassChat::formatSeconds($this->wait_time) : ($this->status !== self::STATUS_CLOSED ? erLhcoreClassChat::formatSeconds(time() - $this->pnd_time) : 0);
                return $this->wait_time_pending;

            case 'wait_time_response':
                $this->wait_time_response = $this->response_time > 0 ? erLhcoreClassChat::formatSeconds($this->response_time) : ($this->status !== self::STATUS_CLOSED ? erLhcoreClassChat::formatSeconds(time() - $this->accept_time) : 0);
                return $this->wait_time_response;

            case 'conv_duration_front':
                $this->conv_duration_front = $this->conv_duration > 0 ? erLhcoreClassChat::formatSeconds($this->conv_duration) : 0;
                return $this->conv_duration_front;

            case 'last_mail_front':
                $this->last_mail_front = erLhcoreClassChat::formatSeconds(time() - $this->udate);
                return $this->last_mail_front;

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

            case 'customer_email':
                $this->customer_email = '';
                if ($this->mailbox instanceof erLhcoreClassModelMailconvMailbox&& $this->from_address == $this->mailbox->mail) {
                    $message = $this->is_archive === false ? erLhcoreClassModelMailconvMessage::fetch($this->message_id) : \LiveHelperChat\Models\mailConv\Archive\Message::fetch($this->message_id);
                    if (!($message instanceof erLhcoreClassModelMailconvMessage)) {
                        $message = $this->is_archive === false ? erLhcoreClassModelMailconvMessage::fetch($this->last_message_id) : \LiveHelperChat\Models\mailConv\Archive\Message::fetch($this->last_message_id);
                    }
                    if ($message instanceof erLhcoreClassModelMailconvMessage) {
                        foreach ($message->to_data_array as $toData) {
                            $this->customer_email = $toData['email'];
                            break;
                        }
                    }
                } else {
                    $this->customer_email = $this->from_address;
                }
                return $this->customer_email;

            case 'chat_variables_array':
            case 'mail_variables_array':
                if (!empty($this->mail_variables)) {
                    $jsonData = json_decode($this->mail_variables,true);
                    if ($jsonData !== null) {
                        $this->mail_variables_array = $jsonData;
                    } else {
                        $this->mail_variables_array = $this->mail_variables;
                    }
                } else {
                    $this->mail_variables_array = array();
                }
                return $this->mail_variables_array;

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

    const ATTACHMENT_EMPTY = 0;
    const ATTACHMENT_INLINE = 1;
    const ATTACHMENT_FILE = 2;
    const ATTACHMENT_MIX = 3;

    public $id = NULL;
    public $dep_id = null;
    public $user_id = 0;
    public $status = 0;

    public $start_type = self::START_IN;

    public $subject = '';
    public $body = '';
    public $from_name = '';
    public $from_address = '';
    public $from_address_clean = '';
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
    public $mail_variables = '';
    public $priority = 0;
    public $priority_asc = 0;
    public $mailbox_id = 0;
    public $total_messages = 0;
    public $match_rule_id = 0;
    public $follow_up_id = 0;
    public $undelivered = 0;
    public $pending_sync = 0;

    // Assignment workflow attribute
    public $tslasign = 0;       // Time when last auto assignment happened
    public $cls_time = 0;         // Close time when conversation was closed.
    public $pnd_time = 0;         // Time when mail became pending
    public $wait_time = 0;        // How long chat was in pending before it was accepted. accept_time - pnd_time
    public $accept_time = 0;      // Time when chat was accepted.
    public $response_time = 0;    // How long chat was in active state before it was responded.
    public $lr_time = 0;          // Last response time by operator
    public $interaction_time = 0; // is time between the agent accepting a and closing e-chat.
    public $conv_duration = 0;         // Total time spend between durations in the message
    public $has_attachment = self::ATTACHMENT_EMPTY;
    public $lang = '';
    public $opened_at = 0;
    public $phone = '';
    public $is_archive = false;
    public $archive = null;
    public $ignore_imap = false;
}

?>