<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/htmlcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('chatbox/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/htmlcode','HTML code')))

?>