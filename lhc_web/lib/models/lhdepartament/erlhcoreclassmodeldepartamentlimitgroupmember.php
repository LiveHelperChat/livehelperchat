<?php

class erLhcoreClassModelDepartamentLimitGroupMember
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_departament_limit_group_member';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'dep_limit_group_id' => $this->dep_limit_group_id
        );
    }

    public function __toString()
    {
        return $this->dep;
    }
        
    public function afterRemove()
    {
        $db = ezcDbInstance::get();

        $stmt = $db->prepare("UPDATE lh_departament SET pending_group_max = 0 WHERE id = :dep_id");
        $stmt->bindValue( ':dep_id', $this->dep_id);
        $stmt->execute();
    }
    
    public function __get($var)
    {
        switch ($var) {
            case 'dep':
                $this->dep = null;
                return $this->dep;
                break;
            
            default:
                break;
        }
    }

    public $id = null;

    public $dep_id = 0;

    public $dep_limit_group_id = 0;
}

?>