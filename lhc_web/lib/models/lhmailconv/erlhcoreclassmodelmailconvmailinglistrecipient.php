<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvMailingListRecipient
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_mailing_list_recipient';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'mailing_list_id' => $this->mailing_list_id,
            'mailing_recipient_id' => $this->mailing_recipient_id
        );
    }

    public function __toString()
    {
        return $this->email;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return date('Ymd') == date('Ymd', $this->mtime) ? date(erLhcoreClassModule::$dateHourFormat, $this->mtime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->mtime);

            case 'mailing_recipient':
                $this->mailing_recipient = erLhcoreClassModelMailconvMailingRecipient::fetch($this->mailing_recipient_id);
                return $this->mailing_recipient;

            default:
                break;
        }
    }

    public $id = NULL;
    public $mailing_list_id = null;
    public $mailing_recipient_id = null;
}

?>