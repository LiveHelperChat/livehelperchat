<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/account.tpl.php' );

$currentUser = erLhcoreClassUser::instance();

$UserData = $currentUser->getUserData();

$tpl->set('tab',$Params['user_parameters_unordered']['tab'] == 'canned' ? 'tab_canned' : '');

if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','allowtochoosependingmode') && isset($_POST['UpdatePending_account'])) {	
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}
	
	$showAllPending = erLhcoreClassUserValidator::validateShowAllPendingOption();
	
	erLhcoreClassModelUserSetting::setSetting('show_all_pending', $showAllPending);
	
	$tpl->set('account_updated','done');
	$tpl->set('tab','tab_pending');
	
}

if (erLhcoreClassUser::instance()->hasAccessTo('lhspeech','changedefaultlanguage') && isset($_POST['UpdateSpeech_account'])) {
		
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}
	
	$validateSpeechData = erLhcoreClassUserValidator::validateSpeech();
	
	erLhcoreClassModelUserSetting::setSetting('speech_language', $validateSpeechData['speech_language']);
	erLhcoreClassModelUserSetting::setSetting('speech_dialect', $validateSpeechData['speech_dialect']);

	$tpl->set('account_updated','done');
	$tpl->set('tab','tab_speech');
	
}

if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','change_visibility_list') && isset($_POST['UpdateTabsSettings_account'])) {
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}
	
	$validateVisibilityListData = erLhcoreClassUserValidator::validateVisibilityList();
	
	erLhcoreClassModelUserSetting::setSetting('enable_pending_list', $validateVisibilityListData['enable_pending_list']);
	erLhcoreClassModelUserSetting::setSetting('enable_active_list', $validateVisibilityListData['enable_active_list']);
	erLhcoreClassModelUserSetting::setSetting('enable_close_list', $validateVisibilityListData['enable_close_list']);
	erLhcoreClassModelUserSetting::setSetting('enable_unread_list', $validateVisibilityListData['enable_unread_list']);
	
	$tpl->set('account_updated','done');
	$tpl->set('tab','tab_settings');
	
}

if (isset($_POST['Update'])) {
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}
	
	$Errors = erLhcoreClassUserValidator::validateAccount($UserData);

    if ( isset($_POST['DeletePhoto']) ) {
    	$UserData->removeFile();
    }

    $userPhotoErrors = erLhcoreClassUserValidator::validateUserPhoto($UserData);
    
    if($userPhotoErrors !== false) {
    	$Errors = array_merge($Errors, $userPhotoErrors);
    }

    if (count($Errors) == 0) {
    	
        erLhcoreClassUser::getSession()->update($UserData);
        
        $tpl->set('account_updated','done');

    }  else {
        $tpl->set('errors',$Errors);
    }
    
}

$currentUser = erLhcoreClassUser::instance();

$allowEditDepartaments = $currentUser->hasAccessTo('lhuser','editdepartaments');

if ($allowEditDepartaments && isset($_POST['UpdateDepartaments_account'])) {
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}
   
	$globalDepartament = erLhcoreClassUserValidator::validateDepartments($UserData);
   	
	erLhcoreClassUser::getSession()->update($UserData);
	
	if (count($globalDepartament) > 0) {
		erLhcoreClassUserDep::addUserDepartaments($globalDepartament, false, $UserData);
	} else {
		erLhcoreClassUserDep::addUserDepartaments(array(), false, $UserData);
	}
   		
	erLhcoreClassModelDepartamentGroupUser::addUserDepartmentGroups($UserData, erLhcoreClassUserValidator::validateDepartmentsGroup($UserData));
	
	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.after_user_departments_update',array('user' => & $UserData));
	
	$tpl->set('account_updated_departaments','done');
	$tpl->set('tab','tab_departments');
   
}

// If already set during account update
if (!isset($UserData)) {
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
			
			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.canned_msg_after_save',array('msg' => & $cannedMessage));
			
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

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.account', array('userData' => & $UserData, 'tpl' => & $tpl));

$Result['content'] = $tpl->fetch();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.account_result', array('result' => & $Result));

?>