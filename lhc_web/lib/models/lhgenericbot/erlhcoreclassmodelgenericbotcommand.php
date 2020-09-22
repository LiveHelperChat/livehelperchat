<?php

class erLhcoreClassModelGenericBotCommand {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_command';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'command' => $this->command,
            'dep_id' => $this->dep_id,
            'bot_id' => $this->bot_id,
            'trigger_id' => $this->trigger_id
        );

        return $stateArray;
    }

    public function __get($var) {

        switch ($var) {

            case 'bot':
                $this->bot = erLhcoreClassModelGenericBotBot::fetch($this->bot_id);
                return $this->bot;

            case 'trigger':
                $this->trigger = erLhcoreClassModelGenericBotTrigger::fetch($this->trigger_id);
                return $this->trigger;

            case 'dep':
                $this->dep = null;
                if ($this->dep_id > 0) {
                    $this->dep = erLhcoreClassModelDepartament::fetch($this->dep_id);
                }
                return $this->dep;

            default:
                break;
        }
    }

    public function __toString()
    {
        return $this->command;
    }

    public $id = null;
    public $command = '';
    public $dep_id = 0;
    public $bot_id = 0;
    public $trigger_id = 0;
}