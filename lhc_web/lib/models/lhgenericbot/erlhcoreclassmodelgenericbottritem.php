<?php

class erLhcoreClassModelGenericBotTrItem {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_tr_item';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'group_id' => $this->group_id,
            'identifier' => $this->identifier,
            'translation' => $this->translation
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $identifier = '';
    public $translation = '';
    public $group_id = 0;
}