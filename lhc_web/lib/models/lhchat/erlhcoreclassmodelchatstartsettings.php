<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelChatStartSettings
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_start_settings';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'data' => $this->data,
            'dep_ids' => $this->dep_ids,
            'name' => $this->name,
            'department_id' => $this->department_id
        );
    }

    public function __get($var)
    {
        switch ($var) {
            
            case 'department':
                $this->department = false;
                if ($this->department_id > 0) {
                    try {
                        $this->department = erLhcoreClassModelDepartament::fetch($this->department_id, true);
                    } catch (Exception $e) {}
                }
                
                return $this->department;

            case 'data_array':
                $this->data_array = unserialize($this->data);                                
                return $this->data_array;

            case 'dep_ids_array':
                $this->dep_ids_array = json_decode($this->dep_ids, true);
                if (!is_array($this->dep_ids_array)) {
                    $this->dep_ids_array = [];
                }
                return $this->dep_ids_array;
            
            default:
                break;
        }
    }

    public $id = null;

    public $data = '';

    public $dep_ids = '';

    public $name = '';

    public $department_id = 0;
}

?>