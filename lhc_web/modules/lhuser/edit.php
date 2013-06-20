<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/edit.tpl.php');

$UserData = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUser', (int)$Params['user_parameters']['user_id'] );

if (isset($_POST['Update_account']) || isset($_POST['Save_account']))
{    
   $definition = array(
        'Password' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Password1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),    
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
        ),
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Surname' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),  
        'Username' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),    		
		'UserDisabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
		),
		'HideMyStatus' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
		),    		
		'DefaultGroup' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int',
				null,
				FILTER_REQUIRE_ARRAY
		)
    );
  
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
        
    if ( !$form->hasValidData( 'Username' ) ) {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Please enter a username!');
    }  elseif ( $form->hasValidData( 'Username' ) && $form->Username != $UserData->username && !erLhcoreClassModelUser::userExists($form->Username) ) {
    	$UserData->username = $form->Username;
    } elseif ( $form->hasValidData( 'Username' ) && $form->Username != $UserData->username) {
    	$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User exists!');
    }
    
    if ( !$form->hasValidData( 'Email' ) )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Wrong email address');
    } 
    
    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Please enter a name');
    }
    
    if ( !$form->hasValidData( 'Surname' ) || $form->Surname == '')
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Please enter a surname');
    }
    
    if ( $form->hasInputField( 'Password' ) && (!$form->hasInputField( 'Password1' ) || $form->Password != $form->Password1  ) ) // check for optional field
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Passwords mismatch');
    }
    
    if ( $form->hasValidData( 'DefaultGroup' ) ) {
    	$UserData->user_groups_id = $form->DefaultGroup;
    } else {
    	$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Please choose a default user group');
    }
        
    if ( $form->hasValidData( 'UserDisabled' ) && $form->UserDisabled == true )
    {
    	$UserData->disabled = 1;
    } else {
    	$UserData->disabled = 0;
    }   

    if ( $form->hasValidData( 'HideMyStatus' ) && $form->HideMyStatus == true )
    {
    	$UserData->hide_online = 1;
    } else {
    	$UserData->hide_online = 0;
    }

    if (count($Errors) == 0)
    {     
        // Update password if neccesary
        if ($form->hasInputField( 'Password' ) && $form->hasInputField( 'Password1' ) && $form->Password != '')
        {
            $UserData->setPassword($form->Password);
        }

        $UserData->email   = $form->Email;
        $UserData->name    = $form->Name;
        $UserData->surname = $form->Surname;

        erLhcoreClassUser::getSession()->update($UserData);

        erLhcoreClassUserDep::setHideOnlineStatus($UserData);

        erLhcoreClassModelGroupUser::removeUserFromGroups($UserData->id);

        foreach ($UserData->user_groups_id as $group_id) {
        	$groupUser = new erLhcoreClassModelGroupUser();
        	$groupUser->group_id = $group_id;
        	$groupUser->user_id = $UserData->id;
        	$groupUser->saveThis();
        }

        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();

        if (isset($_POST['Save_account'])) {        
            erLhcoreClassModule::redirect('user/userlist');
            exit;
        } else {
            $tpl->set('updated',true);
        }
                
    }  else {
        $tpl->set('errors',$Errors);
    }
}

if (isset($_POST['UpdateDepartaments_account']))
{        
   $globalDepartament = array();
   if (isset($_POST['all_departments']) && $_POST['all_departments'] == 'on') {
       $UserData->all_departments = 1;
       $globalDepartament[] = 0;
   } else {
       $UserData->all_departments = 0;
   }    

   erLhcoreClassUser::getSession()->update($UserData);
   
   if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0)
   {
       $globalDepartament = array_merge($_POST['UserDepartament'],$globalDepartament);       
   }
      
   if (count($globalDepartament) > 0)
   {
       erLhcoreClassUserDep::addUserDepartaments($globalDepartament,$Params['user_parameters']['user_id'],$UserData);
   } else {
       erLhcoreClassUserDep::addUserDepartaments(array(),$Params['user_parameters']['user_id'],$UserData);
   }
   
   $tpl->set('account_updated_departaments','done');
}


$tpl->set('user',$UserData);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Users')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User edit').' - '.$UserData->name.' '.$UserData->surname));

?>