<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfaq/embedcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('faq/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view','FAQ')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code')))

?>