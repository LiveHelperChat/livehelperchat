<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.embedcode', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/embedcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();
$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('form/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('form/index','Form')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code')))

?>