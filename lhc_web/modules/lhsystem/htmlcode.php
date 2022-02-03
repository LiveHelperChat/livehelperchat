<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/htmlcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance(); 
 
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));

/**
 * Append user departments filter
 * */
$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
if ($userDepartments !== true){
	$departmentParams['filterin']['id'] = $filter['filterin']['dep_id'] = $userDepartments;
}
$tpl->set('departmentParams',$departmentParams);
$departmentParams['limit'] = false;
$departmentParams['sort'] = '`name` ASC';

$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code'))
)


?>