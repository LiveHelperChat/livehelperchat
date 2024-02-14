<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhmailarchive/archive.tpl.php');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Mail archive')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailarchive.archive_path',array('result' => & $Result));
?>