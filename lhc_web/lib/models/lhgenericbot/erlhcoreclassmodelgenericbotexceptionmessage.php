<?php

class erLhcoreClassModelGenericBotExceptionMessage {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_exception_message';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'code' => $this->code,
            'exception_group_id' => $this->exception_group_id,
            'message' => $this->message,
            'active' => $this->active,
            'priority' => $this->priority
        );

        return $stateArray;
    }

    public function __toString()
    {
        return (string)$this->code;
    }

    public $id = null;
    public $code = '';
    public $exception_group_id = 0;
    public $message = '';
    public $default_message = '';
    public $active = 0;
    public $priority = 0;
}