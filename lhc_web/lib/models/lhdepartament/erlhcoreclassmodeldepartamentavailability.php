<?php

class erLhcoreClassModelDepartamentAvailability {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_departament_availability';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'hour' => $this->hour,
            'minute' => $this->minute,
            'time' => $this->time,
            'status' => $this->status,
            'ymdhi' => $this->ymdhi,
            'ymd' => $this->ymd,
            'hourminute' => $this->hourminute,
        );
    }

    public function __toString()
    {
        return $this->dep;
    }

    public function __get($var) {
        switch ($var) {
            case 'is_online':
                $this->is_online = erLhcoreClassChat::isOnline($this->id);
                return $this->is_online;
                break;

            default:
                break;
        }
    }

    public function beforeSave()
    {
        $this->hourminute = $this->hour . str_pad($this->minute, 2, '0', STR_PAD_LEFT);
    }

    const STATUS_ONLINE = 0;
    const STATUS_DISABLED = 1;
    const STATUS_OVERLOADED = 2;
    const STATUS_OFFLINE = 3;

    public $id = null;
    public $dep_id = 0;
    public $hour = '';
    public $minute = 0;
    public $time = 0;
    public $ymdhi = 0;
    public $ymd = 0;
    public $hourminute = 0;
    public $status = self::STATUS_ONLINE;
}

?>