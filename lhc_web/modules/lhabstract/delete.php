<?php

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('abstract.delete_'.strtolower($Params['user_parameters']['identifier']).'_general', array());

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

$ObjectData = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModel'.$Params['user_parameters']['identifier'], (int)$Params['user_parameters']['object_id'] );

$object_trans = $ObjectData->getModuleTranslations();

if (isset($object_trans['permission']) && !$currentUser->hasAccessTo($object_trans['permission']['module'],$object_trans['permission']['function'])) {
	erLhcoreClassModule::redirect();
	exit;
}

if ( method_exists($ObjectData,'checkPermission') ) {
	if ( $ObjectData->checkPermission() === false ) {
		erLhcoreClassModule::redirect();
		exit;
	}
}

$ObjectData->removeThis();

erLhcoreClassLog::logObjectChange(array(
    'object' => $ObjectData,
    'check_log' => true,
    'action' => 'Delete',
    'msg' => array(
        'delete' => $ObjectData->getState(),
        'user_id' => $currentUser->getUserID()
    )
));

$cache = CSCacheAPC::getMem();
$cache->increaseCacheVersion('site_attributes_version');

erLhcoreClassModule::redirect('abstract/list','/'.$Params['user_parameters']['identifier']);
exit;

?>