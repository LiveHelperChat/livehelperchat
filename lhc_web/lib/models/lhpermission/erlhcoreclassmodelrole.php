<?php

class erLhcoreClassModelRole
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_role';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassRole::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';

}


?>