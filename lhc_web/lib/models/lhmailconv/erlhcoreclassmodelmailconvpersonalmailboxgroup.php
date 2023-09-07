<?php
/**
 *
 * user_id - administartor user_id,
 * If 0 web user
 *
 * */
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvPersonalMailboxGroup
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_personal_mailbox_group';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'mails' => $this->mails,
            'active' => $this->active
        );

        return $stateArray;
    }

    public function __toString()
    {
        return (string)$this->name;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'mails_array':
                $this->mails_array = array();
                if ($this->mails != '') {
                    $this->mails_array = json_decode($this->mails, true);
                }
                return $this->mails_array;

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $name = '';
    public $mails = '';
    public $active = 0;
}

?>