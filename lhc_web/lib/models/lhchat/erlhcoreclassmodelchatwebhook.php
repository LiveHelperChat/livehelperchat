<?php

class erLhcoreClassModelChatWebhook {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_webhook';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id'            => $this->id,
            'event'         => $this->event,
            'bot_id'        => $this->bot_id,
            'trigger_id'    => $this->trigger_id,
            'trigger_id_alt'=> $this->trigger_id_alt,
            'bot_id_alt'    => $this->bot_id_alt,
            'disabled'      => $this->disabled,
            'type'          => $this->type,
            'configuration' => $this->configuration,
        );
    }

    public function __get($var) {

        switch ($var) {

            case 'bot':
                $this->bot = erLhcoreClassModelGenericBotBot::fetch($this->bot_id);
                return $this->bot;

            case 'trigger':
                $this->trigger = erLhcoreClassModelGenericBotTrigger::fetch($this->trigger_id);
                return $this->trigger;

            case 'conditions_array':
                $conditions_array = json_decode($this->configuration,true);
                if ($conditions_array === null) {
                    $conditions_array = [];
                }
                $this->conditions_array = $conditions_array;
                return $this->conditions_array;

            default:
                break;
        }
    }

    public $id = null;
    public $event = '';
    public $bot_id = 0;
    public $trigger_id = 0;
    public $bot_id_alt = 0;
    public $trigger_id_alt = 0;
    public $disabled = 0;
    public $type = 0;
    public $configuration = '';
}

?>