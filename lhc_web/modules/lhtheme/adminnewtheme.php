<?php

$tpl = erLhcoreClassTemplate::getInstance('lhtheme/adminnewtheme.tpl.php');

$form = new erLhAbstractModelAdminTheme();

$fields = include 'lib/core/lhabstract/fields/erlhabstractmodeladmintheme.php';

if (ezcInputForm::hasPostData()) {

    $Errors = erLhcoreClassThemeValidator::validateAdminTheme($form);

    $ErrorsAbstract = erLhcoreClassAbstract::validateInput($form);

    if (count($Errors) == 0 && count($ErrorsAbstract) == 0) {
        $form->saveThis();
        erLhcoreClassModule::redirect('theme/adminthemes');
        exit();
    } else {       
        $tpl->set('errors',array_merge($Errors,$ErrorsAbstract));
    }
}

$tpl->set('form', $form);
$tpl->set('fields',$fields);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/admintheme.form.angular.js').'"></script>';
$Result['additional_header_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/colorpicker.js').'"></script>';
$Result['additional_header_css'] = '<link rel="stylesheet" type="text/css" href="'.erLhcoreClassDesign::designCSS('css/colorpicker.css').'" />';

$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')),
    array('url' => erLhcoreClassDesign::baseurl('theme/adminthemes'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','Admin themes')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','New admin theme'))
)


?>