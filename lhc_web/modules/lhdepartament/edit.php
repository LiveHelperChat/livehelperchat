<?php

$tpl = new erLhcoreClassTemplate( 'lhdepartament/edit.tpl.php');

$Departament = erLhcoreClassDepartament::getSession()->load( 'erLhcoreClassModelDepartament', (int)$Params['user_parameters']['departament_id'] );




if (isset($_POST['Update_departament']))
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
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter departament name');
    }
    
    if (count($Errors) == 0)
    {     
        $Departament->name = $form->Name;
    
        erLhcoreClassDepartament::getSession()->update($Departament);
       
        erLhcoreClassModule::redirect('departament/departaments');
        return ;
        
    }  else {
        $tpl->set('errArr',$Errors);
    }
}

$tpl->set('departament',$Departament);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),

array('url' => erLhcoreClassDesign::baseurl('departament/departaments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','departments')),

array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit department').' - '.$Departament->name),
)


?>