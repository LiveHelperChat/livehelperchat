<?php

class erLhAbstractModelChatPriority
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_chat_priority';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'sort_priority DESC, priority DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'value' => $this->value,
            'priority' => $this->priority,
            'sort_priority' => $this->sort_priority,
            'dest_dep_id' => $this->dest_dep_id
        );

        return $stateArray;
    }

    public function __toString()
    {
        return (string)$this->value_frontend;
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodelchatpriority.php');
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
                'function' => 'administratechatpriority'
            ),
            'permission' => array(
                'module' => 'lhchat',
                'function' => 'administratechatpriority'
            ),
            'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Chat priority')
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

            case 'dep':
                $this->dep = null;

                if ($this->dep_id > 0) {
                    $this->dep = erLhcoreClassModelDepartament::fetch($this->dep_id);
                }

                return $this->dep;

            case 'dest_dep':
                $this->dest_dep = null;

                if ($this->dest_dep_id > 0) {
                    $this->dest_dep = erLhcoreClassModelDepartament::fetch($this->dest_dep_id);
                }

                return $this->dest_dep;

            case 'value_array':
                $this->value_array = array();
                if ($this->value != ''){
                    $this->value_array = json_decode($this->value,true);
                }
                return $this->value_array;
                break;

            case 'value_frontend':
                $items = array();
                foreach ($this->value_array as $item) {
                    $items[] = $item['field'] . ' '. $item['comparator'] . ' ' .  $item['value'];
                }

                $this->value_frontend = implode(', ', $items);
                return $this->value_frontend;
                break;

            default:
                break;
        }
    }

    public function dependFooterJs()
    {
        return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular.lhc.priority.js').'"></script>';
    }

    public function customForm()
    {
        return 'chat_priority.tpl.php';
    }

    public $id = null;

    public $priority = 0;

    public $sort_priority = 0;

    public $value = '';

    public $dep_id = 0;

    public $dest_dep_id = 0;

    public $hide_delete = false;
}

?>