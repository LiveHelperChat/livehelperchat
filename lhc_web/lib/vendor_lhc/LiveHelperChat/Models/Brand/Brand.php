<?php

namespace LiveHelperChat\Models\Brand;

class Brand {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_brand';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'name ASC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function afterRemove()
    {
        foreach (BrandMember::getList(['filter' => ['brand_id' => $this->id]]) as $brand) {
            $brand->removeThis();
        }
    }

    public function __get($var)
    {
        switch ($var) {

            case 'departments_ids':
                $rows = array();
                if ($this->id > 0) {
                    $db = \ezcDbInstance::get();
                    $stmt = $db->prepare('SELECT dep_id FROM lh_brand_member WHERE brand_id = :brand_id');
                    $stmt->bindValue( ':brand_id',$this->id);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                }
                return $rows;

            case 'conditions_array':
                $this->conditions_array = $this->departments_ids;
                return $this->conditions_array;

            case 'conditions_array_roles':
                $this->conditions_array_roles = [];
                foreach (BrandMember::getList(['filter' => ['brand_id' => $this->id]]) as $member) {
                    $this->conditions_array_roles[$member->dep_id] = $member->role;
                }
                return $this->conditions_array_roles;

            default:
                ;
                break;
        }
    }

    public function saveMembers($membersNew) {

        $membersPresent = BrandMember::getList(['filter' => ['brand_id' => $this->id]]);

        $membersUpdated = [];

        // Update members
        foreach ($membersNew as $memberNew) {
            foreach ($membersPresent as $memberPresent) {
                if ($memberPresent->dep_id == $memberNew['dep_id']) {
                    $memberPresent->role = $memberNew['role'];
                    $memberPresent->updateThis();
                    $membersUpdated[] = $memberNew['dep_id'];
                }
            }
        }

        // Store new members
        foreach ($membersNew as $memberNew) {
            if (!in_array($memberNew['dep_id'],$membersUpdated)){
                $memberPresent = new BrandMember();
                $memberPresent->dep_id = $memberNew['dep_id'];
                $memberPresent->role = $memberNew['role'];
                $memberPresent->brand_id = $this->id;
                $memberPresent->saveThis();
                $membersUpdated[] = $memberNew['dep_id'];
            }
        }

        // Remove deleted members
        foreach ($membersPresent as $memberPresent) {
            if (!in_array($memberPresent->dep_id, $membersUpdated)) {
                $memberPresent->removeThis();
            }
        }

    }

    public $id = null;
    public $name = '';
}