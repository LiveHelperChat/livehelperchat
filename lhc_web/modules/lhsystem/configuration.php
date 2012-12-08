<?php

$tpl = new erLhcoreClassTemplate( 'lhsystem/configuration.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')))

?>