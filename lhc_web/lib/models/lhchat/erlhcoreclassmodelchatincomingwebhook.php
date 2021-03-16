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
            'scope'         => $this->scope,
            'configuration' => $this->configuration
        );
    }

    public function __get($var) {
        switch ($var) {
            case 'conditions_array':
                $this->conditions_array = [];

                if ($this->configuration != '') {
                    $conditions_array = json_decode($this->configuration,true);
                    if ($conditions_array !== null) {
                        $this->conditions_array = $conditions_array;
                    }
                }

                if (!isset($this->conditions_array['attr'])) {
                    $this->conditions_array['attr'] = [];
                }

                return $this->conditions_array;

            case 'attributes':
                $attributes = [];
                foreach ($this->conditions_array['attr'] as $attr) {
                    $attributes[$attr['key']] = $attr['value'];
                }
                $this->attributes = $attributes;
                return $this->attributes;

            default:
                break;
        }
    }

    public $id = null;
    public $name = '';
    public $disabled = 0;
    public $dep_id = 0;
    public $configuration = '';
    public $scope = '';
    public $identifier = '';
}

?>