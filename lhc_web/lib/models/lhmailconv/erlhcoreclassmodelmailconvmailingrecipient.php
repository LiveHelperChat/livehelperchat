<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvMailingRecipient
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_recipient';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'data' => $this->data,
            'email' => $this->email,
            'mailbox' => $this->mailbox,
            'disabled' => $this->disabled,
            'name' => $this->name,
            'attr_str_1' => $this->attr_str_1,
            'attr_str_2' => $this->attr_str_2,
            'attr_str_3' => $this->attr_str_3,
            'attr_str_4' => $this->attr_str_4,
            'attr_str_5' => $this->attr_str_5,
            'attr_str_6' => $this->attr_str_6,
        );
    }

    public function removeAssignment(){
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_mailconv_mailing_list_recipient` WHERE `mailing_recipient_id` = :ml_id');
        $stmt->bindValue(':ml_id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function afterRemove()
    {
        $this->removeAssignment();

        // Remove old assignment as recipient is removed
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_mailconv_mailing_campaign_recipient` WHERE `recipient_id` = :ml_id');
        $stmt->bindValue(':ml_id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function afterSave()
    {
        $this->removeAssignment();

        $db = ezcDbInstance::get();

        if (isset($this->ml_ids) && !empty($this->ml_ids)) {
            $values = [];
            foreach ($this->ml_ids as $ml_id) {
                $values[] = "(" . $this->id . "," . $ml_id . ")";
            }
            if (!empty($values)) {
                $db->query('INSERT INTO `lhc_mailconv_mailing_list_recipient` (`mailing_recipient_id`,`mailing_list_id`) VALUES ' . implode(',', $values));
            }
        }
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

            case 'ml':
                $this->ml = erLhcoreClassModelMailconvMailingListRecipient::getList(['filter' => ['mailing_recipient_id' => $this->id]]);
                return $this->ml;

            case 'ml_ids_front':
                $this->ml_ids_front = [];
                foreach ($this->ml as $ml) {
                    $this->ml_ids_front[] = $ml->mailing_list_id;
                }
                return $this->ml_ids_front;

            default:
                break;
        }
    }

    public $id = NULL;
    public $data = '';
    public $email = '';
    public $mailbox = '';
    public $disabled = 0;
    public $ml_ids = [];
    
    public $name = '';
    public $attr_str_1 = '';
    public $attr_str_2 = '';
    public $attr_str_3 = '';
    public $attr_str_4 = '';
    public $attr_str_5 = '';
    public $attr_str_6 = '';

}

?>