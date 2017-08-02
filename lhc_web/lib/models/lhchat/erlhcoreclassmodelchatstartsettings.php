<?php

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
                break;
            
            case 'data_array':
                $this->data_array = unserialize($this->data);                                
                return $this->data_array;
                break;
            
            default:
                break;
        }
    }

    public $id = null;

    public $data = '';
    
    public $name = '';

    public $department_id = 0;
}

?>