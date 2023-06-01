<?php

namespace LiveHelperChat\Models\Brand;

class BrandMember {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_brand_member';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'dep_id ASC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'brand_id' => $this->brand_id,
            'role' => $this->role
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->role;
    }

    public function __get($var)
    {
        switch ($var) {


            default:
                ;
                break;
        }
    }

    public $id = null;
    public $dep_id = null;
    public $brand_id = null;
    public $role = '';
}