<?php

class erLhAbstractModelChatAlertIcon
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_chat_alert_icon';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'identifier' => $this->identifier,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodelchatalerticon.php');
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
                'function' => 'administrate_alert_icon'
            ),
            'permission' => array(
                'module' => 'lhchat',
                'function' => 'administrate_alert_icon'
            ),
            'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Chat alert icons')
        );

        return $metaData;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'left_menu':
                $this->left_menu = '';
                return $this->left_menu;

            default:
                break;
        }
    }

    public $id = null;
    public $name = '';
    public $identifier = '';

    public $hide_delete = false;
}

?>