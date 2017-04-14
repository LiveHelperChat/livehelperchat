<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhuser/new.tpl.php');

$UserData = new erLhcoreClassModelUser();

$UserDepartaments = isset($_POST['UserDepartament']) ? $_POST['UserDepartament'] : array();
$userDepartamentsGroup = isset($_POST['UserDepartamentGroup']) ? $_POST['UserDepartamentGroup'] : array();


$tpl->set('tab',$Params['user_parameters_unordered']['tab'] == 'canned' ? 'tab_canned' : '');

$groups_can_edit = erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'editusergroupall') == true ? true : erLhcoreClassGroupRole::getGroupsAccessedByUser(erLhcoreClassUser::instance()->getUserData());

$userParams = array('show_all_pending' => 1, 'global_departament' => array(), 'groups_can_edit' => $groups_can_edit);

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
    
            if (count($userParams['global_departament']) > 0) {
               erLhcoreClassUserDep::addUserDepartaments($userParams['global_departament'], $UserData->id, $UserData);
            }
            
            $UserData->setUserGroups();
                    
            $userPhotoErrors = erLhcoreClassUserValidator::validateUserPhoto($UserData);
            
            if ($userPhotoErrors !== false && count($userPhotoErrors) == 0) {
            	$UserData->saveThis();
            }
    
            erLhcoreClassModelDepartamentGroupUser::addUserDepartmentGroups($UserData, erLhcoreClassUserValidator::validateDepartmentsGroup($UserData));
            
            erLhcoreClassModelUserSetting::setSetting('show_all_pending', $userParams['show_all_pending'], $UserData->id);

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
$tpl->set('show_all_pending',$userParams['show_all_pending']);

$userGroupFilter = $groups_can_edit === true ? array() : array('filterin' => array('id' => $groups_can_edit));
$tpl->set('user_groups_filter',$userGroupFilter);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
	array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','System configuration')),
	array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Users')),
	array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.new_path', array('result' => & $Result));

?>