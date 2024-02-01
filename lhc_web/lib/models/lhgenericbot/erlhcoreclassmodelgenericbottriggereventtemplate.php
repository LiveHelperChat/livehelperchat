<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelGenericBotTriggerEventTemplate {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_trigger_event_template';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'configuration' => $this->configuration,
        );

        return $stateArray;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'configuration_array':
                $this->configuration_array = $this->configuration != '' ? json_decode($this->configuration,true) : [];

                if (is_array($this->configuration_array ) && empty($this->configuration_array)) {
                    $this->configuration_array = [];
                }

                return $this->configuration_array;

            default:
                break;
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $configuration = '';
}