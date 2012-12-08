<?php

$tpl = new erLhcoreClassTemplate('lhuser/grouplist.tpl.php');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','System configuration')),

array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/grouplist','Groups'))
)

?>