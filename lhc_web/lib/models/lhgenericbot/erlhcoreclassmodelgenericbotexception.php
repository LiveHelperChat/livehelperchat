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

    public function afterSave()
    {
        foreach ($this->exceptions as $exception) {
            $exception->exception_group_id = $this->id;
            if ($exception->message != '') {
                $exception->saveThis();
            } elseif ($exception->message == '' && $exception->id > 0){
                $exception->removeThis();
            }
        }
    }

    public function afterRemove()
    {
        foreach (erLhcoreClassModelGenericBotExceptionMessage::getList(array('filter' => array('exception_group_id' => $this->id))) as $exception){
            $exception->removeThis();
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $priority = 0;
    public $active = 1;
    public $exceptions = array();
}