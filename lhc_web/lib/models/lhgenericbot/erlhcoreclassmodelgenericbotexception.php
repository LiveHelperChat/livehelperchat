<?php

class erLhcoreClassModelGenericBotException {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_exception';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'priority' => $this->priority,
            'active' => $this->active
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $priority = 0;
    public $active = 1;
}