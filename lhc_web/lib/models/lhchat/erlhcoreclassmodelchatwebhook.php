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
            'id'              => $this->id,
            'event'           => $this->event,
            'bot_id'          => $this->bot_id,
            'trigger_id'      => $this->trigger_id,
            'disabled'        => $this->disabled
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

            default:
                break;
        }
    }

    public $id = null;
    public $event = '';
    public $bot_id = 0;
    public $trigger_id = 0;
    public $disabled = 0;
}

?>