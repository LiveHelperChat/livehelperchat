<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatarchive/archive.tpl.php');

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Chat archive')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chatarchive.archive_path',array('result' => & $Result));
?>