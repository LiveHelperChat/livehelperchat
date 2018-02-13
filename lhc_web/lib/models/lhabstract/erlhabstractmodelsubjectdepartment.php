<?php

class erLhAbstractModelSubjectDepartment {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_subject_dep';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'subject_id' => $this->subject_id
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
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
    public $dep_id = 0;
    public $subject_id = 0;
}