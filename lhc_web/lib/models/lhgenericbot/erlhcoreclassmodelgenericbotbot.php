<?php

class erLhcoreClassModelGenericBotBot {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_bot';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
    public $header_content = '';
    public $header_css = '';
    public $static_content = '';
    public $static_js_content = '';
    public $static_css_content = '';
}