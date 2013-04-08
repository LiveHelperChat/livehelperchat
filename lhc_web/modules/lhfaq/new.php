<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfaq/new.tpl.php');

$faq = new erLhcoreClassModelFaq();

if ( isset($_POST['Save']) )
{
	$Errors = erLhcoreClassFaq::validateFaq($faq);

	if (count($Errors) == 0) {
		erLhcoreClassFaq::getSession()->SaveOrUpdate($faq);
		erLhcoreClassModule::redirect('faq/view','/'.$faq->id);
		exit;
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
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','New question')));
?>