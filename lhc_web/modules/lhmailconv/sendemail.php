<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/sendemail.tpl.php');

$item = new erLhcoreClassModelMailconvMessage();

if (ezcInputForm::hasPostData()) {
    $Errors = erLhcoreClassMailconvValidator::validateNewEmail($item);

    if (empty($Errors)) {

        $response = array();
        erLhcoreClassMailconvValidator::sendEmail($item, $response);

        if ($response['send'] == true) {
            $tpl->set('updated',true);
        } else {
            $tpl->set('errors',$response['errors']);
        }

    } else {
        $tpl->set('errors',$Errors);
    }

}

$tpl->setArray(array(
    'item' => $item,
));

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::design('js/tinymce/js/tinymce/tinymce.min.js').'"></script>';

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailconv/conversations'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>