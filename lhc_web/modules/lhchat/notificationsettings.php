<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/notificationsettings.tpl.php');
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','New chat notification settings')));

?>