<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('browseoffer.htmlcode', []);

$tpl = erLhcoreClassTemplate::getInstance( 'lhbrowseoffer/htmlcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));

$Result['content'] = $tpl->fetch();
$Result['path'] = [
	['url' => erLhcoreClassDesign::baseurl('browseoffer/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index', 'Browse offers')],
	['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode', 'HTML code')]
];
