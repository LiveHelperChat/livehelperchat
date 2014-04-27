<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/fill.tpl.php');

$form = erLhAbstractModelForm::fetch((int)$Params['user_parameters']['form_id']);
$tpl->set('content',$form->content_rendered);

if (erLhcoreClassFormRenderer::isCollected()) {
	erLhcoreClassFormRenderer::storeCollectedInformation($form, erLhcoreClassFormRenderer::getCollectedInfo());
};

$tpl->set('form',$form);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Fill')));
$Result['pagelayout'] = 'userchat';

?>