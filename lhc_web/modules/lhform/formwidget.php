<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

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

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/fill.tpl.php');
$tpl->set('content',$form->content_rendered);

if (erLhcoreClassFormRenderer::isCollected()) {
	erLhcoreClassFormRenderer::storeCollectedInformation($form, erLhcoreClassFormRenderer::getCollectedInfo());
};

$tpl->set('form',$form);
$tpl->set('embed_mode',true);
$tpl->set('action_url',erLhcoreClassDesign::baseurl('form/formwidget'));

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;
$Result['dynamic_height_append'] = 10;
$Result['dynamic_height_message'] = 'lhc_sizing_form_embed';
$Result['pagelayout_css_append'] = 'embed-widget';