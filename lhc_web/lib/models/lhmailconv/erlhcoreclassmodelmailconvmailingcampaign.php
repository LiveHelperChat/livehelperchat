<?php

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
            'enabled' => $this->enabled
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
                break;

            default:
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_STARTED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_FINISHED = 3;

    public $id = NULL;
    public $name = '';
    public $user_id = 0;
    public $enabled = 0;
    public $status = self::STATUS_PENDING;
    public $starts_at = 0;

}

?>