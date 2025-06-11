<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/newresponsetemplate.tpl.php');

$item = new erLhcoreClassModelMailconvResponseTemplate();

if (ezcInputForm::hasPostData()) {

    $items = array();

    $Errors = erLhcoreClassMailconvValidator::validateResponseTemplate($item);

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $Errors[] = 'Invalid CSRF Token!';
    }

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            erLhcoreClassModule::redirect('mailconv/responsetemplates');
            exit;
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }

    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('item', $item);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJSStatic('js/tinymce/js/tinymce/tinymce.min.js').'"></script>';

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailconv/responsetemplates'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Response Templates')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>