<?php

class erLhcoreClassModelCannedMsgReplace
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_canned_msg_replace';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'identifier' => $this->identifier,
            'default' => $this->default,
            'conditions' => $this->conditions
        );
    }

    public function __get($var)
    {
        switch ($var) {

            case 'conditions_array':
                $jsonData = json_decode($this->conditions,true);
                if ($jsonData !== null) {
                    $this->conditions_array = $jsonData;
                } else {
                    $this->conditions_array = $this->conditions;
                }

                if (!is_array($this->conditions_array)) {
                    $this->conditions_array = array();
                }

                return $this->conditions_array;

            default:
                break;
        }
    }

    public $id = null;
    public $identifier = '';
    public $default = '';
    public $conditions = '';
}

?>