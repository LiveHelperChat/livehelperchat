<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/newgroup.tpl.php');
$Departament_group = new erLhcoreClassModelDepartamentGroup();

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/group');
    exit;
}

if (isset($_POST['Save_departament']))
{
    $Errors = erLhcoreClassDepartament::validateDepartmentGroup($Departament_group);

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $Errors[] = 'Invalid CSRF token!';
    }

    if (count($Errors) == 0)
    {
        $db = ezcDbInstance::get();
        $db->beginTransaction();

        try {

            $Departament_group->saveThis();

            erLhcoreClassDepartament::validateDepartmentGroupDepartments($Departament_group);

            erLhcoreClassAdminChatValidatorHelper::clearUsersCache();

            erLhcoreClassChatStatsResque::updateDepartmentGroupStats($Departament_group);

            $db->commit();

            erLhcoreClassModule::redirect('department/group');
            exit ;
        } catch (Exception $e) {
            $db->rollback();
            $tpl->set('errors',[$e->getMessage()]);
        }

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('departament_group',$Departament_group);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('department/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')),
array('url' => erLhcoreClassDesign::baseurl('department/group'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Departments groups')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department group')));

?>