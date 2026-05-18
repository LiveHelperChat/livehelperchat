<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/new.tpl.php');
$Departament = new erLhcoreClassModelDepartament();

$userDepartments = true;

/**
 * Append user departments filter
 * */
if ($currentUser->hasAccessTo('lhdepartment','manageall') !== true)
{
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
}

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/departments');
    exit;
}

if (isset($_POST['Save_departament']) || isset($_POST['Update_departament']))
{
    $Errors = erLhcoreClassDepartament::validateDepartment($Departament);

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $Errors[] = 'Invalid CSRF token!';
    }

    if (count($Errors) == 0)
    {
        erLhcoreClassDepartament::getSession()->save($Departament);

        erLhcoreClassDepartament::validateDepartmentCustomWorkHours($Departament);
        
        erLhcoreClassDepartament::validateDepartmentProducts($Departament);
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('department.modified',['department' => $Departament]);

        erLhcoreClassAdminChatValidatorHelper::clearUsersCache();
        
        if (isset($_POST['Update_departament'])) {
            erLhcoreClassModule::redirect('department/edit','/' . $Departament->id);
        } else {
            erLhcoreClassModule::redirect('department/departments');
        }
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('departament',$Departament);
$tpl->set('departamentCustomWorkHours', json_encode([], JSON_HEX_APOS));
$tpl->set('limitDepartments',$userDepartments !== true ? ['filterin' => ['id' => $userDepartments]] : []);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/lhc.customdepartmentperiodgenerator.js').'"></script>';

$Result['path'] = [
    ['url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new', 'System configuration')],
    ['url' => erLhcoreClassDesign::baseurl('department/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Departments')],
    ['url' => erLhcoreClassDesign::baseurl('department/departments'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Departments list')],
    ['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new', 'New department')],
];
