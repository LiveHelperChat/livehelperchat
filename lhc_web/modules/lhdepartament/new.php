<?php

$tpl = new erLhcoreClassTemplate( 'lhdepartament/new.tpl.php');

if (isset($_POST['Save_departament']))
{    
   $definition = array(
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        )       
    );
    
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Please enter department name');
    }
    
    if (count($Errors) == 0)
    {     
        
        $Departament = new erLhcoreClassModelDepartament();
        $Departament->name = $form->Name;
    
        erLhcoreClassDepartament::getSession()->save($Departament);
       
        erLhcoreClassModule::redirect('departament/departaments');
        return ;
        
    }  else {
        $tpl->set('errArr',$Errors);
    }
}

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('departament/departaments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Departments')),

array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New department')),
)

?>