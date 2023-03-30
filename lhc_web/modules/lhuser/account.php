<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/account.tpl.php' );

$currentUser = erLhcoreClassUser::instance();

$UserData = $currentUser->getUserData();

$validTabs = array(
    'canned' => 'tab_canned',
    'autoresponder' => 'tab_autoresponder'
);

$tpl->set('tab',key_exists($Params['user_parameters_unordered']['tab'], $validTabs) ? $validTabs[$Params['user_parameters_unordered']['tab']] : '');

if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','allowtochoosependingmode') && isset($_POST['UpdatePending_account'])) {

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/account');
		exit;
	}

	$pendingSettings = erLhcoreClassUserValidator::validateShowAllPendingOption();

    // Log user changes
    $auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
    $data = (array)$auditOptions->data;
    if (isset($data['log_user']) && $data['log_user'] == 1) {
        $originalSettings['old'] = array(
            'auto_accept' => $UserData->auto_accept,
            'max_chats' => $UserData->max_active_chats,
            'exclude_autoasign' => $UserData->exclude_autoasign,
            'show_all_pending' => erLhcoreClassModelUserSetting::getSetting('show_all_pending',  1, $UserData->id),
            'auto_join_private' =>  erLhcoreClassModelUserSetting::getSetting('auto_join_private',  1, $UserData->id),
            'no_scroll_bottom' =>  erLhcoreClassModelUserSetting::getSetting('no_scroll_bottom',  0, $UserData->id),
            'remove_closed_chats' =>  erLhcoreClassModelUserSetting::getSetting('remove_closed_chats',  0, $UserData->id),
            'remove_closed_chats_remote' =>  erLhcoreClassModelUserSetting::getSetting('remove_closed_chats_remote',  0, $UserData->id),
            'remove_close_timeout' =>  erLhcoreClassModelUserSetting::getSetting('remove_close_timeout',  5, $UserData->id),
            'chat_text_rows' =>  erLhcoreClassModelUserSetting::getSetting('chat_text_rows',  2, $UserData->id),
        );
        $originalSettings['new'] = $pendingSettings;

        erLhcoreClassLog::logObjectChange(array(
            'object' => $UserData,
            'msg' => array(
                'prev' => $originalSettings['old'],
                'new' => $originalSettings['new'],
                'user_id' => $currentUser->getUserID()
            )
        ));
    }

	erLhcoreClassModelUserSetting::setSetting('show_all_pending', $pendingSettings['show_all_pending']);
	erLhcoreClassModelUserSetting::setSetting('auto_uppercase', $pendingSettings['auto_uppercase']);
	erLhcoreClassModelUserSetting::setSetting('chat_text_rows', $pendingSettings['chat_text_rows']);

    $UserData->exclude_autoasign = $pendingSettings['exclude_autoasign'];
    $UserData->auto_accept = $pendingSettings['auto_accept'];
    $UserData->max_active_chats = $pendingSettings['max_chats'];
    $UserData->saveThis();

    if (isset($_POST['auto_preload']) && $_POST['auto_preload'] == 1) {
        erLhcoreClassModelUserSetting::setSetting('auto_preload', 1);
    } else {
        erLhcoreClassModelUserSetting::setSetting('auto_preload', 0);
    }

    if (isset($_POST['remove_closed_chats']) && $_POST['remove_closed_chats'] == 1) {
        erLhcoreClassModelUserSetting::setSetting('remove_closed_chats', 1);
    } else {
        erLhcoreClassModelUserSetting::setSetting('remove_closed_chats', 0);
    }

    if (isset($_POST['remove_closed_chats_remote']) && $_POST['remove_closed_chats_remote'] == 1) {
        erLhcoreClassModelUserSetting::setSetting('remove_closed_chats_remote', 1);
    } else {
        erLhcoreClassModelUserSetting::setSetting('remove_closed_chats_remote', 0);
    }

    if (isset($_POST['remove_close_timeout']) && (int)$_POST['remove_close_timeout'] >= 1 || (int)$_POST['remove_close_timeout'] <= 60 ) {
        erLhcoreClassModelUserSetting::setSetting('remove_close_timeout', (int)$_POST['remove_close_timeout']);
    } else {
        erLhcoreClassModelUserSetting::setSetting('remove_close_timeout', 5);
    }

    if (isset($_POST['auto_join_private']) && $_POST['auto_join_private'] == 1) {
        erLhcoreClassModelUserSetting::setSetting('auto_join_private', 1);
    } else {
        erLhcoreClassModelUserSetting::setSetting('auto_join_private', 0);
    }

    if (isset($_POST['no_scroll_bottom']) && $_POST['no_scroll_bottom'] == 1) {
        erLhcoreClassModelUserSetting::setSetting('no_scroll_bottom', 1);
    } else {
        erLhcoreClassModelUserSetting::setSetting('no_scroll_bottom', 0);
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
	erLhcoreClassModelUserSetting::setSetting('enable_mchats_list', $validateVisibilityListData['enable_mchats_list']);
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

    if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','changephoto')) {
        if ( isset($_POST['DeletePhoto']) ) {
            $UserData->removeFile();
        }

        $userPhotoErrors = erLhcoreClassUserValidator::validateUserPhoto($UserData);

        if ($userPhotoErrors !== false) {
            $Errors = array_merge($Errors, $userPhotoErrors);
        }
    }

    if (count($Errors) == 0) {
    	
        $UserData->updateThis();

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
    erLhcoreClassModelUserSetting::setSetting('hide_quick_notifications', $validateNotificationsData['hide_quick_notifications']);

    if ($currentUser->hasAccessTo('lhuser', 'largeactivitytimeout')) {
        erLhcoreClassModelUserSetting::setSetting('trackactivitytimeout', $validateNotificationsData['trackactivitytimeout']);
    }

    erLhcoreClassModelUserSetting::setSetting('show_alert_transfer', $validateNotificationsData['show_alert_transfer']);

    $tpl->set('account_updated','done');
    $tpl->set('tab','tab_notifications');  
}

$currentUser = erLhcoreClassUser::instance();

$allowEditDepartaments = $currentUser->hasAccessTo('lhuser','editdepartaments');

if ($allowEditDepartaments && isset($_POST['UpdateDepartaments_account']) && ($currentUser->hasAccessTo('lhuser','see_assigned_departments') || $currentUser->hasAccessTo('lhuser','see_assigned_departments_groups'))) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('user/account');
        exit;
    }

    try {
        $db = ezcDbInstance::get();

        $db->beginTransaction();

        if ($currentUser->hasAccessTo('lhuser', 'see_assigned_departments')) {

            $globalDepartament = array();

            if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'self_all_departments'))
            {
                if (isset($_POST['all_departments']) && $_POST['all_departments'] == 'on') {
                    $UserData->all_departments = 1;
                    $globalDepartament[] = 0;
                } else {
                    $UserData->all_departments = 0;
                    if(isset($params['all_departments_0_global_value'])) {
                        $globalDepartament[] = $params['all_departments_0_global_value'];
                    } else {
                        $globalDepartament[] = -1;
                    }
                }

                erLhcoreClassUserDep::addUserDepartaments($globalDepartament, false, $UserData, [], [], ['only_global' => true]);

                $UserData->updateThis();
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.after_user_departments_update', array('user' => & $UserData));

        $tpl->set('account_updated_departaments', 'done');
        $tpl->set('tab', 'tab_departments');

        $db->commit();

    } catch (Exception $e) {
        $db->rollback();
        $tpl->set('account_updated_departaments','failed');
        $tpl->set('tab', 'tab_departments');
    }
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
	
	if (is_numeric($Params['user_parameters_unordered']['msg']) && $Params['user_parameters_unordered']['action'] == '' && $Params['user_parameters_unordered']['tab'] == 'canned') {
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

		    $isNew = $cannedMessage->id == null;

            $previousState = $cannedMessage->getState();

			$cannedMessage->user_id = $UserData->id;
			$cannedMessage->saveThis();

			if ($isNew == true){
                erLhcoreClassLog::logObjectChange(array(
                    'object' => $cannedMessage,
                    'check_log' => true,
                    'msg' => array(
                        'new' => $cannedMessage->getState(),
                        'user_id' => $currentUser->getUserID()
                    )
                ));
            } else {
                erLhcoreClassLog::logObjectChange(array(
                    'object' => $cannedMessage,
                    'check_log' => true,
                    'msg' => array(
                        'prev' => $previousState,
                        'curr' => $cannedMessage->getState(),
                        'user_id' => $currentUser->getUserID()
                    )
                ));
            }

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
	if (is_numeric($Params['user_parameters_unordered']['msg']) && $Params['user_parameters_unordered']['action'] == 'delete' && $Params['user_parameters_unordered']['tab'] == 'canned') {
		
		if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
			die('Invalid CSRF Token');
			exit;
		}
		
		try {
			$cannedToDelete = erLhcoreClassModelCannedMsg::fetch($Params['user_parameters_unordered']['msg']);		
			if ($cannedToDelete->user_id == $UserData->id){
				$cannedToDelete->removeThis();
			}

            erLhcoreClassLog::logObjectChange(array(
                'object' => $cannedToDelete,
                'check_log' => true,
                'msg' => array(
                    'delete' => $cannedToDelete->getState(),
                    'user_id' => $currentUser->getUserID()
                )
            ));

		} catch (Exception $e) {
			
		}	
		erLhcoreClassModule::redirect('user/account','#canned');
		exit;
	}
	
	$tpl->set('canned_msg',$cannedMessage);	
	
}

if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','personalautoresponder') ) {
    $autoResponderMessage = new erLhAbstractModelAutoResponder();

    if (is_numeric($Params['user_parameters_unordered']['msg']) && $Params['user_parameters_unordered']['action'] == '' && $Params['user_parameters_unordered']['tab'] == 'autoresponder') {
        $autoResponderMessage = erLhAbstractModelAutoResponder::fetch($Params['user_parameters_unordered']['msg']);
        if ($autoResponderMessage->user_id != $UserData->id) {
            erLhcoreClassModule::redirect('user/account','#canned');
            exit;
        }
    }

    if (isset($_POST['Cancel_autoresponder_action']))
    {
        erLhcoreClassModule::redirect('user/account','#autoresponder');
        exit;
    }

    if (isset($_POST['Save_autoresponder_action']))
    {
        if (!$currentUser->validateCSFRToken($_POST['csfr_token'])) {
            die('Invalid CSRF Token');
            exit;
        }

        $Errors = erLhcoreClassAbstract::validateInput($autoResponderMessage);

        if (count($Errors) == 0) {

            $autoResponderMessage->user_id = $UserData->id;
            $autoResponderMessage->saveThis();

            $tpl->set('updated_autoresponder',true);
        } else {
            $tpl->set('errors_autoresponder',$Errors);
        }

        $tpl->set('tab','tab_autoresponder');
    }

    /**
     * Delete auto responder
     * */
    if (is_numeric($Params['user_parameters_unordered']['msg']) && $Params['user_parameters_unordered']['action'] == 'delete' && $Params['user_parameters_unordered']['tab'] == 'autoresponder') {

        if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
            die('Invalid CSRF Token');
            exit;
        }

        $cannedToDelete = erLhAbstractModelAutoResponder::fetch($Params['user_parameters_unordered']['msg']);
        if ($cannedToDelete instanceof erLhAbstractModelAutoResponder && $cannedToDelete->user_id == $UserData->id){
            $cannedToDelete->removeThis();
        }

        erLhcoreClassModule::redirect('user/account','#autoresponder');
        exit;
    }


    $tpl->set('autoResponder_msg', $autoResponderMessage);
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.account', array('userData' => & $UserData, 'tpl' => & $tpl, 'params' => $Params));

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.cannedmsg.js;js/angular.lhc.autoresponder.js').'"></script>';

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.account_result', array('result' => & $Result));

?>