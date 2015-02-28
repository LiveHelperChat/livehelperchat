<?php

$faq = erLhcoreClassModelFaq::fetch($Params['user_parameters']['id']);

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('faq.view', array('faq' => $faq));

$tpl = erLhcoreClassTemplate::getInstance('lhfaq/view.tpl.php');

if ( isset($_POST['Update']) )
{
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	$Errors = erLhcoreClassFaq::validateFaq($faq);

	if (count($Errors) == 0) {
		$faq->saveThis();
		$tpl->set('updated',true);
	} else {
		$tpl->set('errors',$Errors);
	}
}

if ( isset($_POST['Cancel']) ) {
	erLhcoreClassModule::redirect('faq/list');
	exit;
}

$tpl->set('faq',$faq);
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' =>erLhcoreClassDesign::baseurl('faq/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faq/list','FAQ')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view','FAQ description')));
?>