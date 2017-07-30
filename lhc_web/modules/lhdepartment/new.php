<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/new.tpl.php');
$Departament = new erLhcoreClassModelDepartament();

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/departments');
    exit;
}

if (isset($_POST['Save_departament']))
{
    $Errors = erLhcoreClassDepartament::validateDepartment($Departament);
    
    if (count($Errors) == 0)
    {
        erLhcoreClassDepartament::getSession()->save($Departament);

        erLhcoreClassDepartament::validateDepartmentCustomWorkHours($Departament);
        
        erLhcoreClassDepartament::validateDepartmentProducts($Departament);
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('department.modified',array('department' => $Departament));
        
        erLhcoreClassModule::redirect('department/departments');
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('departament',$Departament);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.customdepartmentperiodgenerator.js').'"></script>';
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('department/departments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Departments')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department')),
)

?>