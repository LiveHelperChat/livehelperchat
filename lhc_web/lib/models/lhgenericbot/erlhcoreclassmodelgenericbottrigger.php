<?php

class erLhcoreClassModelGenericBotTrigger {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_trigger';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'group_id' => $this->group_id,
            'bot_id' => $this->bot_id,
            'actions' => $this->actions,
        	'default' => $this->default,
        	'default_unknown' => $this->default_unknown,
        	'default_always' => $this->default_always,
        	'default_unknown_btn' => $this->default_unknown_btn,
        );

        return $stateArray;
    }

    public function beforeRemove()
    {
        $q = ezcDbInstance::get()->createDeleteQuery();

        // Messages
        $q->deleteFrom( 'lh_generic_bot_trigger_event' )->where( $q->expr->eq( 'trigger_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();
    }

    public function __get($var)
    {
        switch ($var) {
            case 'actions_front':

                    if ($this->actions == ''){
                        $this->actions_front = array();
                    } else {
                        $this->actions_front = json_decode($this->actions, true);
                    }

                    if (!is_array($this->actions_front)){
                        $this->actions_front = array();
                    }

                    return $this->actions_front;

                break;

            case 'events':
                    $this->events = erLhcoreClassModelGenericBotTriggerEvent::getList(array('filter' => array('trigger_id' => $this->id)));
                    return $this->events;
                break;

            default:
                break;
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $actions = '';
    public $group_id = 0;
    public $bot_id = 0;
    public $default = 0;
    public $default_unknown = 0;
    public $default_always = 0;
    public $default_unknown_btn = 0;
}