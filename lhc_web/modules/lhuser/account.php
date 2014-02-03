<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/account.tpl.php' );

$currentUser = erLhcoreClassUser::instance();
$UserData = $currentUser->getUserData();

$tpl->set('tab','');

if (isset($_POST['UpdateTabsSettings_account']))
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
        'Skype' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'XMPPUsername' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
   		'UserTimeZone' => new ezcInputFormDefinitionElement(
   				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
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

    if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','changeskypenick') ) {
	    if ( $form->hasValidData( 'Skype' ) && $form->Skype != '')
	    {
	    	$UserData->skype = $form->Skype;
	    } else {
	    	$UserData->skype = '';
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
    	erLhcoreClassFileUpload::mkdirRecursive( $dir );

    	$file = qqFileUploader::upload($_FILES,'UserPhoto',$dir);

    	if ( !empty($file["errors"]) ) {
    		foreach ($file["errors"] as $err) {
    			$Errors[] = $err;
    		}
    	} else {

    		$UserData->removeFile();
    		$UserData->filename           = $file["data"]["filename"];
    		$UserData->filepath           = $file["data"]["dir"];

    		erLhcoreClassImageConverter::getInstance()->converter->transform( 'photow_150', $UserData->file_path_server, $UserData->file_path_server );
    		chmod($UserData->file_path_server, 0644);
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

   erLhcoreClassUser::getSession()->update($UserData);

   if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0)
   {
       $globalDepartament = array_merge($_POST['UserDepartament'],$globalDepartament);
   }

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

$Result['content'] = $tpl->fetch();


?>