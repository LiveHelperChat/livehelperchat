<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/embedcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' =>erLhcoreClassDesign::baseurl('chatbox/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox')),
		array('url' =>erLhcoreClassDesign::baseurl('chatbox/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox list')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code')))

?>