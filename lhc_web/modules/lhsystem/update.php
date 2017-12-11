<?php

if ((string)$Params['user_parameters_unordered']['action'] == 'statusdb' || (string)$Params['user_parameters_unordered']['action'] == 'statusdbdoupdate') {

	if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
		echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));		
		exit;
	}

	$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update/statusdb.tpl.php');
	
	if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == false) {
	   $contentData = erLhcoreClassModelChatOnlineUser::executeRequest('https://raw.githubusercontent.com/LiveHelperChat/livehelperchat/master/lhc_web/doc/update_db/structure.json');
	} else {
	   $contentData = file_get_contents('doc/update_db/structure.json');
	}
	
	if ((string)$Params['user_parameters_unordered']['action'] == 'statusdbdoupdate'){	
		erLhcoreClassUpdate::doTablesUpdate(json_decode($contentData,true));
	}
		
	$tables = erLhcoreClassUpdate::getTablesStatus(json_decode($contentData,true));
	$tpl->set('tables',$tables);
	echo json_encode(array('result' => $tpl->fetch()));
	exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update.tpl.php');
$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Live Helper Chat update information')));


?>