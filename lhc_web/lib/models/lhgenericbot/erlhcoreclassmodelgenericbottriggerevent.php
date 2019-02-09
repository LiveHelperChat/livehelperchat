<?php

class erLhcoreClassModelGenericBotTriggerEvent {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_trigger_event';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'ASC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'trigger_id' => $this->trigger_id,
            'pattern' => $this->pattern,
            'pattern_exc' => $this->pattern_exc,
            'configuration' => $this->configuration,
            'bot_id' => $this->bot_id,
            'type' => $this->type,
            'on_start_type' => $this->on_start_type,
            'priority' => $this->priority,
        );

        return $stateArray;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'actions_front':
                return $this->actions_front;
                break;

            case 'trigger':
                $this->trigger = erLhcoreClassModelGenericBotTrigger::fetch($this->trigger_id);
                return $this->trigger;
                break;

            case 'configuration_array':
                $this->configuration_array = $this->configuration != '' ? json_decode($this->configuration,true) : new stdClass();

                if (is_array($this->configuration_array ) && empty($this->configuration_array)) {
                    $this->configuration_array = new stdClass();
                }

                return $this->configuration_array;
                break;

            default:
                break;
        }
    }

    public function __toString()
    {
        return $this->pattern;
    }

    public $id = null;
    public $trigger_id = 0;
    public $pattern = '';
    public $pattern_exc = '';
    public $configuration = '';
    public $type = 0;
    public $bot_id = 0;
    public $on_start_type = 0;
    public $priority = 0;
}