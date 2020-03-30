<?php

class erLhAbstractModelSubject {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_subject';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

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

    public function customForm() {
        return 'subject.tpl.php';
    }

    public function getFields()
    {
        $items = include ('lib/core/lhabstract/fields/erlhabstractmodelsubject.php');
        return $items;
    }

    public function getModuleTranslations()
    {
        $metaData = array('permission_delete' => array('module' => 'lhchat','function' => 'administratesubject'),'permission' => array('module' => 'lhchat','function' => 'administratesubject'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Subject'));
        /**
         * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
         * */
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_subject', array('object_meta_data' => & $metaData));

        return $metaData;
    }

    public function afterSave()
    {
        $this->saveSubjects();
    }

    public function afterUpdate()
    {
        $this->saveSubjects();
    }

    public function afterRemove()
    {
        foreach (erLhAbstractModelSubjectDepartment::getList(array('filter' => array('subject_id' => $this->id))) as $subjectDep) {
           $subjectDep->removeThis();
        }
    }

    private function saveSubjects()
    {
        $varExisting = array();

        // Remove legacy assignment
        foreach (erLhAbstractModelSubjectDepartment::getList(array('filter' => array('subject_id' => $this->id))) as $subjectDep) {
            if (!in_array($subjectDep->dep_id, $this->dep_id)) {
                $subjectDep->removeThis();
            } else {
                $varExisting[] = $subjectDep->dep_id;
            }
        }

        $newEntries = array_diff($this->dep_id, $varExisting);

        foreach ($newEntries as $depId) {
            $item = new erLhAbstractModelSubjectDepartment();
            $item->dep_id = $depId;
            $item->subject_id = $this->id;
            $item->saveThis();
        }

        if (empty($this->dep_id)) {
            $item = new erLhAbstractModelSubjectDepartment();
            $item->dep_id = 0;
            $item->subject_id = $this->id;
            $item->saveThis();
        }
    }

    public function __get($var)
    {
        switch ($var) {

            case 'dep_id_objects':
                    $objects = erLhAbstractModelSubjectDepartment::getList(array('filter' => array('subject_id' => $this->id)));
                    $this->dep_id_objects = array();
                    foreach ($objects as $object) {
                        $this->dep_id_objects[$object->dep_id] = $object;
                    }
                    return $this->dep_id_objects;
                break;

            case 'dep_id':
                    $this->dep_id = array_keys($this->dep_id_objects);
                    return $this->dep_id;
                break;

            default:
                ;
                break;
        }
    }

    public function getFilter() {

        $filter = array();

        // Global filters
        $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter(false, 'dep_id');

        if (!empty($departmentFilter)){

            $subjects = erLhAbstractModelSubjectDepartment::getCount($departmentFilter,'',false,'distinct subject_id', false, true, true);

            if (empty($subjects)) {
                $filter['filterin']['id'] = array(-1);
            } else {
                $filter['filterin']['id'] = $subjects;
            }
        }

        return $filter;

    }


    public $id = null;
    public $name = '';
}