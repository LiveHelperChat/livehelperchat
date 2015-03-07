<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdepartament/edit.tpl.php');

$Departament = erLhcoreClassDepartament::getSession()->load( 'erLhcoreClassModelDepartament', (int)$Params['user_parameters']['departament_id'] );

$userDepartments = true;

/**
 * Append user departments filter
* */
if ($currentUser->hasAccessTo('lhdepartment','manageall') !== true)
{
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
    if ($userDepartments !== true) {
    	if (!in_array($Departament->id, $userDepartments)) {
    		erLhcoreClassModule::redirect('departament/departaments');
    		exit;
    	}
    }
}

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('departament/departaments');
    exit;
}

if ( isset($_POST['Delete_departament']) ) {

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token']) || !$currentUser->hasAccessTo('lhdepartament','delete') || !$Departament->can_delete) {
		erLhcoreClassModule::redirect('departament/departaments');
		exit;
	}

	$Departament->removeThis();
    erLhcoreClassModule::redirect('departament/departaments');
    exit;
}


if (isset($_POST['Update_departament']) || isset($_POST['Save_departament'])  )
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('departament/departaments');
		exit;
	}
	
	$Errors = erLhcoreClassDepartament::validateDepartment($Departament);
	
    if (count($Errors) == 0)
    {    	
        erLhcoreClassDepartament::getSession()->update($Departament);

        if (isset($_POST['Save_departament'])) {
            erLhcoreClassModule::redirect('departament/departaments');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('departament',$Departament);
$tpl->set('currentUser',$currentUser);
$tpl->set('limitDepartments',$userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array());

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('departament/departaments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','departments')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit a department').' - '.$Departament->name),);

?>