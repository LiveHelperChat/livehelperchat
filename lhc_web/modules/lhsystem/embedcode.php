<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/embedcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance();

$tpl->set('locales',$cfgSite->getSetting( 'site', 'available_site_access' ));

$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Embed code')));


?>