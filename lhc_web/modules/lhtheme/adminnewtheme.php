<?php

$tpl = erLhcoreClassTemplate::getInstance('lhtheme/adminnewtheme.tpl.php');

$form = new erLhAbstractModelAdminTheme();

if (ezcInputForm::hasPostData()) {

    $Errors = erLhcoreClassThemeValidator::validateAdminTheme($form);

    if (! empty($Errors)) {
        $tpl->set('errors', $Errors);
    } else {
        $form->saveThis();
        erLhcoreClassModule::redirect('theme/adminthemes');
        exit();
    }
}

$tpl->set('form', $form);
$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/admintheme.form.angular.js').'"></script>';
$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')),
    array('url' => erLhcoreClassDesign::baseurl('theme/adminthemes'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','Admin themes')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','New admin theme'))
)


?>