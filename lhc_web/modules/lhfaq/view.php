<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfaq/view.tpl.php');

$faq = erLhcoreClassModelFaq::fetch($Params['user_parameters']['id']);

if ( isset($_POST['Update']) )
{
	$Errors = erLhcoreClassFaq::validateFaq($faq);

	if (count($Errors) == 0) {
		erLhcoreClassFaq::getSession()->SaveOrUpdate($faq);
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
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faqg/view','FAQ description')));
?>