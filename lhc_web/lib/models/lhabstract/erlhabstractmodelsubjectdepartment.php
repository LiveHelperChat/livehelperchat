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
        return (string)$this->subject;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'subject':
                $this->subject = erLhAbstractModelSubject::fetch($this->subject_id);
                return $this->subject;
            default:
                ;
                break;
        }
    }

    public $id = null;
    public $dep_id = 0;
    public $subject_id = 0;
}