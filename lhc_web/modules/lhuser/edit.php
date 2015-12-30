<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/edit.tpl.php');

try {
	$UserData = erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['user_id']);
} catch (Exception $e) {
	erLhcoreClassModule::redirect('user/userlist');
	exit;
}

$tpl->set('tab',$Params['user_parameters_unordered']['tab'] == 'canned' ? 'tab_canned' : '');

if (isset($_POST['Update_account']) || isset($_POST['Save_account'])) {
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/edit', '/'.$UserData->id);
		exit;
	}
	
	$Errors = erLhcoreClassUserValidator::validateUserEdit($UserData);
	
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
        
		$UserData->setUserGroups();

        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();
       
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.user_modified',array('userData' => & $UserData, 'password' => $UserData->password_front));
        
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

if (isset($_POST['UpdatePending_account'])) {
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/edit', '/'.$UserData->id);
		exit;
	}
	
	$showAllPending = erLhcoreClassUserValidator::validateShowAllPendingOption();
	
	erLhcoreClassModelUserSetting::setSetting('show_all_pending', $showAllPending, $UserData->id);

	$tpl->set('account_updated','done');
	$tpl->set('tab','tab_pending');
	
}

if (isset($_POST['UpdateDepartaments_account'])) {
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/edit', '/'.$UserData->id);
		exit;
	}
	
	$globalDepartament = erLhcoreClassUserValidator::validateDepartments($UserData, array('all_departments_0_global_value' => -1));
			
	erLhcoreClassUser::getSession()->update($UserData);
   
	if (count($globalDepartament) > 0) {
		erLhcoreClassUserDep::addUserDepartaments($globalDepartament, $UserData->id, $UserData);
	} else {
    	erLhcoreClassUserDep::addUserDepartaments(array(), $UserData->id, $UserData);
	}

	$tpl->set('account_updated_departaments','done');
   
}

$tpl->set('user',$UserData);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
	array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','System configuration')),
	array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Users')),
	array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','User edit').' - '.$UserData->name.' '.$UserData->surname)
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit_path',array('result' => & $Result));

?>