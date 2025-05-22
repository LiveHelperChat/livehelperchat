<?php
#[\AllowDynamicProperties]
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
            'name' => $this->name,
            'achats_cnt' => $this->achats_cnt,
            'inachats_cnt' => $this->inachats_cnt,
            'pchats_cnt' => $this->pchats_cnt,
            'bchats_cnt' => $this->bchats_cnt,
            'max_load' => $this->max_load,
            'max_load_h' => $this->max_load_h,
            'inopchats_cnt' => $this->inopchats_cnt,
            'acopchats_cnt' => $this->acopchats_cnt,
            'max_load_op' => $this->max_load_op,
            'max_load_op_h' => $this->max_load_op_h
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
    public $achats_cnt = 0;
    public $inachats_cnt = 0;
    public $pchats_cnt = 0;
    public $bchats_cnt = 0;
    public $inopchats_cnt = 0;
    public $acopchats_cnt = 0;
    public $max_load = 0;
    public $max_load_h = 0;
    public $max_load_op = 0;
    public $max_load_op_h = 0;
}

?>