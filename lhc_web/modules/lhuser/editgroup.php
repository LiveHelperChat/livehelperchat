<?php

$tpl = new erLhcoreClassTemplate('lhuser/editgroup.tpl.php');

$Group = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelGroup', (int)$Params['user_parameters']['group_id'] );

if (isset($_POST['Update_group']) )
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
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Please enter group name');
    }
    
    if (count($Errors) == 0)
    {     
        $Group->name = $form->Name;
    
        erLhcoreClassUser::getSession()->update($Group);
                     
        erLhcoreClassModule::redirect('user/grouplist');
        
        return ;
        
    }  else {
        $tpl->set('errArr',$Errors);
    }
}

if (isset($_POST['AssignUsers']) && isset($_POST['UserID']) && count($_POST['UserID']) > 0)
{
    foreach ($_POST['UserID'] as $UserID)
    {                
        $GroupUser = new erLhcoreClassModelGroupUser();        
        $GroupUser->group_id = $Group->id;
        $GroupUser->user_id = $UserID;        
        erLhcoreClassUser::getSession()->save($GroupUser);
    }
}

if (isset($_POST['AssignRoles']) && isset($_POST['RoleID']) && count($_POST['RoleID']) > 0)
{
    foreach ($_POST['RoleID'] as $RoleID)
    {                
        $GroupRole = new erLhcoreClassModelGroupRole();        
        $GroupRole->group_id = $Group->id;
        $GroupRole->role_id = $RoleID;        
        erLhcoreClassRole::getSession()->save($GroupRole);
    }
}

if (isset($_POST['Remove_user_from_group']) && isset($_POST['AssignedID']) && count($_POST['AssignedID']) > 0)
{
    foreach ($_POST['AssignedID'] as $AssignedID)
    {                
        erLhcoreClassGroupUser::deleteGroupUser($AssignedID);
    }
}

if (isset($_POST['Remove_role_from_group']) && isset($_POST['AssignedID']) && count($_POST['AssignedID']) > 0)
{
    foreach ($_POST['AssignedID'] as $AssignedID)
    {                
        erLhcoreClassGroupRole::deleteGroupRole($AssignedID);
    }
}



if (isset($_GET['adduser']))
{
    $tpl->set('adduser','true');
}

$tpl->set('group',$Group);

$Result['content'] = $tpl->fetch();


$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','System configuration')),

array('url' => erLhcoreClassDesign::baseurl('user/grouplist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Groups')),

array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Group edit').' - '.$Group->name)
)

?>