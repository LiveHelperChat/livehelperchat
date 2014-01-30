<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/update.tpl.php');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Live Helper Chat update')));


?>