<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhquestionary/htmlcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('questionary/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Questionary')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','HTML code')))

?>