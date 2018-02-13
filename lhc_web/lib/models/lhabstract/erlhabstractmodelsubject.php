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
        $currentUser = erLhcoreClassUser::instance();
        $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
        return include ('lib/core/lhabstract/fields/erlhabstractmodelsubject.php');
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

    public function __get($var)
    {
        switch ($var) {

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $name = '';
}