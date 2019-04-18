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
	
	$pendingSettings = erLhcoreClassUserValidator::validateShowAllPendingOption();
	
	erLhcoreClassModelUserSetting::setSetting('show_all_pending', $pendingSettings['show_all_pending']);
	erLhcoreClassModelUserSetting::setSetting('auto_uppercase', $pendingSettings['auto_uppercase']);

    $UserData->exclude_autoasign = $pendingSettings['exclude_autoasign'];
    $UserData->auto_accept = $pendingSettings['auto_accept'];
    $UserData->max_active_chats = $pendingSettings['max_chats'];
    $UserData->saveThis();

    if (isset($_POST['auto_preload']) && $_POST['auto_preload'] == 1) {
        erLhcoreClassModelUserSetting::setSetting('auto_preload', 1);
    } else {
        erLhcoreClassModelUserSetting::setSetting('auto_preload', 0);
    }

    // Update max active chats directly
    $db = ezcDbInstance::get();
    $stmt = $db->prepare('UPDATE lh_userdep SET max_chats = :max_chats, exclude_autoasign = :exclude_autoasign WHERE user_id = :user_id');
    $stmt->bindValue(':max_chats', $UserData->max_active_chats, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $UserData->id, PDO::PARAM_INT);
    $stmt->bindValue(':exclude_autoasign', $UserData->exclude_autoasign, PDO::PARAM_INT);
    $stmt->execute();

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

    erLhcoreClassSpeech::setUserLanguages($currentUser->getUserID(),$validateSpeechData['user_languages']);

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
	erLhcoreClassModelUserSetting::setSetting('enable_bot_list', $validateVisibilityListData['enable_bot_list']);

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

        erLhcoreClassUserDep::setHideOnlineStatus($UserData);

        erLhcoreClassChat::updateActiveChats($UserData->id);

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed',array('user' => & $UserData, 'reason' => 'user_action'));

        $tpl->set('account_updated','done');

    }  else {
        $tpl->set('errors',$Errors);
    }
    
}

if (isset($_POST['UpdateNotifications_account'])) {
    
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('user/account');
        exit;
    }
    
    $validateNotificationsData = erLhcoreClassUserValidator::validateNotifications();



    erLhcoreClassModelUserSetting::setSetting('show_alert_chat', $validateNotificationsData['show_alert_chat']);
    erLhcoreClassModelUserSetting::setSetting('sn_off', $validateNotificationsData['sn_off']);
    erLhcoreClassModelUserSetting::setSetting('ownntfonly', $validateNotificationsData['ownntfonly']);
    erLhcoreClassModelUserSetting::setSetting('trackactivity', $validateNotificationsData['trackactivity']);
    erLhcoreClassModelUserSetting::setSetting('trackactivitytimeout', $validateNotificationsData['trackactivitytimeout']);
    erLhcoreClassModelUserSetting::setSetting('show_alert_transfer', $validateNotificationsData['show_alert_transfer']);

    $tpl->set('account_updated','done');
    $tpl->set('tab','tab_notifications');  
}

$currentUser = erLhcoreClassUser::instance();

$allowEditDepartaments = $currentUser->hasAccessTo('lhuser','editdepartaments');

if ($allowEditDepartaments && isset($_POST['UpdateDepartaments_account'])) {
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}

	$globalDepartament = erLhcoreClassUserValidator::validateDepartments($UserData);

    $readOnlyDepartments = array();
    if (isset($_POST['UserDepartamentRead']) && count($_POST['UserDepartamentRead']) > 0) {
        $readOnlyDepartments = $_POST['UserDepartamentRead'];
    }

	erLhcoreClassUser::getSession()->update($UserData);
	
	if (count($globalDepartament) > 0) {
		erLhcoreClassUserDep::addUserDepartaments($globalDepartament, false, $UserData, $readOnlyDepartments);
	} else {
		erLhcoreClassUserDep::addUserDepartaments(array(), false, $UserData, $readOnlyDepartments);
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
				
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.canned_msg_before_save',array('errors' => & $Errors, 'msg' => & $cannedMessage, 'scope' => 'user'));
		
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

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.account', array('userData' => & $UserData, 'tpl' => & $tpl, 'params' => $Params));

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.cannedmsg.js').'"></script>';

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.account_result', array('result' => & $Result));

?>