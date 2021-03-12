<?php

class erLhcoreClassModelChatIncomingWebhook {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_incoming_webhook';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'            => $this->id,
            'name'          => $this->name,
            'dep_id'        => $this->dep_id,
            'disabled'      => $this->disabled,
            'identifier'    => $this->identifier,
            'configuration' => $this->configuration
        );
    }

    public function __get($var) {
        switch ($var) {
            case 'conditions_array':

                if ($this->configuration == '[]') {
                    $this->configuration = '{}';
                }

                $conditions_array = json_decode($this->configuration,true);
                if ($conditions_array === null) {
                    $conditions_array = new stdClass();
                }
                $this->conditions_array = $conditions_array;
                return $this->conditions_array;
            default:
                break;
        }
    }

    public $id = null;
    public $name = '';
    public $disabled = 0;
    public $dep_id = 0;
    public $configuration = '';
}

?>