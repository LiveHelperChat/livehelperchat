<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/new.tpl.php');

$UserData = new erLhcoreClassModelUser();
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
    'remove_closed_chats_remote' => 0,
    'remove_close_timeout' => 5,
    'auto_preload' => 0,
    'auto_uppercase' => 1,
    'chat_text_rows' => 2,

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

if (isset($_POST['Update_account']) || isset($_POST['Update_account_edit']))
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

            if ($UserData->time_zone == 'default') {
                $UserData->time_zone = '';
            }

            erLhcoreClassUser::getSession()->save($UserData);

            if ( isset($_POST['ForceResetPassword']) ) {
                erLhcoreClassModelUserLogin::logUserAction(array(
                    'type' => erLhcoreClassModelUserLogin::TYPE_PASSWORD_RESET_REQUEST,
                    'user_id' => $UserData->id,
                    'msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Password reset requested by') . ' ' . $currentUser->getUserData(),
                ));
            }

            $UserData->setUserGroups();

            $userPhotoErrors = erLhcoreClassUserValidator::validateUserPhoto($UserData);

            if ($userPhotoErrors !== false && count($userPhotoErrors) == 0) {
            	$UserData->saveThis();
            }

            // Chats
            erLhcoreClassModelUserSetting::setSetting('show_all_pending', $userParams['show_all_pending'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('auto_join_private', $userParams['auto_join_private'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('remove_closed_chats', $userParams['remove_closed_chats'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('remove_closed_chats_remote', $userParams['remove_closed_chats_remote'], $UserData->id);
            erLhcoreClassModelUserSetting::setSetting('remove_close_timeout', $userParams['remove_close_timeout'], $UserData->id);

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
            erLhcoreClassModelUserSetting::setSetting('chat_text_rows', $userParams['chat_text_rows'], $UserData->id);

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.user_created',array('userData' => & $UserData, 'password' => $UserData->password_front));

            $db->commit();

            if (isset($_POST['Update_account_edit'])) {
                erLhcoreClassModule::redirect('user/edit','/' . $UserData->id);
            } else {
                erLhcoreClassModule::redirect('user/userlist');
            }
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