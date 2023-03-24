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
            'chat_list_enabled' => $this->chat_list_enabled,
            'online_enabled' => $this->online_enabled,
            'icon_mode' => $this->icon_mode,
            'has_popup' => $this->has_popup,
            'popup_content' => $this->popup_content,
            'sort_column' => $this->sort_column,
            'sort_enabled' => $this->sort_enabled,
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

    public function getSort($asc = true)
    {
        if ($this->sort_enabled == 0) {
            return;
        }

        if ($this->sort_column != '') {
            if (preg_match('/^([A-Za-z0-9_]{2,60})$/', $this->sort_column)) {
                return '`' . $this->sort_column . '`' . ($asc === true ? ' ASC' : ' DESC');
            } elseif (preg_match('/^JSON_EXTRACT\(`lh_chat`\.`([A-Za-z0-9_]{2,60})`, \'\$.([A-Za-z0-9_]{2,60})\'\)$/',$this->sort_column)) {
                return $this->sort_column . ($asc === true ? ' ASC' : ' DESC');
            }
        }

        if (strpos($this->variable,'additional_data.') !== false) {
            return ; // Not supported because of the structure
        } elseif (strpos($this->variable,'chat_variable.') !== false) {
            $db = ezcDbInstance::get();
            $variableName = str_replace('chat_variable.','', $this->variable);
            return "JSON_EXTRACT(`lh_chat`.`chat_variables`, " . $db->quote('$.' . $variableName) . ")" . ($asc ? ' ASC' : ' DESC');
        } elseif (strpos($this->variable,'lhc.') !== false) {
            $db = ezcDbInstance::get();
            $chat = new erLhcoreClassModelChat();
            $validColumn = array_keys($chat->getState());
            $column = str_replace('lhc.','', $this->variable);
            if (in_array($column, $validColumn)) {
                return "`lh_chat`.`$column`" . ($asc ? ' ASC' : ' DESC');
            }
        }
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
    public $popup_content = '';
    public $chat_enabled = 1;
    public $chat_list_enabled = 0;
    public $online_enabled = 1;
    public $icon_mode = 0;
    public $has_popup = 0;
    public $sort_column = '';
    public $sort_enabled = 0;
    public $hide_delete = false;
}

?>