<?php

$tpl = erLhcoreClassTemplate::getInstance('lhtheme/personaltheme.tpl.php');

$enabledPersonal = (int)erLhcoreClassModelUserSetting::getSetting('admin_theme_enabled',0);

$form = erLhAbstractModelAdminTheme::findOne(array('filter' => array('user_id' => $currentUser->getUserID())));

if (!($form instanceof erLhAbstractModelAdminTheme)) {
    $form = new erLhAbstractModelAdminTheme();
    $form->user_id = $currentUser->getUserID();
    $form->saveThis();
}

if ($form->name == '') {
    $form->name = erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','My theme');
}

$fields = include 'lib/core/lhabstract/fields/erlhabstractmodeladmintheme.php';

if ( isset($_POST['CancelAction']) ) {
    erLhcoreClassModule::redirect('theme/adminthemes');
    exit;
}

if (ezcInputForm::hasPostData())
{

    if (isset($_POST['EnabledPersonal']) && $_POST['EnabledPersonal'] == 'on') {
        (int)erLhcoreClassModelUserSetting::setSetting('admin_theme_enabled',1);
        $enabledPersonal = 1;
    } else {
        (int)erLhcoreClassModelUserSetting::setSetting('admin_theme_enabled',0);
        $enabledPersonal = 0;
    }

    $Errors = erLhcoreClassThemeValidator::validateAdminTheme($form);

    $ErrorsAbstract = erLhcoreClassAbstract::validateInput($form);

    if (count($Errors) == 0 && count($ErrorsAbstract) == 0)
    {
        $form->saveOrUpdate();

        if (isset($_POST['SaveAction'])) {
            erLhcoreClassModule::redirect('system/configuration');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    } else {
        $tpl->set('errors',array_merge($Errors,$ErrorsAbstract));
    }
}

$tpl->set('fields',$fields);
$tpl->set('form',$form);
$tpl->set('enabledPersonal',$enabledPersonal == 1);

$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/admintheme.form.angular.js').'"></script>';
$Result['additional_header_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/colorpicker.js').'"></script>';
$Result['additional_header_css'] = '<link rel="stylesheet" type="text/css" href="'.erLhcoreClassDesign::designCSS('css/colorpicker.css').'" />';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhtheme/admin','Personal theme')));

?>