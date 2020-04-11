<?php

class erLhcoreClassModelGenericBotRestAPI {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_rest_api';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array (
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'configuration' => $this->configuration,
        );
    }

    public function __get($var) {

        switch ($var) {
            case 'configuration_array':
                $jsonData = json_decode($this->configuration,true);
                if ($jsonData !== null) {
                    $this->configuration_array = $jsonData;
                } else {
                    $this->configuration_array = array();
                }
                return $this->configuration_array;
                break;

            default:
                break;
        }
    }

    public $id = null;
    public $name = '';
    public $description = '';
    public $configuration = '';
}
