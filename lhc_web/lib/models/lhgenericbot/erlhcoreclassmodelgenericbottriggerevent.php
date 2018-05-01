<?php

class erLhcoreClassModelGenericBotTriggerEvent {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_trigger_event';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'trigger_id' => $this->trigger_id,
            'pattern' => $this->pattern,
            'bot_id' => $this->bot_id,
            'type' => $this->type,
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
    public $type = 0;
    public $bot_id = 0;
}