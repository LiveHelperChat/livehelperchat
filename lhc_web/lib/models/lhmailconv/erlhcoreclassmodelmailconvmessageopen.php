<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvMessageOpen
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_msg_open';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'opened_at' => $this->opened_at,
            'hash' => $this->hash
        );
    }

    public function __get($var)
    {

        switch ($var) {
            case 'opened_at_front':
                if (date('Ymd') == date('Ymd', $this->opened_at)) {
                    $this->opened_at_front = date(erLhcoreClassModule::$dateHourFormat, $this->opened_at);
                } else {
                    $this->opened_at_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->opened_at);
                }
                return $this->opened_at_front;

            default:
                break;
        }
    }

    public $id = null;
    public $opened_at = '';
    public $hash = null;
}

?>