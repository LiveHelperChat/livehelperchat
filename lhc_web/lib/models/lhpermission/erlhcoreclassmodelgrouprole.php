<?php

class erLhcoreClassModelGroupRole
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_grouprole';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassRole::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'group_id' => $this->group_id,
            'role_id' => $this->role_id
        );
    }

    public $id = null;
    public $group_id = '';
    public $role_id = '';
}


?>