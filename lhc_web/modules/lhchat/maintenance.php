<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/maintenance.tpl.php');


if ($Params['user_parameters_unordered']['action'] == 'closechats' || $Params['user_parameters_unordered']['action'] == 'purgechats'){

	$currentUser = erLhcoreClassUser::instance();	
	if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
		die('Invalid CSRF Token');
		exit;
	}
	
	if ($Params['user_parameters_unordered']['action'] == 'closechats' ) {
		$tpl->set('closedchats',erLhcoreClassChatWorkflow::automaticChatClosing());
	}
	
	if ($Params['user_parameters_unordered']['action'] == 'purgechats' ) {
		$tpl->set('purgedchats',erLhcoreClassChatWorkflow::automaticChatPurge());
	}
}


$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Maintenance')));