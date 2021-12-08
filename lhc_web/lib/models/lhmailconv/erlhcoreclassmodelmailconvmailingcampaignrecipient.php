<?php

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
            'status' => $this->status,
            'send_at' => $this->send_at,
            'name' => $this->name,
            'attr_str_1' => $this->attr_str_1,
            'attr_str_2' => $this->attr_str_2,
            'attr_str_3' => $this->attr_str_3,
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
                return date('Ymd') == date('Ymd', $this->send_at) ? date(erLhcoreClassModule::$dateHourFormat, $this->send_at) : date(erLhcoreClassModule::$dateDateHourFormat, $this->send_at);

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                }
                return $this->user;
                break;

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
            case 'recipient_name':
                $this->{$var} = '';
                $systemAttr = str_replace('recipient_','',$var);
                if ($this->type == self::TYPE_MANUAL) {
                    $this->recipient_name = $this->{$systemAttr};
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
    public $status = self::PENDING;
    public $log = '';

    public $name = '';
    public $attr_str_1 = '';
    public $attr_str_2 = '';
    public $attr_str_3 = '';
}

?>