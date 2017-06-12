<?php

class erLhcoreClassModelDepartamentLimitGroup
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_departament_limit_group';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'pending_max' => $this->pending_max
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'departments_ids':
                $rows = array();
                if ($this->id > 0) {
                    $db = ezcDbInstance::get();                 
                    $stmt = $db->prepare('SELECT dep_id FROM lh_departament_limit_group_member WHERE dep_limit_group_id = :dep_group_id');
                    $stmt->bindValue( ':dep_group_id',$this->id);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
                }
                return $rows;
                break;
            
            default:
                break;
        }
    }
    
    public function updateDepartmentsLimits()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("UPDATE lh_departament SET pending_group_max = :pending_group_max WHERE id IN (SELECT dep_id FROM lh_departament_limit_group_member WHERE dep_limit_group_id = :dep_limit_group_id)");
        $stmt->bindValue( ':dep_limit_group_id',$this->id);
        $stmt->bindValue( ':pending_group_max',$this->pending_max);
        $stmt->execute();
    }
    
    public function afterRemove()
    {        
        foreach (erLhcoreClassModelDepartamentLimitGroupMember::getList(array('limit' => false, 'filter' => array('dep_limit_group_id' => $this->id))) as $groupMember)
        {
            $groupMember->removeThis();
        }
        
    }
    
    public $id = null;

    public $name = '';
    
    public $pending_max = 0;
}

?>