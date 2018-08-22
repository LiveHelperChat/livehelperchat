<?php

class erLhcoreClassModelRoleFunction
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_rolefunction';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassRole::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'role_id' => $this->role_id,
            'module' => $this->module,
            'function' => $this->function,
            'limitation' => $this->limitation,
        );
    }

    public $id = null;
    public $role_id = null;
    public $module = null;
    public $function = null;
    public $limitation = '';
}

?>