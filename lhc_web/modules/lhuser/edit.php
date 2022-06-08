<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/edit.tpl.php');

if (isset($_POST['Cancel_account'])) {
    erLhcoreClassModule::redirect('user/userlist');
    exit;
}

try {
	$UserData = erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['user_id']);
} catch (Exception $e) {
	erLhcoreClassModule::redirect('user/userlist');
	exit;
}

$tpl->set('tab',$Params['user_parameters_unordered']['tab'] == 'canned' ? 'tab_canned' : '');

$can_edit_groups = erLhcoreClassGroupRole::canEditUserGroups(erLhcoreClassUser::instance()->getUserData(), $UserData);

$groups_can_edit = erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'editusergroupall') == true ? true : erLhcoreClassGroupRole::getGroupsAccessedByUser(erLhcoreClassUser::instance()->getUserData());

$userDataGroupsRead = array();
if ($groups_can_edit !== true) {
    $userDataGroupsRead = erLhcoreClassGroupRole::getGroupsAccessedByUser($UserData)['read'];
}

if ((isset($_POST['Update_account']) || isset($_POST['Save_account'])) && $can_edit_groups === true) {
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/edit', '/'.$UserData->id);
		exit;
	}
	
	$params = array('can_edit_groups' => $can_edit_groups, 'groups_can_read' => $userDataGroupsRead, 'groups_can_edit' => ($groups_can_edit === true ? true : $groups_can_edit['groups']));

    $originalSettings['old'] = $UserData->getState();

    $Errors = erLhcoreClassUserValidator::validateUserEdit($UserData, $params);
	
    if ( isset($_POST['DeletePhoto']) ) {
    	$UserData->removeFile();
    }
    
    $userPhotoErrors = erLhcoreClassUserValidator::validateUserPhoto($UserData);
    
    if($userPhotoErrors !== false) {
    	$Errors = array_merge($Errors, $userPhotoErrors);
    }
    
    if (count($Errors) == 0) {

        if ( isset($_POST['ForceResetPassword']) ) {
            if (erLhcoreClassModelUserLogin::getCount(array('filter' => array (
                'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
                'status' => erLhcoreClassModelUserLogin::STATUS_PENDING,
                'user_id' => $UserData->id))) == 0) {
                    erLhcoreClassModelUserLogin::logUserAction(array(
                        'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
                        'user_id' => $UserData->id,
                        'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password reset requested by') . ' ' . $currentUser->getUserData(),
                    ));
            }
        } else {
            $userLogin = erLhcoreClassModelUserLogin::findOne(array('filter' => array (
                'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
                'status' => erLhcoreClassModelUserLogin::STATUS_PENDING,
                'user_id' => $UserData->id)));
            if ($userLogin instanceof erLhcoreClassModelUserLogin){
                $userLogin->removeThis();
            }
        }

        // Log user changes
        $auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
        $data = (array)$auditOptions->data;
        if (isset($data['log_user']) && $data['log_user'] == 1) {
            $originalSettings['new'] = $UserData->getState();

            erLhcoreClassLog::logObjectChange(array(
                'object' => $UserData,
                'msg' => array(
                    'action' => 'account_data',
                    'prev' => $originalSettings['old'],
                    'new' => $originalSettings['new'],
                    'user_id' => $currentUser->getUserID()
                )
            ));
        }

        $UserData->updateThis();

        erLhcoreClassUserDep::setHideOnlineStatus($UserData);
        
        if ($can_edit_groups == true) {
            $UserData->setUserGroups();
        }
        
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();
       
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.user_modified',array('userData' => & $UserData, 'password' => $UserData->password_front));
        
        if (isset($_POST['Save_account'])) {
            erLhcoreClassModule::redirect('user/userlist');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    } else {
        $tpl->set('errors',$Errors);
    }    
}

if (isset($_POST['UpdatePending_account']) && $can_edit_groups === true) {
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/edit', '/'.$UserData->id);
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

	erLhcoreClassModelUserSetting::setSetting('show_all_pending', $pendingSettings['show_all_pending'], $UserData->id);
	erLhcoreClassModelUserSetting::setSetting('auto_join_private', $pendingSettings['auto_join_private'], $UserData->id);

    $UserData->auto_accept = $pendingSettings['auto_accept'];
    $UserData->max_active_chats = $pendingSettings['max_chats'];
    $UserData->exclude_autoasign = $pendingSettings['exclude_autoasign'];
    $UserData->saveThis();

    // Update max active chats directly
    $db = ezcDbInstance::get();
    $stmt = $db->prepare('UPDATE lh_userdep SET max_chats = :max_chats,exclude_autoasign = :exclude_autoasign WHERE user_id = :user_id');
    $stmt->bindValue(':max_chats', $UserData->max_active_chats, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $UserData->id, PDO::PARAM_INT);
    $stmt->bindValue(':exclude_autoasign', $UserData->exclude_autoasign, PDO::PARAM_INT);
    $stmt->execute();

	$tpl->set('account_updated','done');
	$tpl->set('tab','tab_pending');


	
}

if (isset($_POST['UpdateDepartaments_account']) && $can_edit_groups === true) {

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/edit', '/'.$UserData->id);
		exit;
	}

    $departmentEditParams = erLhcoreClassUserValidator::getDepartmentValidationParams($UserData);

	$globalDepartament = erLhcoreClassUserValidator::validateDepartments($UserData, array(
        'all_departments_0_global_value' => -1,
        'all_departments' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','edit_all_departments'),
        'edit_params' => $departmentEditParams
    ));

    $readOnlyDepartments = array();

    // All departments can be edited
    if ($departmentEditParams['individual']['edit_all'] == true) {
        if (isset($_POST['UserDepartamentRead']) && count($_POST['UserDepartamentRead']) > 0) {
            $readOnlyDepartments = $_POST['UserDepartamentRead'];
        }
    } else if ($departmentEditParams['individual']['edit_personal'] == true) {
        $readOnlyDepartments = $departmentEditParams['individual']['remote_id_read'];
        if (isset($_POST['UserDepartamentRead']) && count($_POST['UserDepartamentRead']) > 0) {
            foreach ($_POST['UserDepartamentRead'] as $depId){
                if (in_array($depId,$departmentEditParams['individual']['id'])) {
                    $readOnlyDepartments[] = $depId;
                }
            }
        }
    } else {
        $readOnlyDepartments = $departmentEditParams['individual']['remote_id_read_all'];
    }

    $excAutoDepartments = array();
    if (isset($_POST['UserDepartamentAutoExc']) && count($_POST['UserDepartamentAutoExc']) > 0) {
        $excAutoDepartments = $_POST['UserDepartamentAutoExc'];
    }

    $UserData->updateThis();

    if (count($globalDepartament) > 0) {
        erLhcoreClassUserDep::addUserDepartaments($globalDepartament, $UserData->id, $UserData, $readOnlyDepartments, $excAutoDepartments);
    } else {
        erLhcoreClassUserDep::addUserDepartaments(array(), $UserData->id, $UserData, $readOnlyDepartments, $excAutoDepartments);
    }

    $excludeGroups = erLhcoreClassUserValidator::validateDepartmentsGroup($UserData, array('edit_params' => $departmentEditParams, 'exclude_auto' => true));

    // Write
	erLhcoreClassModelDepartamentGroupUser::addUserDepartmentGroups($UserData, erLhcoreClassUserValidator::validateDepartmentsGroup($UserData, array('edit_params' => $departmentEditParams)),
        false,
        $excludeGroups);

    // Read
    erLhcoreClassModelDepartamentGroupUser::addUserDepartmentGroups($UserData, erLhcoreClassUserValidator::validateDepartmentsGroup($UserData, array('edit_params' => $departmentEditParams, 'read_only' => true)),
        true,
        $excludeGroups);

	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.after_user_departments_update', array('user' => & $UserData));

	$tpl->set('account_updated_departaments','done');

}

if (isset($_POST['UpdateSpeech_account']) && $can_edit_groups === true) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('user/edit', '/'.$UserData->id);
        exit;
    }

    $validateSpeechData = erLhcoreClassUserValidator::validateSpeech();

    erLhcoreClassModelUserSetting::setSetting('speech_language', $validateSpeechData['speech_language'],$UserData->id);
    erLhcoreClassModelUserSetting::setSetting('speech_dialect', $validateSpeechData['speech_dialect'],$UserData->id);

    erLhcoreClassSpeech::setUserLanguages($UserData->id, $validateSpeechData['user_languages']);

    $tpl->set('account_updated','done');
    $tpl->set('tab','tab_speech');
}

$userGroupFilter = $groups_can_edit === true ? array() : array('filterin' => array('id' => $groups_can_edit['groups']));

$tpl->set('user_groups_filter',$userGroupFilter);
$tpl->set('can_edit_groups',$can_edit_groups);
$tpl->set('groups_read_only',$groups_can_edit === true ? true : $groups_can_edit['read']);

$tpl->set('force_reset_password', erLhcoreClassModelUserLogin::getCount(array('filter' => array(
    'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
    'status' => erLhcoreClassModelUserLogin::STATUS_PENDING,
    'user_id' => $UserData->id))));

$tpl->set('user',$UserData);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit_user_window',array('userData' => & $UserData, 'tpl' => & $tpl, 'params' => $Params));

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.account.validator.js').'"></script>';
$Result['path'] = array(
	array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','System configuration')),
	array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Users')),
	array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User edit').' - '.$UserData->name.' '.$UserData->surname)
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit_path',array('result' => & $Result));

?>