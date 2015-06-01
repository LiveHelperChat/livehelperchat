<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/account.tpl.php' );

$currentUser = erLhcoreClassUser::instance();
$UserData = $currentUser->getUserData();

$tpl->set('tab',$Params['user_parameters_unordered']['tab'] == 'canned' ? 'tab_canned' : '');

if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','allowtochoosependingmode') && isset($_POST['UpdatePending_account']))
{	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}
	
	$definition = array(
			'showAllPendingEnabled' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			)
	);
	
	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'showAllPendingEnabled' ) && $form->showAllPendingEnabled == true )
	{
		erLhcoreClassModelUserSetting::setSetting('show_all_pending',1);
	} else {
		erLhcoreClassModelUserSetting::setSetting('show_all_pending',0);
	}
	
	$tpl->set('account_updated','done');
	$tpl->set('tab','tab_pending');
}

if (erLhcoreClassUser::instance()->hasAccessTo('lhspeech','changedefaultlanguage') && isset($_POST['UpdateSpeech_account']))
{	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}
	
	$definition = array(
			'select_language' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
			),
			'select_dialect' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
	);
	
	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();
	
	if ( $form->hasValidData( 'select_language' ) )	{	  
	    erLhcoreClassModelUserSetting::setSetting('speech_language',$form->select_language);
	} else {
	    erLhcoreClassModelUserSetting::setSetting('speech_language','');
	}
	
	if ( $form->hasValidData( 'select_dialect' ) && $form->hasValidData( 'select_dialect' ) != '0' )	{
	    erLhcoreClassModelUserSetting::setSetting('speech_dialect',$form->select_dialect);
	} else {
	   erLhcoreClassModelUserSetting::setSetting('speech_dialect','');
	}

	$tpl->set('account_updated','done');
	$tpl->set('tab','tab_speech');
}

if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','change_visibility_list') && isset($_POST['UpdateTabsSettings_account']))
{
	$definition = array(
			'pendingTabEnabled' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'activeTabEnabled' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'closedTabEnabled' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'unreadTabEnabled' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			)
	);

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'pendingTabEnabled' ) && $form->pendingTabEnabled == true )
	{
		erLhcoreClassModelUserSetting::setSetting('enable_pending_list',1);
	} else {
		erLhcoreClassModelUserSetting::setSetting('enable_pending_list',0);
	}

	if ( $form->hasValidData( 'activeTabEnabled' ) && $form->activeTabEnabled == true )
	{
		erLhcoreClassModelUserSetting::setSetting('enable_active_list',1);
	} else {
		erLhcoreClassModelUserSetting::setSetting('enable_active_list',0);
	}

	if ( $form->hasValidData( 'closedTabEnabled' ) && $form->closedTabEnabled == true )
	{
		erLhcoreClassModelUserSetting::setSetting('enable_close_list',1);
	} else {
		erLhcoreClassModelUserSetting::setSetting('enable_close_list',0);
	}

	if ( $form->hasValidData( 'unreadTabEnabled' ) && $form->unreadTabEnabled == true )
	{
		erLhcoreClassModelUserSetting::setSetting('enable_unread_list',1);
	} else {
		erLhcoreClassModelUserSetting::setSetting('enable_unread_list',0);
	}

	$tpl->set('account_updated','done');
	$tpl->set('tab','tab_settings');
}


if (isset($_POST['Update']))
{
   $definition = array(
        'Password' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Password1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
        ),
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'Surname' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'Username' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'JobTitle' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Skype' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'XMPPUsername' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
   		'UserTimeZone' => new ezcInputFormDefinitionElement(
   				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   		),
   		'UserInvisible' => new ezcInputFormDefinitionElement(
   				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   		),
   		'ReceivePermissionRequest' => new ezcInputFormDefinitionElement(
   				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   		)
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
   		erLhcoreClassModule::redirect('user/account');
   		exit;
    }

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'Username' ) ) {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Please enter a username!');
    }  elseif ( $form->hasValidData( 'Username' ) && $form->Username != $UserData->username && !erLhcoreClassModelUser::userExists($form->Username) ) {
    	$UserData->username = $form->Username;
    } elseif ( $form->hasValidData( 'Username' ) && $form->Username != $UserData->username) {
    	$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','User exists!');
    }
    
    if ( $form->hasValidData( 'UserTimeZone' ) && $form->UserTimeZone != '')
    {
    	$UserData->time_zone = $form->UserTimeZone;
    	CSCacheAPC::getMem()->setSession('lhc_user_timezone',$UserData->time_zone,true);
    } else {
    	CSCacheAPC::getMem()->setSession('lhc_user_timezone','',true);
    	$UserData->time_zone = '';
    }
    
    if ( !$form->hasValidData( 'Email' ) )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Wrong email address');
    }

    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Please enter a name');
    }
        
    if ( $form->hasValidData( 'Surname' ) && $form->Surname != '')
    {
    	$UserData->surname = $form->Surname;
    } else {
    	$UserData->surname = '';
    }
        
    if ( $form->hasValidData( 'JobTitle' ) && $form->JobTitle != '')
    {
    	$UserData->job_title = $form->JobTitle;
    } else {
    	$UserData->job_title = '';
    }

    if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','changeskypenick') ) {
	    if ( $form->hasValidData( 'Skype' ) && $form->Skype != '')
	    {
	    	$UserData->skype = $form->Skype;
	    } else {
	    	$UserData->skype = '';
	    }
    }
    
    if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','changevisibility') ) {
	    if ( $form->hasValidData( 'UserInvisible' ) && $form->UserInvisible == true ) {
	    	$UserData->invisible_mode = 1;
	    } else {
	    	$UserData->invisible_mode = 0;
	    }
    }
    
    if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','receivepermissionrequest') ) {
	    if ( $form->hasValidData( 'ReceivePermissionRequest' ) && $form->ReceivePermissionRequest == true ) {
	    	$UserData->rec_per_req = 1;
	    } else {
	    	$UserData->rec_per_req = 0;
	    }
    }
    
    if ( $form->hasValidData( 'XMPPUsername' ) && $form->XMPPUsername != '')
    {
    	$UserData->xmpp_username = $form->XMPPUsername;
    } else {
    	$UserData->xmpp_username = '';
    }
        
    if ( $form->hasInputField( 'Password' ) && (!$form->hasInputField( 'Password1' ) || $form->Password != $form->Password1  ) ) // check for optional field
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Passwords mismatch');
    }

    if ( isset($_POST['DeletePhoto']) ) {
    	$UserData->removeFile();
    }

    if ( isset($_FILES["UserPhoto"]) && is_uploaded_file($_FILES["UserPhoto"]["tmp_name"]) && $_FILES["UserPhoto"]["error"] == 0 && erLhcoreClassImageConverter::isPhoto('UserPhoto') ) {
    	$UserData->removeFile();

    	$dir = 'var/userphoto/' . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $UserData->id . '/';
    	
    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_path',array('dir' => & $dir,'storage_id' => $UserData->id));
    	
    	$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_store', array('file_post_variable' => 'UserPhoto', 'dir' => & $dir, 'storage_id' => $UserData->id));
    	
    	// There was no callbacks
    	if ($response === false) {
    		erLhcoreClassFileUpload::mkdirRecursive( $dir );
    		$file = qqFileUploader::upload($_FILES,'UserPhoto',$dir);
    	} else {
    		$file = $response['data'];
    	}
    	
    	if ( !empty($file["errors"]) ) {
    		foreach ($file["errors"] as $err) {
    			$Errors[] = $err;
    		}
    	} else {
    		$UserData->removeFile();
    		$UserData->filename           = $file["data"]["filename"];
    		$UserData->filepath           = $file["data"]["dir"];
    		
    		$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_resize_150', array('mime_type' => $file["data"]['mime_type'],'user' => $UserData));
    		
    		if ($response === false) {
    			erLhcoreClassImageConverter::getInstance()->converter->transform( 'photow_150', $UserData->file_path_server, $UserData->file_path_server );
    			chmod($UserData->file_path_server, 0644);
    		}    		
    	}
    }

    if (count($Errors) == 0)
    {
        // Update password if neccesary
        if ( $form->hasInputField( 'Password' ) && $form->hasInputField( 'Password1' ) && $form->Password != '' )
        {
            $UserData->setPassword($form->Password);
        }

        $UserData->email   = $form->Email;
        $UserData->name    = $form->Name;
        $UserData->surname = $form->Surname;

        erLhcoreClassUser::getSession()->update($UserData);
        $tpl->set('account_updated','done');

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$currentUser = erLhcoreClassUser::instance();

$allowEditDepartaments = $currentUser->hasAccessTo('lhuser','editdepartaments');

if ($allowEditDepartaments && isset($_POST['UpdateDepartaments_account']))
{

   if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
   }

   $globalDepartament = array();
   if (isset($_POST['all_departments']) && $_POST['all_departments'] == 'on') {
       $UserData->all_departments = 1;
       $globalDepartament[] = 0;
   } else {
       $UserData->all_departments = 0;
   }

   if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0)
   {
       $globalDepartament = array_merge($_POST['UserDepartament'],$globalDepartament);
   }
      
   $UserData->departments_ids = implode(',', $globalDepartament);
   erLhcoreClassUser::getSession()->update($UserData);
      
   if (count($globalDepartament) > 0) {
       erLhcoreClassUserDep::addUserDepartaments($globalDepartament,false,$UserData);
   } else {
       erLhcoreClassUserDep::addUserDepartaments(array(),false,$UserData);
   }

   $tpl->set('account_updated_departaments','done');
   $tpl->set('tab','tab_departments');
}

// If already set during account update
if (!isset($UserData))
{
    $UserData = $currentUser->getUserData();
}

$tpl->set('editdepartaments',$allowEditDepartaments);

$tpl->set('user',$UserData);

if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','personalcannedmsg') ) {
	
	/**
	 * Canned messages part
	 * */
	$cannedMessage = new erLhcoreClassModelCannedMsg();
	
	if (is_numeric($Params['user_parameters_unordered']['msg']) && $Params['user_parameters_unordered']['action'] == ''){
		$cannedMessage = erLhcoreClassModelCannedMsg::fetch($Params['user_parameters_unordered']['msg']);
		if ($cannedMessage->user_id != $UserData->id) {
			erLhcoreClassModule::redirect('user/account','#canned');
			exit;
		}
	}
	
	if (isset($_POST['Cancel_canned_action']))
	{
		erLhcoreClassModule::redirect('user/account','#canned');
		exit;
	}
	
	if (isset($_POST['Save_canned_action']))
	{	
		$Errors = erLhcoreClassAdminChatValidatorHelper::validateCannedMessage($cannedMessage, true);
				
		if (count($Errors) == 0) {		
			$cannedMessage->user_id = $UserData->id;
			$cannedMessage->saveThis();			
			$tpl->set('updated_canned',true);
		}  else {
			$tpl->set('errors_canned',$Errors);
		}
		
		$tpl->set('tab','tab_canned');
	}
	
	/**
	 * Delete canned message
	 * */
	if (is_numeric($Params['user_parameters_unordered']['msg']) && $Params['user_parameters_unordered']['action'] == 'delete') {
		
		if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
			die('Invalid CSRF Token');
			exit;
		}
		
		try {
			$cannedToDelete = erLhcoreClassModelCannedMsg::fetch($Params['user_parameters_unordered']['msg']);		
			if ($cannedToDelete->user_id == $UserData->id){
				$cannedToDelete->removeThis();
			}
		} catch (Exception $e) {
			
		}	
		erLhcoreClassModule::redirect('user/account','#canned');
		exit;
	}
	
	$tpl->set('canned_msg',$cannedMessage);	
}


$Result['content'] = $tpl->fetch();


?>