<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfaq/htmlcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));


$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('faq/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','FAQ')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faq/htmlcode','HTML code')))

?>