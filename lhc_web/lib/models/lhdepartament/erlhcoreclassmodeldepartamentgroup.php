<?php

class erLhcoreClassModelDepartamentGroup
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_departament_group';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassDepartament::getSession';

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

    public function __get($var)
    {
        switch ($var) {
            case 'departments_ids':
                $rows = array();
                if ($this->id > 0) {
                    $db = ezcDbInstance::get();                 
                    $stmt = $db->prepare('SELECT dep_id FROM lh_departament_group_member WHERE dep_group_id = :dep_group_id');
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

    public function afterRemove()
    {
        foreach (erLhcoreClassModelDepartamentGroupUser::getList(array('limit' => false, 'filter' => array('dep_group_id' => $this->id))) as $groupUser)
        {
            $groupUser->removeThis();
        }
        
        foreach (erLhcoreClassModelDepartamentGroupMember::getList(array('limit' => false, 'filter' => array('dep_group_id' => $this->id))) as $groupMember)
        {
            $groupMember->removeThis();
        }
        
    }
    
    public $id = null;

    public $name = '';
}

?>