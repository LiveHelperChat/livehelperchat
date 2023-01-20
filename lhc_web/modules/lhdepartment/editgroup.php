<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdepartment/editgroup.tpl.php');

$Departament_group = erLhcoreClassModelDepartamentGroup::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/group');
    exit;
}

if ( isset($_POST['Delete_departament']) ) {

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('department/group');
		exit;
	}

	$Departament_group->removeThis();
    erLhcoreClassModule::redirect('department/group');
    exit;
}

if ($Params['user_parameters_unordered']['action'] == 'operators') {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/operators_group.tpl.php');
    $tpl->set('department_group', $Departament_group);
    $tpl->set('group_op', isset($_GET['group']) && $_GET['group'] === 'true' ? true : (isset($_GET['group']) ? false : null));
    $tpl->set('only_online', isset($_GET['only_online']) && $_GET['only_online'] === 'true' ? true : (isset($_GET['only_online']) ? false : null));
    $tpl->set('only_logged', isset($_GET['only_logged']) && $_GET['only_logged'] === 'true' ? true : (isset($_GET['only_logged']) ? false : null));
    $tpl->set('only_offline', isset($_GET['only_offline']) && $_GET['only_offline'] === 'true' ? true : (isset($_GET['only_offline']) ? false : null));
    echo $tpl->fetch();
    exit;
}

if ($Params['user_parameters_unordered']['action'] == 'updatestats') {
    erLhcoreClassChatStatsResque::updateDepartmentGroupStats($Departament_group);
    erLhcoreClassModule::redirect('department/group');
    exit;
}

if (isset($_POST['Update_departament']) || isset($_POST['Save_departament'])  )
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('department/group');
		exit;
	}
	
	$Errors = erLhcoreClassDepartament::validateDepartmentGroup($Departament_group);
	
    if (count($Errors) == 0)
    {    	
        $Departament_group->updateThis();

        erLhcoreClassDepartament::validateDepartmentGroupDepartments($Departament_group);

        erLhcoreClassAdminChatValidatorHelper::clearUsersCache();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('department.edit_department_group',array('department_group' => & $Departament_group));

        erLhcoreClassChatStatsResque::updateDepartmentGroupStats($Departament_group);

        if (isset($_POST['Save_departament'])) {
            erLhcoreClassModule::redirect('department/group');
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
array('url' => erLhcoreClassDesign::baseurl('department/group'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Departments groups')),
array('title' => $Departament_group->name));

?>