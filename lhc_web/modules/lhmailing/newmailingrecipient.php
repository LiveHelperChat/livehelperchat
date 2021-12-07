<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/newmailingrecipient.tpl.php');

$item = new erLhcoreClassModelMailconvMailingRecipient();

if (is_array($Params['user_parameters_unordered']['ml'])){
    $item->ml_ids_front = $Params['user_parameters_unordered']['ml'];
}

if (ezcInputForm::hasPostData()) {

    $items = array();

    $Errors = erLhcoreClassMailconvMailingValidator::validateMailingRecipient($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            erLhcoreClassModule::redirect('mailing/mailingrecipient');
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

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailing/mailingrecipient'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Recipients')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>