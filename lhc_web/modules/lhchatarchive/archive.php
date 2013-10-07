<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatarchive/archive.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Archive utility')))

?>