<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/newgroup.tpl.php');

$GroupData = new erLhcoreClassModelGroup();

if (isset($_POST['Save_group']) || isset($_POST['Save_group_and_assign_user']))
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

    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Please enter a group name');
    }

    if ( $form->hasValidData( 'Disabled' ) && $form->Disabled == true ) {
        $GroupData->disabled = 1;
    } else {
        $GroupData->disabled = 0;
    }

    if (count($Errors) == 0)
    {  
        $GroupData->name    = $form->Name;

        erLhcoreClassUser::getSession()->save($GroupData);

        if ($form->hasValidData('MemberGroup') && !empty($form->MemberGroup)) {
            erLhcoreClassGroupRole::assignGroupMembers($GroupData, $form->MemberGroup);
        }

        if (isset($_POST['Save_group_and_assign_user'])) {
            erLhcoreClassModule::redirect('user/editgroup/' . $GroupData->id . '/?adduser=1');
        } else {
            erLhcoreClassModule::redirect('user/grouplist');
        }

        exit;
        
    }  else {
                
        $GroupData->name = $form->Name;
        
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('group',$GroupData);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('user/grouplist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Groups')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','New group'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.newgroup_path', array('result' => & $Result));
?>