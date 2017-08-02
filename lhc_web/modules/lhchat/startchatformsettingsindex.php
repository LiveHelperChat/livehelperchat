<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/startchatformsettingsindex.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Start chat form settings')));


?>