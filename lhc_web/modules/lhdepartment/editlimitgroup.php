<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdepartment/editlimitgroup.tpl.php');

$Departament_group = erLhcoreClassModelDepartamentLimitGroup::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/limitgroup');
    exit;
}

if ( isset($_POST['Delete_departament']) ) {

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('department/limitgroup');
		exit;
	}

	$Departament_group->removeThis();
    erLhcoreClassModule::redirect('department/limitgroup');
    exit;
}

if (isset($_POST['Update_departament']) || isset($_POST['Save_departament'])  )
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('department/limitgroup');
		exit;
	}
	
	$Errors = erLhcoreClassDepartament::validateDepartmentLimitGroup($Departament_group);
	
    if (count($Errors) == 0)
    {    	
        $Departament_group->updateThis();

        erLhcoreClassDepartament::validateDepartmentGroupLimitDepartments($Departament_group);
        
        $Departament_group->updateDepartmentsLimits();
        
        if (isset($_POST['Save_departament'])) {
            erLhcoreClassModule::redirect('department/limitgroup');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('departament_group',$Departament_group);
$tpl->set('currentUser',$currentUser);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('department/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')),
array('url' => erLhcoreClassDesign::baseurl('department/limitgroup'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Departments limit groups')),
array('title' => $Departament_group->name));

?>