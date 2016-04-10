<?php

$tpl = erLhcoreClassTemplate::getInstance('lhtheme/adminthemeedit.tpl.php');

$form = erLhAbstractModelAdminTheme::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['CancelAction']) ) {
	erLhcoreClassModule::redirect('theme/adminthemes');
	exit;
}

if (ezcInputForm::hasPostData())
{
    $Errors = erLhcoreClassThemeValidator::validateAdminTheme($form);

	if (count($Errors) == 0)
	{
	    $form->saveThis();
	    
		if (isset($_POST['SaveAction'])) {
			erLhcoreClassModule::redirect('theme/adminthemes');
			exit;
		} else {
			$tpl->set('updated',true);
		}

	}  else {
		$tpl->set('errors',$Errors);
	}
}

$tpl->set('form',$form);
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/admintheme.form.angular.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')),
		array('url' => erLhcoreClassDesign::baseurl('theme/adminthemes'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','Admin themes')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','Edit admin theme')))


?>