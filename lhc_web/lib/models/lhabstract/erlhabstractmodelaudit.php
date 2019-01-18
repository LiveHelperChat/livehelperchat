<?php

class erLhAbstractModelAudit
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_audits';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'category' => $this->category,
            'file' => $this->file,
            'line' => $this->line,
            'message' => $this->message,
            'severity' => $this->severity,
            'source' => $this->source,
            'time' => $this->time,
            'object_id' => $this->object_id,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->message;
    }

    public function getFields()
    {
        return include ('lib/core/lhabstract/fields/erlhabstractmodelaudit.php');
    }

    public function getModuleTranslations()
    {
        /**
         * Get's executed before permissions check.
         * It can redirect to frontpage throw permission exception etc
         */
        $metaData = array(
            'permission_delete' => array(
                'module' => 'lhsystem',
                'function' => 'auditlog'
            ),
            'permission' => array(
                'module' => 'lhsystem',
                'function' => 'auditlog'
            ),
            'table_class' => 'table-condensed table-small',
            'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Audit log')
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

    public $category = '';

    public $file = '';

    public $line = '';

    public $message = '';

    public $severity = '';

    public $source = '';

    public $time = '';

    public $object_id = 0;

    public $filter_name = 'audit';

    public $has_filter = true;

    public $hide_add = true;

}

?>