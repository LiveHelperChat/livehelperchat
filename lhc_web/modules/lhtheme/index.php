<?php

$tpl = erLhcoreClassTemplate::getInstance('lhtheme/index.tpl.php');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')));

?>