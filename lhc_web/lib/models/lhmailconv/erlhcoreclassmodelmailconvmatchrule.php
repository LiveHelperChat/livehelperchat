<?php

class erLhcoreClassModelMailconvMatchRule
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_match_rule';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'active' => $this->active,
            'conditions' => $this->conditions
        );
    }

    public function __toString()
    {
        return $this->mail;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return '';

            case 'department':
                $this->department = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->department;

            default:
                ;
                break;
        }
    }

    public $id = NULL;
    public $dep_id = '';
    public $active = 1;
    public $conditions = '';
}

?>