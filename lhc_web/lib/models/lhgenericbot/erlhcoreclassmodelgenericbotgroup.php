<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelGenericBotGroup {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_group';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'bot_id' => $this->bot_id,
            'is_collapsed' => $this->is_collapsed,
            'pos' => $this->pos,
        );

        return $stateArray;
    }

    public function beforeRemove()
    {
        foreach (erLhcoreClassModelGenericBotTrigger::getList(array('filter' => array('group_id' => $this->id))) as $trigger) {
            $trigger->removeThis();
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $bot_id = 0;
    public $is_collapsed = 0;
    public $pos = 0;
}