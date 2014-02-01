<?php

if ((string)$Params['user_parameters_unordered']['action'] == 'comparedb') {
	$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update/comparedb.tpl.php');
	$updateLinks = erLhcoreClassUpdate::getMissingUpdates($_POST['data']);
	$tpl->set('links',$updateLinks);
	echo json_encode(array('result' => $tpl->fetch()));
	exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update.tpl.php');
$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Live Helper Chat update information')));


?>