<?php

class erLhAbstractModelStats {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_stats';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'type' => $this->type,
            'object_id' => $this->object_id,
            'stats' => $this->stats,
            'lupdate' => $this->lupdate,
        );

        return $stateArray;
    }

    public static function getInstance($type, $objectId) {
        $stats = self::findOne(array('filter' => array('type' => $type, 'object_id' => $objectId)));
        if (!($stats instanceof erLhAbstractModelStats)) {
            $stats = new self();
            $stats->type = $type;
            $stats->object_id = $objectId;
        }

        return $stats;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'stats_array':
                $stats = json_decode($this->stats,true);
                if ($stats === null){
                    $stats = [];
                }
                $this->stats_array = $stats;
                return $this->stats_array;

            default:
                break;
        }
    }

    const STATS_DEP = 0;

    public $id = null;
    public $type = self::STATS_DEP;
    public $object_id = 0;
    public $stats = '';
    public $lupdate = 0;
}