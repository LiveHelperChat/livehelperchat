<?php

class erLhAbstractModelChatColumn
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_chat_column';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'column_name' => $this->column_name,
            'variable' => $this->variable,
            'position' => $this->position,
            'enabled' => $this->enabled,
            'conditions' => $this->conditions,
            'column_icon' => $this->column_icon,
            'column_identifier' => $this->column_identifier,
            'chat_enabled' => $this->chat_enabled,
            'online_enabled' => $this->online_enabled,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->column_name;
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodelchatcolumn.php');
    }


    public function getModuleTranslations()
    {
        /**
         * Get's executed before permissions check.
         * It can redirect to frontpage throw permission exception etc
         */
        $metaData = array(
            'permission_delete' => array(
                'module' => 'lhchat',
                'function' => 'administratecolumn'
            ),
            'permission' => array(
                'module' => 'lhchat',
                'function' => 'administratecolumn'
            ),
            'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Chat columns')
        );

        return $metaData;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'left_menu':
                $this->left_menu = '';
                return $this->left_menu;
                break;

            default:
                break;
        }
    }

    public $id = null;

    public $column_name = '';

    public $column_icon = '';
    
    public $column_identifier = '';

    public $variable = '';

    public $position = '';

    public $enabled = 1;

    public $conditions = '';

    public $chat_enabled = 1;

    public $online_enabled = 1;

    public $hide_delete = false;
}

?>