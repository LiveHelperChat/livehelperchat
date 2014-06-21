<?php

if ((string)$Params['user_parameters_unordered']['action'] == 'comparedb') {
	$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update/comparedb.tpl.php');
	$updateLinks = erLhcoreClassUpdate::getMissingUpdates($_POST['data']);
	$tpl->set('links',$updateLinks);
	echo json_encode(array('result' => $tpl->fetch()));
	exit;
}

if ((string)$Params['user_parameters_unordered']['action'] == 'statusdb' || (string)$Params['user_parameters_unordered']['action'] == 'statusdbdoupdate') {
	$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update/statusdb.tpl.php');
	
	if ((string)$Params['user_parameters_unordered']['action'] == 'statusdbdoupdate'){
		erLhcoreClassUpdate::doTablesUpdate(json_decode(file_get_contents('doc/update_db/structure.json'),true)/* $_POST['data'] */);
	}
	
	$tables = erLhcoreClassUpdate::getTablesStatus(json_decode(file_get_contents('doc/update_db/structure.json'),true)/* $_POST['data'] */);
	$tpl->set('tables',$tables);
	echo json_encode(array('result' => $tpl->fetch()));
	exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update.tpl.php');
$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Live Helper Chat update information')));


?>