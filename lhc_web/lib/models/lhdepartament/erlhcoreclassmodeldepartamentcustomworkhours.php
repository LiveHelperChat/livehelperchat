<?php

class erLhcoreClassModelDepartamentCustomWorkHours
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_departament_custom_work_hours';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'start_hour' => $this->start_hour,
            'end_hour' => $this->end_hour
        );
    }

    public function __get($var)
    {
        switch ($var) {
            case 'start_hour_front':
                return str_pad(floor($this->start_hour / 100), 2, '0', STR_PAD_LEFT);
                break;
            
            case 'start_minutes_front':
                return str_pad($this->start_hour - ($this->start_hour_front * 100), 2, '0', STR_PAD_LEFT);
                break;
            
            case 'end_hour_front':
                return str_pad(floor($this->end_hour / 100), 2, '0', STR_PAD_LEFT);
                break;
            
            case 'end_minutes_front':
                return str_pad($this->end_hour - ($this->end_hour_front * 100), 2, '0', STR_PAD_LEFT);
                break;
            
            default:
                ;
                break;
        }
    }

    public $id = null;

    public $dep_id = 0;

    public $date_from = 0;

    public $date_to = 0;

    public $start_hour = 0;

    public $end_hour = 0;
}

?>