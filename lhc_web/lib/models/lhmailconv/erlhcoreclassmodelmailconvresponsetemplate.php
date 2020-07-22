<?php

class erLhcoreClassModelMailconvResponseTemplate
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_response_template';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'template' => $this->template
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'ctime_front':
                return date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                break;

            default:
                ;
                break;
        }
    }

    public $id = NULL;
    public $name = '';
    public $template = '';
}

?>