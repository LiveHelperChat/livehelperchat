<?php

$tpl = new erLhcoreClassTemplate( 'lhsystem/htmlcode.tpl.php');

$cfgSite = erConfigClassLhConfig::getInstance(); 
 
$tpl->set('locales',$cfgSite->conf->getSetting( 'site', 'available_locales' ));

$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code'))
)


?>