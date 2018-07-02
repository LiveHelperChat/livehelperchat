<?php

$tpl = erLhcoreClassTemplate::getInstance('lhnotifications/index.tpl.php');
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array(
        'url' => erLhcoreClassDesign::baseurl('notifications/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhelasticsearch/module', 'Notifications')
    )
);

?>