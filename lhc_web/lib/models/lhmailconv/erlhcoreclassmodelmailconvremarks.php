<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvRemarks
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_remarks';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'email' => $this->email,
            'remarks' => $this->remarks
        );
    }

    public static function getInstance($emailSender, $create = false)
    {
        $email = self::findOne(array('filter' => array('email' => $emailSender)));
        if (!($email instanceof erLhcoreClassModelMailconvRemarks)) {
            $email = new self();
            $email->email = $emailSender;
            if ($create == true) {
                $email->saveThis();
            }
        }

        return $email;
    }

    public function __toString()
    {
        return $this->email;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'ctime_front':
                return date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);

            case 'file_path_server':
                $this->file_path_server = $this->file_path . $this->file_name;
                return $this->file_path_server;

            default:
                ;
                break;
        }
    }

    public $id = NULL;
    public $email = '';
    public $remarks = '';
}

?>