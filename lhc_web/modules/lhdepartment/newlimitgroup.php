<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/newlimitgroup.tpl.php');
$Departament_group = new erLhcoreClassModelDepartamentLimitGroup();

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/limitgroup');
    exit;
}

if (isset($_POST['Save_departament']))
{
    $Errors = erLhcoreClassDepartament::validateDepartmentLimitGroup($Departament_group);
    
    if (count($Errors) == 0)
    {
        $Departament_group->saveThis();
        
        erLhcoreClassDepartament::validateDepartmentGroupLimitDepartments($Departament_group);
        
        $Departament_group->updateDepartmentsLimits();
        
        erLhcoreClassModule::redirect('department/limitgroup');
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('departament_group',$Departament_group);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('department/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')),
array('url' => erLhcoreClassDesign::baseurl('department/limitgroup'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Departments limit groups')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department limit group')));

?>