<?php
#[\AllowDynamicProperties]
class erLhAbstractModelChatVariable
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_chat_variable';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'js_variable' => $this->js_variable,
            'var_name' => $this->var_name,
            'var_identifier' => $this->var_identifier,
            'type' => $this->type,
            'persistent' => $this->persistent,
            'inv' => $this->inv,
            'change_message' => $this->change_message,
            'old_js_id' => $this->old_js_id,
            'content_field' => $this->content_field,
            'try_decrypt' => $this->try_decrypt,
            'case_insensitive' => $this->case_insensitive,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->var_name;
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodelchatvariable.php');
    }

    public static function getDataTypes()
    {
        $items = array();

        $item = new stdClass();
        $item->id = 0;
        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','String');
        $items[] = $item;

        $item = new stdClass();
        $item->id = 1;
        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Integer');
        $items[] = $item;

        $item = new stdClass();
        $item->id = 2;
        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Decimal');
        $items[] = $item;

        $item = new stdClass();
        $item->id = 3;
        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Encrypted');
        $items[] = $item;

        $item = new stdClass();
        $item->id = 5;
        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Website cookie');
        $items[] = $item;

        $item = new stdClass();
        $item->id = 6;
        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','JWT Token content field');
        $items[] = $item;

        return $items;
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
                'function' => 'administratechatvariable'
            ),
            'permission' => array(
                'module' => 'lhchat',
                'function' => 'administratechatvariable'
            ),
            'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Chat variables')
        );

        return $metaData;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'dep':
                if ($this->dep_id > 0) {
                    $this->dep = erLhcoreClassModelDepartament::fetch($this->dep_id);
                } else {
                    $this->dep = null;
                }
                return $this->dep;
                break;

            case 'left_menu':
                $this->left_menu = '';
                return $this->left_menu;
                break;

            default:
                break;
        }
    }

    public $id = null;
    public $dep_id = 0;
    public $js_variable = '';
    public $var_name = '';
    public $var_identifier = '';
    public $type = 0;
    public $persistent = 0;
    public $inv = 0;
    public $try_decrypt = 0;
    public $case_insensitive = 0;
    public $change_message = '';
    public $old_js_id = '';
    public $content_field = '';
    public $hide_delete = false;
}

?>