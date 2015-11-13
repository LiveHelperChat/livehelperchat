<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpermission/newrole.tpl.php');

$Role = new erLhcoreClassModelRole();

if (isset($_POST['Save_role']) ||isset($_POST['New_policy']) )
{
   $definition = array(
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
   		erLhcoreClassModule::redirect();
   		exit;
    }

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','Please enter role name');
    }

    if (count($Errors) == 0)
    {
        $Role->name = $form->Name;

        erLhcoreClassRole::getSession()->save($Role);

        if (isset($_POST['New_policy']))
            erLhcoreClassModule::redirect('permission/editrole/' . $Role->id .'/?newPolicy=1' );
        else
            erLhcoreClassModule::redirect('permission/roles' );
       exit;

    }  else {
        $tpl->set('errors',$Errors);
    }
}

if (isset($_POST['Cancel_role']))
{
    erLhcoreClassModule::redirect('permission/roles' );
    exit;
}

$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','System configuration')),
array('url'=>erLhcoreClassDesign::baseurl('permission/roles'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','List of roles')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/newrole','New role'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('permission.newrole_path', array('result' => & $Result));

?>