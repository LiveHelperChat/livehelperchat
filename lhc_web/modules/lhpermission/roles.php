<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpermission/roles.tpl.php');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','List of roles'))
)

?>