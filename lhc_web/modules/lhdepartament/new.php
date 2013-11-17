<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartament/new.tpl.php');
$Departament = new erLhcoreClassModelDepartament();

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('departament/departaments');
    exit;
}

if (isset($_POST['Save_departament']))
{
    $Errors = erLhcoreClassDepartament::validateDepartment($Departament);
    
    if (count($Errors) == 0)
    {
        erLhcoreClassDepartament::getSession()->save($Departament);
        erLhcoreClassModule::redirect('departament/departaments');
        exit ;

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('departament',$Departament);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('departament/departaments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Departments')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department')),
)

?>