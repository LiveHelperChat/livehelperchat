<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvMailingList
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_mailing_list';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id
        );
    }

    public function afterRemove()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_mailconv_mailing_list_recipient` WHERE `mailing_list_id` = :ml_id');
        $stmt->bindValue(':ml_id', $this->id, PDO::PARAM_INT);
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

    public $id = NULL;
    public $name = '';
    public $user_id = 0;
}

?>