<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartament/new.tpl.php');
$Departament = new erLhcoreClassModelDepartament();

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('departament/departaments');
    exit;
}

if (isset($_POST['Save_departament']))
{
   $definition = array(
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Priority' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Please enter a department name');
    }

    if ( $form->hasValidData( 'Email' ) ) {

    	$partsEmail = explode(',', $form->Email);
    	$validatedEmail = array();
    	foreach ($partsEmail as $email){
    		if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)){
    			$validatedEmail[] = trim($email);
    		}
    	}

        $Departament->email = implode(',', $validatedEmail);

    } else {
    	$Departament->email = '';
    }

    if ( $form->hasValidData( 'Priority' ) ) {
    	$Departament->priority = $form->Priority;
    } else {
    	$Departament->priority = 0;
    }

    if (count($Errors) == 0)
    {
        $Departament->name = $form->Name;
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