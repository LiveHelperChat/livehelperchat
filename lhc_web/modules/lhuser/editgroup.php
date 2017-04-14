<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/editgroup.tpl.php');

$Group = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelGroup', (int)$Params['user_parameters']['group_id'] );

if (isset($_POST['Update_group']) )
{
   $definition = array(
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'Disabled' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'MemberGroup' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY
        )
    );
   
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
    	erLhcoreClassModule::redirect('user/userlist');
    	exit;
    }

    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Please enter a group name');
    }

    if ( $form->hasValidData( 'Disabled' ) && $form->Disabled == true ) {
        $Group->disabled = 1;
    } else {
        $Group->disabled = 0;
    }

    if (count($Errors) == 0)
    {
        $Group->name = $form->Name;

        if ($form->hasValidData('MemberGroup') && !empty($form->MemberGroup)) {
            erLhcoreClassGroupRole::assignGroupMembers($Group, $form->MemberGroup);
        }
        
        erLhcoreClassUser::getSession()->update($Group);
        
        erLhcoreClassModule::redirect('user/grouplist');
        exit;

    }  else {
        $tpl->set('errors',$Errors);
    }
}

if (isset($_POST['AssignRoles']) && isset($_POST['RoleID']) && count($_POST['RoleID']) > 0)
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/userlist');
		exit;
	}

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
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/userlist');
		exit;
	}

    foreach ($_POST['AssignedID'] as $AssignedID)
    {
        $group_user = erLhcoreClassModelGroupUser::fetch($AssignedID);
        $group_user->removeThis();
    }
}

if (isset($_POST['Remove_role_from_group']) && isset($_POST['AssignedID']) && count($_POST['AssignedID']) > 0)
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/userlist');
		exit;
	}

    foreach ($_POST['AssignedID'] as $AssignedID)
    {
        erLhcoreClassGroupRole::deleteGroupRole($AssignedID);
    }
}

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelGroupUser::getCount(array('filter' => array('group_id' => $Group->id)));
$pages->setItemsPerPage(20);
$pages->serverURL = erLhcoreClassDesign::baseurl('user/editgroup').'/'.$Group->id;
$pages->paginate();

$tpl->set('pages',$pages);


if ($pages->items_total > 0) {
    $tpl->set('users',erLhcoreClassModelGroupUser::getList(array('filter' => array('group_id' => $Group->id),'offset' => $pages->low, 'limit' => $pages->items_per_page )));
} else {
    $tpl->set('users',array());
}


if (isset($_GET['adduser']))
{
    $tpl->set('adduser','true');
}

$tpl->set('group',$Group);
$tpl->set('group_work',erLhcoreClassModelGroupWork::getList(array('filter' => array('group_id' => $Group->id))));


$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('user/grouplist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Groups')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Group edit').' - '.$Group->name)
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.editgroup_path',array('result' => & $Result));
?>