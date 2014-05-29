<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/fill.tpl.php');

try {
	$form = erLhAbstractModelForm::fetch((int)$Params['user_parameters']['form_id']);
} catch (Exception $e) {
	erLhcoreClassModule::redirect();
	exit;
}

if ($form->active == 0) {
	erLhcoreClassModule::redirect();
	exit;
}

$tpl->set('content',$form->content_rendered);

if (erLhcoreClassFormRenderer::isCollected()) {
	erLhcoreClassFormRenderer::storeCollectedInformation($form, erLhcoreClassFormRenderer::getCollectedInfo());	
};

$tpl->set('form',$form);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => (string)$form));
$Result['pagelayout'] = $form->pagelayout != '' ? $form->pagelayout : 'form';
$Result['hide_close_window'] = true;