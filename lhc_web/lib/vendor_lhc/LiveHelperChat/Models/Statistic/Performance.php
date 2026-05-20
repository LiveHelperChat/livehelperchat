<?php

namespace LiveHelperChat\Models\Statistic;
#[\AllowDynamicProperties]
class Performance {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_performance';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'data' => $this->data
        );

        return $stateArray;
    }

    public function __toString()
    {
        return (string)$this->type;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'data_array':
                $varObject = str_replace('_array','', $var);
                $jsonData = json_decode($this->{$varObject}, true);
                if ($jsonData !== null) {
                    $this->{$var} = $jsonData;
                } else {
                    $this->{$var} = $this->{$varObject};
                }
                if (!is_array($this->{$var})) {
                    $this->{$var} = array();
                }
                return $this->{$var};
            default:
                ;
                break;
        }
    }

    const DEPARTMENT = 0;
    const OPERATOR = 1;

    public $id = null;
    public $type = self::DEPARTMENT;
    public $created_at = null;
    public $data = '';
}