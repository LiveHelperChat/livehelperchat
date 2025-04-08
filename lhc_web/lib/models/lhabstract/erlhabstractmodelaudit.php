<?php
#[\AllowDynamicProperties]
class erLhAbstractModelAudit
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_audits';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = '\erLhcoreClassAbstract::getSession';

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
            'user_id' => $this->user_id
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

    public function doExport($filter)
    {
        $filename = "audit-".date('Y-m-d').".csv";
        $fp = fopen('php://output', 'w');

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);

        fputcsv($fp, ['id','category','file','line','message','severity','source','time','object_id']);

        $filter['limit'] = 5000;

        foreach (self::getList($filter) as $data) {
            fputcsv($fp,[
                $data->id,
                $data->category,
                $data->file,
                $data->line,
                trim(str_replace(['"'],['^'],$data->message)),
                $data->severity,
                $data->source,
                $data->time,
                $data->object_id
            ]);
        }

        exit;
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

            case 'message_array':
                $this->message_array = json_decode($this->message, true);
                return $this->message_array;

            case 'id_frontend':
                $this->id_frontend = '[' . $this->id . '] [' . $this->user_id . '] ' . $this->object_id;
                return $this->id_frontend;

            case 'user_name':
                return $this->user_name = (string)$this->user;

            case 'plain_user_name':
                $this->plain_user_name = false;
                if ($this->user !== false) {
                    $this->plain_user_name = (string)$this->user->name_support;
                }
                return $this->plain_user_name;

            case 'user':
                $this->user = false;
                if ($this->user_id > 0) {
                    try {
                        $this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
                    } catch (Exception $e) {
                        $this->user = false;
                    }
                }
                return $this->user;

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

    public $user_id = 0;

    public $filter_name = 'audit';

    public $has_filter = true;

    public $hide_add = true;

}

?>