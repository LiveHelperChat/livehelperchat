<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/new.tpl.php');
$Departament = new erLhcoreClassModelDepartament();

$userDepartments = true;

/**
 * Append user departments filter
 * */
if ($currentUser->hasAccessTo('lhdepartment','manageall') !== true)
{
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
}

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/departments');
    exit;
}

if (isset($_POST['Save_departament']) || isset($_POST['Update_departament']))
{
    $Errors = erLhcoreClassDepartament::validateDepartment($Departament);
    
    if (count($Errors) == 0)
    {
        erLhcoreClassDepartament::getSession()->save($Departament);

        erLhcoreClassDepartament::validateDepartmentCustomWorkHours($Departament);
        
        erLhcoreClassDepartament::validateDepartmentProducts($Departament);
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('department.modified',array('department' => $Departament));

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
$tpl->set('departamentCustomWorkHours', json_encode(array(), JSON_HEX_APOS));
$tpl->set('limitDepartments',$userDepartments !== true ? array('filterin' => array('id' => $userDepartments)) : array());


$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.customdepartmentperiodgenerator.js').'"></script>';
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('department/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')),
array('url' => erLhcoreClassDesign::baseurl('department/departments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments list')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department')),
)

?>