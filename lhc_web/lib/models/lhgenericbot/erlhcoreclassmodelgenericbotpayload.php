<?php

class erLhcoreClassModelGenericBotPayload {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_payload';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'payload' => $this->payload,
            'bot_id' => $this->bot_id,
            'trigger_id' => $this->trigger_id,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $payload = '';
    public $bot_id = 0;
    public $trigger_id = 0;
}