<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/new.tpl.php');

$UserData = new erLhcoreClassModelUser();

$UserDepartaments = isset($_POST['UserDepartament']) ? $_POST['UserDepartament'] : array();
$userDepartamentsGroup = isset($_POST['UserDepartamentGroup']) ? $_POST['UserDepartamentGroup'] : array();
$userDepartamentsGroupRead = isset($_POST['UserDepartamentGroupRead']) ? $_POST['UserDepartamentGroupRead'] : array();
$userDepartamentsRead = isset($_POST['UserDepartamentRead']) ? $_POST['UserDepartamentRead'] : array();
$userDepartamentsAutoExc = isset($_POST['UserDepartamentAutoExc']) ? $_POST['UserDepartamentAutoExc'] : array();
$userDepartamentsGroupAutoExc = isset($_POST['UserDepartamentGroupAutoExc']) ? $_POST['UserDepartamentGroupAutoExc'] : array();

$tpl->set('tab',$Params['user_parameters_unordered']['tab'] == 'canned' ? 'tab_canned' : '');

$groups_can_edit = erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'editusergroupall') == true ? true : erLhcoreClassGroupRole::getGroupsAccessedByUser(erLhcoreClassUser::instance()->getUserData());

$userParams = array(
    'edit_params' => erLhcoreClassUserValidator::getDepartmentValidationParams($UserData),
    'show_all_pending' => 1,
    'global_departament' => array(),
    'groups_can_read' => array(),
    'auto_join_private' => 1,
    'no_scroll_bottom' => 0,
    'remove_closed_chats' => 0,
    'auto_preload' => 0,
    'auto_uppercase' => 1,
    
    // Notifications
    'ownntfonly' => 0,
    'sn_off' => 1,
    'show_alert_chat' => 0,
    'show_alert_transfer' => 1,
    'hide_quick_notifications' => 0,
    'trackactivity' => 0,
    'trackactivitytimeout' => -1,

    // Groups
    'groups_can_edit' => ($groups_can_edit === true ? true : $groups_can_edit['groups'])
);

if (isset($_POST['Update_account']))
{
	
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('user/new');
		exit;
	}

	$Errors = erLhcoreClassUserValidator::validateUserNew($UserData, $userParams);
	
    if (count($Errors) == 0) {

        try {

            $db = ezcDbInstance::get();

            $db->beginTransaction();

            erLhcoreClassUser::getSession()->save($UserData);

            if ( isset($_POST['ForceResetPassword']) ) {
                erLhcoreClassModelUserLogin::logUserAction(array(
                    'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
                    'user_id' => $UserData->id,
                    'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password reset requested by') . ' ' . $currentUser->getUserData(),
                ));
            }

            if (count($userParams['global_departament']) > 0) {
               erLhcoreClassUserDep::addUserDepartaments($userParams['global_departament'], $UserData->id, $UserData, $userDepartamentsRead, $userDepartamentsAutoExc);
            }

            $UserData->setUserGroups();

            $userPhotoErrors = erLhcoreClassUserValidator::validateUserPhoto($UserData);

            if ($userPhotoErrors !== false && count($userPhotoErrors) == 0) {
            	$UserData->saveThis();
            }

            // Write
            erLhcoreClassModelDepartamentGroupUser::addUserDepartmentGroups($UserData, erLhcoreClassUserValidator::validateDepartmentsGroup($UserData, array('edit_params' => $userParams['edit_params'])), false, $userDepartamentsGroupAutoExc);

            // Read
            erLhcoreClassModelDepartamentGroupUser::addUserDepartmentGroups($UserData, erLhcoreClassUserValidator::validateDepartmentsGroup($UserData, array('edit_params' => $userParams['edit_params'], 'read_only' => true)), true, $userDepartamentsGroupAutoExc);

            // Chats
            erLhcoreClassModelUserSetting::setSetting('show_all_pending', $userParams['show_all_pending'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('auto_join_private', $userParams['auto_join_private'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('remove_closed_chats', $userParams['remove_closed_chats'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('auto_preload', $userParams['auto_preload'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('no_scroll_bottom', $userParams['no_scroll_bottom'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('auto_uppercase', $userParams['auto_uppercase'], $UserData->id);

            // Notifications
            erLhcoreClassModelUserSetting::setSetting('show_alert_chat', $userParams['show_alert_chat'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('sn_off', $userParams['sn_off'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('ownntfonly', $userParams['ownntfonly'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('trackactivity', $userParams['trackactivity'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('hide_quick_notifications', $userParams['hide_quick_notifications'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('trackactivitytimeout', $userParams['trackactivitytimeout'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('show_alert_transfer', $userParams['show_alert_transfer'], $UserData->id);

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.user_created',array('userData' => & $UserData, 'password' => $UserData->password_front));

            $db->commit();

            erLhcoreClassModule::redirect('user/userlist');
            exit;

        } catch (Exception $e) {

            $tpl->set('errors',array($e->getMessage()));

            $UserData->removeFile();

            $db->rollback();

        }

    }  else {
        $tpl->set('errors',$Errors);
    }
    
}

$tpl->set('user',$UserData);
$tpl->set('userDepartaments',$UserDepartaments);
$tpl->set('userDepartamentsGroup',$userDepartamentsGroup);
$tpl->set('userDepartamentsGroupRead',$userDepartamentsGroupRead);
$tpl->set('userDepartamentsRead',$userDepartamentsRead);
$tpl->set('userDepartamentsAutoExc',$userDepartamentsAutoExc);
$tpl->set('userDepartamentsGroupAutoExc',$userDepartamentsGroupAutoExc);
$tpl->set('quick_settings', $userParams);


$userGroupFilter = $groups_can_edit === true ? array() : array('filterin' => array('id' => $groups_can_edit['groups']));
$tpl->set('user_groups_filter',$userGroupFilter);
$tpl->set('groups_read_only',$groups_can_edit === true ? true : $groups_can_edit['read']);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.account.validator.js').'"></script>';

$Result['path'] = array(
	array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','System configuration')),
	array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Users')),
	array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.new_path', array('result' => & $Result));

?>