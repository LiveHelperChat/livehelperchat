<?php

class erLhcoreClassModelGenericBotChatWorkflow {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_chat_workflow';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array (
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'identifier' => $this->identifier,
            'status' => $this->status,
            'collected_data' => $this->collected_data,
            'trigger_id' => $this->trigger_id,
            'time' => $this->time
        );

        return $stateArray;
    }

    public function __get($var) {

        switch ($var) {
            case 'collected_data_array':
                $jsonData = json_decode($this->collected_data,true);
                if ($jsonData !== null) {
                    $this->collected_data_array = $jsonData;
                } else {
                    $this->collected_data_array = array();
                }
                return $this->collected_data_array;
                break;

            default:
                break;
        }
    }

    public function __toString()
    {
        return $this->identifier;
    }

    const STATUS_STARTED = 0;
    const STATUS_PENDING_CONFIRM = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELED = 3;
    const STATUS_EXPIRED = 4;

    public $id = null;
    public $chat_id = null;
    public $identifier = '';
    public $status = self::STATUS_STARTED;
    public $collected_data = '';
    public $trigger_id = null;
    public $time = 0;

}
