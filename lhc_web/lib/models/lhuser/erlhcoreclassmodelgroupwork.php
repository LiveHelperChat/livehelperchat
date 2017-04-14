<?php

class erLhcoreClassModelGroupWork
{
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_group_work';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';
    
    public static $dbSortOrder = 'DESC';
    
    public function getState()
    {
        return array(
            'id' => $this->id,
            'group_id' => $this->group_id,
            'group_work_id' => $this->group_work_id
        );
    }
     
    public $id = null;

    public $group_id = 0;

    public $group_work_id = 0;
}

?>