<?php

$tpl = new erLhcoreClassTemplate( 'lhpermission/editrole.tpl.php');

$Role = erLhcoreClassRole::getSession()->load( 'erLhcoreClassModelRole', (int)$Params['user_parameters']['role_id'] );

if (isset($_POST['Cancel_role']))
{
    erLhcoreClassModule::redirect('permission/roles' );
    return ;
}

if (isset($_POST['Update_role']))
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
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Please enter role name');
    }
    
    if (count($Errors) == 0)
    {     
        $Role->name = $form->Name;
    
    
        
        erLhcoreClassRole::getSession()->update($Role);
       
        erLhcoreClassModule::redirect('permission/roles');
        return ;
        
    }  else {
        $tpl->set('errArr',$Errors);
    }
}

$tpl->set('role',$Role);

if (isset($_POST['New_policy']) || isset($_GET['newPolicy']))
{
    $tpl->setFile( 'lhpermission/newpolicy.tpl.php');
}


if (isset($_POST['Store_policy']))
{
    $definition = array(
        'Module' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        ),
        'ModuleFunction' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        )       
    );
    
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( !$form->hasValidData( 'Module' ) || $form->Module == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Please choose module');
    }
    
    if ( !$form->hasValidData( 'ModuleFunction' ) || $form->ModuleFunction == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Please choose module function');
    }
    
    if (count($Errors) == 0)
    {
        $RoleFunction = new erLhcoreClassModelRoleFunction();
        $RoleFunction->role_id = $Role->id;
        $RoleFunction->module = $form->Module;
        $RoleFunction->function = $form->ModuleFunction;
        
        erLhcoreClassRole::getSession()->save($RoleFunction);
    } else {
         $tpl->setFile( 'lhpermission/newpolicy.tpl.php');
    }
}

if (isset($_POST['Delete_policy']))
{
    if (isset($_POST['PolicyID']) && count($_POST['PolicyID']) > 0)
    {
        foreach ($_POST['PolicyID'] as $PolicyID)
        {
            erLhcoreClassRoleFunction::deleteRolePolicy($PolicyID);
        }
    }
}

if (isset($_POST['Remove_group_from_role']) && isset($_POST['AssignedID']) && count($_POST['AssignedID']) > 0)
{
    foreach ($_POST['AssignedID'] as $AssignedID)
    {                
        erLhcoreClassGroupRole::deleteGroupRole($AssignedID);
    }
}

if (isset($_POST['AssignGroups']) && isset($_POST['GroupID']) && count($_POST['GroupID']) > 0)
{
    foreach ($_POST['GroupID'] as $GroupID)
    {                
        $GroupRole = new erLhcoreClassModelGroupRole();        
        $GroupRole->group_id =$GroupID;
        $GroupRole->role_id = $Role->id;;        
        erLhcoreClassRole::getSession()->save($GroupRole);
    }
}


$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','System configuration')),
array('url'=>erLhcoreClassDesign::baseurl('permission/roles'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','List of roles')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/editrole','Role edit').' - '.$Role->name)
)

?>