<?php

$tpl = erLhcoreClassTemplate::getInstance('lhnotifications/index.tpl.php');
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
    array(
        'url' => erLhcoreClassDesign::baseurl('notifications/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/index', 'Notifications')
    )
);

?>