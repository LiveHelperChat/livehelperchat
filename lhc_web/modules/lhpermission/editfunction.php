<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpermission/editfunction.tpl.php');

$Function = erLhcoreClassModelRoleFunction::fetch((int)$Params['user_parameters']['function_id']);

if (isset($_POST['Cancel_action']))
{
    erLhcoreClassModule::redirect('permission/editrole' ,'/' . $Function->role_id);
    exit ;
}

if (ezcInputForm::hasPostData())
{
    $definition = array(
        'Limitation' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect();
        exit;
    }

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    $Function->limitation = $form->Limitation;
    $Function->saveThis();

    if (isset($_POST['Update_action'])) {
        $tpl->set('updated',true);
    } else {
        erLhcoreClassModule::redirect('permission/editrole' ,'/' . $Function->role_id);
        exit ;
    }
}

$tpl->set('function',$Function);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','System configuration')),
    array('url'=>erLhcoreClassDesign::baseurl('permission/roles'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','List of roles')),
    array('url'=>erLhcoreClassDesign::baseurl('permission/editrole') .'/' . $Function->role_id,'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Edit role')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Edit function')),

);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('permission.editrole_path', array('result' => & $Result));

?>