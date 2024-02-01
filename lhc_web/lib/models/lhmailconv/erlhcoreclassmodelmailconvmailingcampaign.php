<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvMailingCampaign
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_mailing_campaign';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'starts_at' => $this->starts_at,
            'enabled' => $this->enabled,
            'mailbox_id' => $this->mailbox_id,
            'body' => $this->body,
            'body_alt' => $this->body_alt,
            'subject' => $this->subject,
            'as_active' => $this->as_active,
            'reply_email' => $this->reply_email,
            'reply_name' => $this->reply_name,
            'owner_logic' => $this->owner_logic,
            'owner_user_id' => $this->owner_user_id,
        );
    }

    public function afterRemove()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_mailconv_mailing_campaign_recipient` WHERE `campaign_id` = :id');
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function __toString()
    {
        return $this->mail;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return date('Ymd') == date('Ymd', $this->mtime) ? date(erLhcoreClassModule::$dateHourFormat, $this->mtime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->mtime);

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                }
                return $this->user;

            case 'mailbox':
                $this->mailbox = null;
                if ($this->mailbox_id > 0) {
                    $this->mailbox = erLhcoreClassModelMailconvMailbox::fetch($this->mailbox_id);
                }
                return $this->mailbox;

            case 'mailbox_front':
                $this->mailbox_front = (string)$this->mailbox;
                return $this->mailbox_front;

            default:
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED = 2;

    const OWNER_CREATOR = 0;
    const OWNER_DEFAULT = 1;
    const OWNER_USER = 2;

    public $id = NULL;
    public $name = '';
    public $user_id = 0;
    public $enabled = 0;
    public $status = self::STATUS_PENDING;
    public $starts_at = 0;
    public $mailbox_id = 0;
    public $body = '';
    public $body_alt = '';
    public $owner_logic = self::OWNER_CREATOR;
    public $owner_user_id = 0;

    public $subject = '';
    public $as_active = 0;
    public $reply_email = '';
    public $reply_name = '';
}

?>