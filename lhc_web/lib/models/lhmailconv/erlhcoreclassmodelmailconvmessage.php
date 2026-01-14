<?php
#[\AllowDynamicProperties]
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
            'conversation_id_old' => $this->conversation_id_old,
            'mailbox_id' => $this->mailbox_id,

            'body' => (string)$this->body,
            'alt_body' => (string)$this->alt_body,

            'message_id' => $this->message_id,
            'in_reply_to' => $this->in_reply_to,
            'subject' => $this->subject,
            'references' => $this->references,

            'ctime' => $this->ctime,
            'date' => $this->date,
            'udate' => $this->udate,

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
            'conv_duration' => $this->conv_duration,

            'user_id' => $this->user_id,
            'conv_user_id' => $this->conv_user_id,
            'response_type' => $this->response_type,
            'dep_id' => $this->dep_id,
            'mb_folder' => $this->mb_folder,
            'has_attachment' => $this->has_attachment,
            'rfc822_body' => $this->rfc822_body,
            'delivery_status' => $this->delivery_status,
            'undelivered' => $this->undelivered,
            'priority' => $this->priority,
            'lang' => $this->lang,
            'message_hash' => $this->message_hash,
            'opened_at' => $this->opened_at,
            'is_external' => $this->is_external,
            'auto_submitted' => $this->auto_submitted,
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

        if ($this->message_hash == '' && $this->message_id != ''){
            $this->message_hash = sha1($this->message_id);
        }
    }

    public function afterSave($params = array())
    {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.message.after_save',array(
            'message' => & $this
        ));
    }

    public function afterUpdate($params = array())
    {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.message.after_update',array(
            'message' => & $this
        ));
    }

    public function afterRemove() {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.message.after_remove',array(
            'message' => & $this
        ));
    }

    public function beforeRemove()
    {

        // Files
        $files = $this->is_archive === false ? erLhcoreClassModelMailconvFile::getList(['filter' => ['message_id' => $this->id]]) : \LiveHelperChat\Models\mailConv\Archive\File::getList(['filter' => ['message_id' => $this->id]]);

        foreach ($files as $file) {
            $file->removeThis();
        }

        // Message subjects
        $messageSubjects = $this->is_archive === false ? erLhcoreClassModelMailconvMessageSubject::getList(['filter' => ['message_id' => $this->id]]) : \LiveHelperChat\Models\mailConv\Archive\MessageSubject::getList(['filter' => ['message_id' => $this->id]]);

        foreach ($messageSubjects as $messageSubject) {
            $messageSubject->removeThis();
        }

        if ($this->ignore_imap === false) {
            erLhcoreClassMailconvParser::purgeMessage($this);
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
            case 'opened_at_front':
                $varObj = str_replace('_front','',$var);
                $value = $this->{$varObj};
                if ($value > 0) {
                    $this->$var = date('Ymd') == date('Ymd', $value) ? date(erLhcoreClassModule::$dateHourFormat, $this->{$varObj}) : date(erLhcoreClassModule::$dateDateHourFormat, $this->{$varObj});
                } else {
                    $this->$var = null;
                }
                return $this->$var;

            case 'udate_ago':
                $varObj = str_replace('_ago','',$var);
                $this->$var = erLhcoreClassChat::formatSeconds(time() - $this->$varObj, true);
                break;
                
            case 'department':
                $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->department;

            case 'conversation':
                return $this->conversation = $this->is_archive === false ? erLhcoreClassModelMailconvConversation::fetch($this->conversation_id) : \LiveHelperChat\Models\mailConv\Archive\Conversation::fetch($this->conversation_id);
                
            case 'conv_duration_front':
                $this->conv_duration_front = $this->conv_duration > 0 ? erLhcoreClassChat::formatSeconds($this->conv_duration) : 0;
                return $this->conv_duration_front;

            case 'body_front':
                if ($this->body != '') {

                    $body = $this->body;

                    foreach ($this->files as $file) {
                        if ($file->content_id != '') {
                            $body = str_replace('cid:' . $file->content_id,erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurl('mailconv/inlinedownload') .'/' . $file->id . '/' . $this->conversation_id, $body);
                        }
                    }

                    $this->body_front = erLhcoreClassMailconvHTMLParser::getHTMLPreview($body);

                } else {
                    $this->body_front = nl2br(htmlspecialchars($this->alt_body));
                }
                return $this->body_front;

            case 'body_subject_index':
                $this->body_subject_index = $this->subject . ' '. strip_tags($this->body) .' '. $this->alt_body;
                return $this->body_subject_index;

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
                $this->files = $this->is_archive === false ? erLhcoreClassModelMailconvFile::getList(['filter' => ['message_id' => $this->id]]) : \LiveHelperChat\Models\mailConv\Archive\File::getList(['filter' => ['message_id' => $this->id]]);
                return $this->files;

            case 'attachments':
                $this->attachments = [];
                foreach ($this->files as $file) {
                    if (strtolower($file->disposition) == 'attachment' || (strtolower($file->disposition) == 'inline' && ($file->content_id == '' || strpos($this->body,'cid:' . $file->content_id) === false))) {
                        if ($file->content_id == '' || !in_array($file->extension,erLhcoreClassMailconvParser::IMAGE_EXTENSIONS) || strpos($this->body,'cid:' . $file->content_id) === false) {

                            // Determine if file is restricted based on filesResctrictions logic
                            $restricted_file = false;
                            $restricted_reason = 0;
                            if (!empty($this->filesResctrictions)) {
                                $fileExtension = strtolower($file->extension);
                                
                                // If file extension is in allowed_extensions_public, it should always be downloadable
                                if (in_array($fileExtension, $this->filesResctrictions['allowed_extensions_public'])) {
                                    $restricted_file = false;
                                }
                                // Else if file extension is in allowed_extensions_restricted, check if user has download_restricted permission
                                elseif (in_array($fileExtension, $this->filesResctrictions['allowed_extensions_restricted'])) {
                                    $restricted_file = !(isset($this->filesResctrictions['download_restricted']) && $this->filesResctrictions['download_restricted'] === true);
                                    if ($restricted_file) {
                                        $restricted_reason = 1; // Restricted due to download_restricted permission
                                    }
                                }
                                // Else file is denied to be downloaded
                                else {
                                    $restricted_file = true;
                                    $restricted_reason = 2; // Restricted due to file extension not allowed
                                }
                            }

                            if ($file->extension == 'octet-stre') {
                                $extension = erLhcoreClassChatWebhookIncoming::getExtensionByMime(trim(explode(';',$file->type)[0]));
                                if (!empty($extension)) {
                                    $file->extension = $extension;
                                    $file->updateThis(['update' => ['extension']]);
                                }
                            }

                            if (isset($this->filesResctrictions['check_suspicious_pdf']) && $this->filesResctrictions['check_suspicious_pdf'] == 1 && $restricted_file == false && $file->extension == 'pdf' && !\LiveHelperChat\mailConv\helpers\ValidationHelper::isValidPDF($file->file_path_server) && !(isset($this->filesResctrictions['download_restricted']) && $this->filesResctrictions['download_restricted'] === true)) {
                                 $restricted_reason = 1;
                                 $restricted_file = true;
                            }

                            $this->attachments[] = [
                                'id' => $file->id,
                                'name' => $file->name,
                                'description' => $file->description,
                                'download_url' => erLhcoreClassDesign::baseurl('mailconv/inlinedownload') . '/' . $file->id . '/' . $this->conversation_id,
                                'is_image' => in_array($file->extension, erLhcoreClassMailconvParser::IMAGE_EXTENSIONS),
                                'restricted_file' => $restricted_file,
                                'restricted_reason' => $restricted_reason
                            ];


                        }
                    }
                }
                return $this->attachments;

            case 'subjects':
                $messageSubjects = $this->is_archive === false ? erLhcoreClassModelMailconvMessageSubject::getList(['filter' => ['message_id' => $this->id]]) : \LiveHelperChat\Models\mailConv\Archive\MessageSubject::getList(['filter' => ['message_id' => $this->id]]);
                $ids = [];
                $this->subjects = [];
                foreach ($messageSubjects as $messageSubject) {
                    $ids[] = $messageSubject->subject_id;
                }
                if (!empty($ids)) {
                    $this->subjects = array_values(erLhAbstractModelSubject::getList(['filterin' => ['id' => $ids]]));
                }

                return $this->subjects;

            case 'to_data_array':
            case 'reply_to_data_array':
            case 'cc_data_array':
            case 'bcc_data_array':
                $varObj = str_replace('_array','',$var);
                $this->$var = [];
                $data = $this->$varObj;
                if ($data != '') {
                    $items = json_decode($data,true);
                    $itemsFormatted = [];
                    foreach ($items as $mail => $mailTitle) {
                        if ($this->sensitive === true) {
                            if ($this->response_type == self::RESPONSE_INTERNAL) {
                                if ($varObj == 'to_data') {
                                    $mail = \LiveHelperChat\Helpers\Anonymizer::maskEmail($mail);
                                }
                            } elseif ($varObj == 'reply_to_data') {
                                $mail = \LiveHelperChat\Helpers\Anonymizer::maskEmail($mail);
                            }
                        }
                        $itemsFormatted[] = ['email' => $mail, 'name' => $mailTitle];
                    }
                    $this->$var = $itemsFormatted;
                }
                return $this->$var;

            case 'delivery_status_keyed':
            case 'to_data_keyed':
            case 'reply_to_data_keyed':
            case 'cc_data_keyed':
            case 'bcc_data_keyed':
                $varObj = str_replace('_keyed','',$var);
                $this->$var = [];
                $data = $this->$varObj;
                if ($data != '') {
                    $this->$var = json_decode($data,true);
                }
                return $this->$var;

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

                        if ($this->sensitive === true) {
                            if ($this->response_type == self::RESPONSE_INTERNAL) {
                                if ($varObj == 'to_data') {
                                    $mail = \LiveHelperChat\Helpers\Anonymizer::maskEmail($mail);
                                }
                            } elseif ($varObj == 'reply_to_data') {
                                if ($mail == $mailTitle) {
                                    $mailTitle = \LiveHelperChat\Helpers\Anonymizer::maskEmail($mailTitle);
                                }
                                $mail = \LiveHelperChat\Helpers\Anonymizer::maskEmail($mail);
                            }
                        }

                        $itemsFormatted[] = trim($mailTitle . ' <' . $mail . '>');
                    }
                    $this->$var = implode(', ', $itemsFormatted);
                }
                return $this->$var;

            default:
                ;
                break;
        }
    }

    public function setSensitive($sensitive) {
        $this->sensitive = $sensitive;

        if ($this->response_type !== self::RESPONSE_INTERNAL && $this->from_name == $this->from_address) {
            $this->from_name = \LiveHelperChat\Helpers\Anonymizer::maskEmail($this->from_name);
        }
    }

    public function setAttachementsRestrictions($filesResctrictions = []){

        $this->filesResctrictions['download_restricted'] = $filesResctrictions['download_restricted'];

        if (isset($filesResctrictions['allowed_extensions_public']) && $filesResctrictions['allowed_extensions_public'] != '') {
            $this->filesResctrictions['allowed_extensions_public'] = explode('|', $filesResctrictions['allowed_extensions_public']);
        } else {
            $this->filesResctrictions['allowed_extensions_public'] = [];
        }

        if (isset($filesResctrictions['allowed_extensions_restricted']) && $filesResctrictions['allowed_extensions_restricted'] != '') {
            $this->filesResctrictions['allowed_extensions_restricted'] = explode('|', $filesResctrictions['allowed_extensions_restricted']);
        } else {
            $this->filesResctrictions['allowed_extensions_restricted'] = [];
        }

        if (isset($filesResctrictions['check_suspicious_pdf'])) {
            $this->filesResctrictions['check_suspicious_pdf'] = $filesResctrictions['check_suspicious_pdf'];
        } else {
            $this->filesResctrictions['check_suspicious_pdf'] = false;
        }

        return $this->filesResctrictions;
    }

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_RESPONDED = 2;

    const ATTACHMENT_EMPTY = 0;
    const ATTACHMENT_INLINE = 1;
    const ATTACHMENT_FILE = 2;
    const ATTACHMENT_MIX = 3;

    private $sensitive = false;

    public $id = NULL;
    public $status = self::STATUS_PENDING;
    public $mailbox_id = 0;
    public $is_external = 0;
    public $conversation_id = 0;
    public $conversation_id_old = 0;
    public $message_id = '';
    public $in_reply_to = '';
    public $references = '';
    public $subject = '';
    public $body = '';
    public $alt_body = '';
    public $ctime = 0;
    public $date = '';
    public $udate = 0;
    public $size = 0;
    public $uid = 0;
    public $msgno = 0;
    public $recent = 0;
    public $flagged = 0;

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

    // Mailbox folder. We need to know it while moving or deleting mail.
    public $mb_folder = '';

    // Logical attributes
    public $response_time = 0; // How long chat was in accepted state before it was responded.

    public $cls_time = 0; // Time conversation was closed.
    public $wait_time = 0; // how long chat was in pending before it was accepted. pnd_time - accept_time

    // Time when chat was accepted.
    // During sync our send messages get's accept_time as soon they were received
    // Accept time is the one operator opens in
    public $accept_time = 0;

    // Is time between the agent accepting a and closing e-chat.
    public $interaction_time = 0;

    // How long visitor had to wait for an answer
    // Raw time un-till response was send
    public $conv_duration = 0;

    public $lr_time = 0;          // Last response time by operator. When was the last message send based on this message
    public $user_id = 0; // User who has accepted
    public $conv_user_id = 0; // Conversation owner
    public $dep_id = 0; // User who has accepted

    const RESPONSE_UNRESPONDED = 0;    // Normal response by sending mail back.
    const RESPONSE_NOT_REQUIRED = 1;   // Visitor just send thank you message.
    const RESPONSE_INTERNAL = 2;       // We have send this message as reply or forward
    const RESPONSE_NORMAL = 3;         // To this message was responded by us.

    public $response_type = self::RESPONSE_UNRESPONDED; // Normal mail based response
    public $has_attachment = self::ATTACHMENT_EMPTY;


    const AUTO_SUBMITTED_NONE = 0;
    const AUTO_SUBMITTED_REPLIED = 1;
    const AUTO_SUBMITTED_GENERATED = 2;

    public $auto_submitted = self::AUTO_SUBMITTED_NONE;

    public $rfc822_body = '';
    public $delivery_status  = '';
    public $undelivered  = 0;
    public $priority  = 0;
    public $lang  = '';
    public $message_hash  = '';
    public $opened_at  = 0;
    public $is_archive = false;
    public $ignore_imap = false;
    public $filesResctrictions = [];
}

?>