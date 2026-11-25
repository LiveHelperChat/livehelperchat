<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailarchive/process.tpl.php');

$archive = \LiveHelperChat\Models\mailConv\Archive\Range::fetch($Params['user_parameters']['id']);
// Show different ui for backup type archive
$tpl->set('archive', $archive);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
		array('url' => erLhcoreClassDesign::baseurl('mailarchive/archive'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Chat archive')),
		array('url' => erLhcoreClassDesign::baseurl('mailarchive/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list')));
$Result['path'][] = array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process','Process'));


?>