<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvMailingCampaignRecipient
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_mailing_campaign_recipient';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'recipient_id' => $this->recipient_id,
            'type' => $this->type,
            'email' => $this->email,
            'mailbox' => $this->mailbox,
            'status' => $this->status,
            'send_at' => $this->send_at,
            'opened_at' => $this->opened_at,
            'name' => $this->name,
            'attr_str_1' => $this->attr_str_1,
            'attr_str_2' => $this->attr_str_2,
            'attr_str_3' => $this->attr_str_3,
            'attr_str_4' => $this->attr_str_4,
            'attr_str_5' => $this->attr_str_5,
            'attr_str_6' => $this->attr_str_6,
            'message_id' => $this->message_id,
            'conversation_id' => $this->conversation_id,
            'log' => $this->log,
        );
    }

    public function __toString()
    {
        return $this->mail;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'send_at_front':
            case 'opened_at_front':
                $varObj = str_replace('_front','',$var);
                $this->$var = date('Ymd') == date('Ymd', $this->{$varObj}) ? date(erLhcoreClassModule::$dateHourFormat, $this->{$varObj}) : date(erLhcoreClassModule::$dateFormat, $this->{$varObj});
                return $this->$var;

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                }
                return $this->user;

            case 'mailbox_front':
                $this->mailbox_front = '';
                if ($this->type == self::TYPE_MANUAL) {
                    $this->mailbox_front = $this->mailbox;
                } else {
                    $recipient = erLhcoreClassModelMailconvMailingRecipient::fetch($this->recipient_id);
                    if ($recipient instanceof erLhcoreClassModelMailconvMailingRecipient) {
                        $this->mailbox_front = $recipient->mailbox;
                    }
                }
                return $this->mailbox_front;

            case 'recipient':
                $this->recipient = '';
                if ($this->type == self::TYPE_MANUAL) {
                    $this->recipient = $this->email;
                } else {
                    $recipient = erLhcoreClassModelMailconvMailingRecipient::fetch($this->recipient_id);
                    if ($recipient instanceof erLhcoreClassModelMailconvMailingRecipient) {
                        $this->recipient = $recipient->email;
                    }
                }
                return $this->recipient;

            case 'recipient_attr_str_1':
            case 'recipient_attr_str_2':
            case 'recipient_attr_str_3':
            case 'recipient_attr_str_4':
            case 'recipient_attr_str_5':
            case 'recipient_attr_str_6':
            case 'recipient_name':
                $this->{$var} = '';
                $systemAttr = str_replace('recipient_','',$var);
                if ($this->type == self::TYPE_MANUAL) {
                    $this->{$var} = $this->{$systemAttr};
                } else {
                    $recipient = erLhcoreClassModelMailconvMailingRecipient::fetch($this->recipient_id);
                    if ($recipient instanceof erLhcoreClassModelMailconvMailingRecipient) {
                        $this->{$var} = $recipient->{$systemAttr};
                    }
                }
                return $this->{$var};

            default:
                break;
        }
    }

    const TYPE_MAILING_LIST = 0;
    const TYPE_MANUAL = 1;

    const PENDING = 0;
    const IN_PROGRESS = 1;
    const SEND = 2;
    const FAILED = 3;

    public $id = NULL;
    public $campaign_id = 0;
    public $recipient_id = 0;
    public $send_at = 0;
    public $type = self::TYPE_MAILING_LIST;
    public $email = '';
    public $mailbox = '';
    public $status = self::PENDING;
    public $log = '';
    public $message_id = 0;
    public $conversation_id = 0;
    public $opened_at = 0;

    public $name = '';
    public $attr_str_1 = '';
    public $attr_str_2 = '';
    public $attr_str_3 = '';
    public $attr_str_4 = '';
    public $attr_str_5 = '';
    public $attr_str_6 = '';
}

?>