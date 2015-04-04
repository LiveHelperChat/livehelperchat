<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/maintenance.tpl.php');


if ($Params['user_parameters_unordered']['action'] == 'closechats' || $Params['user_parameters_unordered']['action'] == 'purgechats' || $Params['user_parameters_unordered']['action'] == 'updateduration'){

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
	
	if ($Params['user_parameters_unordered']['action'] == 'updateduration' ) {		
		$db = ezcDbInstance::get();
		$db->query('UPDATE lh_chat SET chat_duration = (SELECT MAX(lh_msg.time) FROM lh_msg WHERE lh_msg.chat_id = lh_chat.id AND lh_msg.user_id = 0)-(lh_chat.time+lh_chat.wait_time)');
		$db->query('UPDATE lh_chat SET chat_duration = 0 WHERE chat_duration < 0');			
		$tpl->set('updatedduration',true);
	}
}


$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/maintenance','Maintenance')));


erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.maintenance_path',array('result' => & $Result));