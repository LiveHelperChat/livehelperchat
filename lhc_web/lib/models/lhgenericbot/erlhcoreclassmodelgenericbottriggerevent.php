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
}