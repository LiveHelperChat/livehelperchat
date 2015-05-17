<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/new.tpl.php');

$UserData = new erLhcoreClassModelUser();
$UserDepartaments = isset($_POST['UserDepartament']) ? $_POST['UserDepartament'] : array();
$show_all_pending = 1;

$tpl->set('tab',$Params['user_parameters_unordered']['tab'] == 'canned' ? 'tab_canned' : '');

if (isset($_POST['Update_account']))
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
		'UserInvisible' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
		), 
		'showAllPendingEnabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
		), 
		'ReceivePermissionRequest' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
		), 
   		'JobTitle' => new ezcInputFormDefinitionElement(
   				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   		),
   		'UserTimeZone' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
		'DefaultGroup' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int',
				null,
				FILTER_REQUIRE_ARRAY
		),
        'Skype' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'XMPPUsername' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
   		erLhcoreClassModule::redirect('user/new');
   		exit;
    }

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'Email' ) )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Wrong email address');
    }

    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Please enter a name');
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
    
    if ( $form->hasValidData( 'UserTimeZone' ) && $form->UserTimeZone != '')
    {
    	$UserData->time_zone = $form->UserTimeZone;
    } else {
    	$UserData->time_zone = '';
    }
    
    if ( $form->hasValidData( 'Skype' ) && $form->Skype != '')
    {
    	$UserData->skype = $form->Skype;
    } else {
    	$UserData->skype = '';
    }
    
    if ( $form->hasValidData( 'XMPPUsername' ) && $form->XMPPUsername != '')
    {
    	$UserData->xmpp_username = $form->XMPPUsername;
    } else {
    	$UserData->xmpp_username = '';
    }
    
    if ( !$form->hasValidData( 'Username' ) || $form->Username == '')
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Please enter a username');
    }

    if ( $form->hasValidData( 'Username' ) && $form->Username != '' && erLhcoreClassModelUser::userExists($form->Username) === true )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User exists');
    }

    if ( !$form->hasValidData( 'Password' ) || !$form->hasValidData( 'Password1' ) || $form->Password == '' || $form->Password1 == '' || $form->Password != $form->Password1    ) // check for optional field
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Passwords mismatch');
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

    if ( $form->hasValidData( 'showAllPendingEnabled' ) && $form->showAllPendingEnabled == true )
    {
    	$show_all_pending = 1;
    } else {
    	$show_all_pending = 0;
    }

    if ( $form->hasValidData( 'HideMyStatus' ) && $form->HideMyStatus == true )
    {
    	$UserData->hide_online = 1;
    } else {
    	$UserData->hide_online = 0;
    }
    
    if ( $form->hasValidData( 'UserInvisible' ) && $form->UserInvisible == true ) {
    	$UserData->invisible_mode = 1;
    } else {
    	$UserData->invisible_mode = 0;
    }
    
    if ( $form->hasValidData( 'ReceivePermissionRequest' ) && $form->ReceivePermissionRequest == true ) {
        $UserData->rec_per_req = 1;
    } else {
        $UserData->rec_per_req = 0;
    }
    
    $globalDepartament = array();
    
    if (isset($_POST['all_departments']) && $_POST['all_departments'] == 'on') {
    	$UserData->all_departments = 1;
    	$globalDepartament[] = 0;
    } else {
    	$UserData->all_departments = 0;    	
    }
    
    // Allow extension to do extra validation
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.new_user',array('userData' => & $UserData, 'errors' => & $Errors));
    
    if (count($Errors) == 0)
    {
        $UserData->setPassword($form->Password);
        $UserData->email   = $form->Email;
        $UserData->name    = $form->Name;
        $UserData->username = $form->Username;

        erLhcoreClassUser::getSession()->save($UserData);

        if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0)
        {
           $globalDepartament = array_merge($_POST['UserDepartament'],$globalDepartament);
        }

        if (count($globalDepartament) > 0)
        {
           erLhcoreClassUserDep::addUserDepartaments($globalDepartament,$UserData->id,$UserData);
        }
        
        $UserData->departments_ids = implode(',', $globalDepartament);
        erLhcoreClassUser::getSession()->update($UserData);
        
        
        erLhcoreClassModelGroupUser::removeUserFromGroups($UserData->id);

        foreach ($UserData->user_groups_id as $group_id) {
        	$groupUser = new erLhcoreClassModelGroupUser();
        	$groupUser->group_id = $group_id;
        	$groupUser->user_id = $UserData->id;
        	$groupUser->saveThis();
        }

        // Store photo
        if ( isset($_FILES["UserPhoto"]) && is_uploaded_file($_FILES["UserPhoto"]["tmp_name"]) && $_FILES["UserPhoto"]["error"] == 0 && erLhcoreClassImageConverter::isPhoto('UserPhoto') ) {
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
        	
        	if ( empty($file["errors"]) ) {
        		$UserData->filename           = $file["data"]["filename"];
        		$UserData->filepath           = $file["data"]["dir"];

        		$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_resize_150', array('mime_type' => $file["data"]['mime_type'],'user' => $UserData));
        		
        		if ($response === false) {
        			erLhcoreClassImageConverter::getInstance()->converter->transform( 'photow_150', $UserData->file_path_server, $UserData->file_path_server );
        			chmod($UserData->file_path_server, 0644);
        		}
        		
        		$UserData->saveThis();
        	}
        }

        erLhcoreClassModelUserSetting::setSetting('show_all_pending',$show_all_pending,$UserData->id);
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.user_created',array('userData' => & $UserData));
        
        erLhcoreClassModule::redirect('user/userlist');
        exit;

    }  else {

        if ( $form->hasValidData( 'Email' ) )
        {
            $UserData->email = $form->Email;
        }

        $UserData->name = $form->Name;
        $UserData->surname = $form->Surname;
        $UserData->username = $form->Username;

        $tpl->set('errors',$Errors);
    }
}


$tpl->set('user',$UserData);
$tpl->set('userdepartaments',$UserDepartaments);
$tpl->set('show_all_pending',$show_all_pending);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','System configuration')),

array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Users')),

array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user'))

)

?>