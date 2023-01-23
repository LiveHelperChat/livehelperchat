<?php

class erLhAbstractModelSavedSearch {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_saved_search';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'position DESC, id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'params' => $this->params,
            'scope' => $this->scope,
            'position' => $this->position,
            'user_id' => $this->user_id,
            'days' => $this->days,
            'updated_at' => $this->updated_at,
            'requested_at' => $this->requested_at,
            'total_records' => $this->total_records,
            'passive' => $this->passive,
            'description' => $this->description,
            'sharer_user_id' => $this->sharer_user_id,
            'status' => $this->status,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'params_array':
                $jsonData = json_decode($this->params, true);
                if ($jsonData !== null) {
                    $this->params_array = $jsonData;
                } else {
                    $this->params_array = $this->params;
                }

                if (!is_array($this->params_array)) {
                    $this->params_array = array();
                }

                return $this->params_array;

            case 'user':
                $this->user = $this->user_id;
                return $this->user;

            case 'updated_ago':
                $this->updated_ago = erLhcoreClassChat::formatSeconds(time() - $this->updated_at);
                return $this->updated_ago;

            default:
                ;
                break;
        }
    }

    const ACTIVE = 0;
    const INVITE = 1;

    public $id = null;
    public $name = '';
    public $params = '';
    public $days = 180;
    public $user_id = 0;
    public $scope = 'chat';
    public $position = 0;
    public $requested_at = 0;
    public $updated_at = 0;
    public $total_records = 0;
    public $passive = 1;
    public $description = '';
    public $sharer_user_id = 0;
    public $status = self::ACTIVE;
}