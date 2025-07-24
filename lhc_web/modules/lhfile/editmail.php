<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfile/editmail.tpl.php');

$file = erLhcoreClassModelMailconvFile::fetch((int)$Params['user_parameters']['file_id']);

$tpl->set('item', $file);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('file/listmail'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of mail files')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Mail file details')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.editmail_path', array('result' => & $Result));

?>
