<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/newcampaign.tpl.php');

$item = new erLhcoreClassModelMailconvMailingCampaign();

if (ezcInputForm::hasPostData()) {

    $items = array();

    $Errors = erLhcoreClassMailconvMailingValidator::validateCampaign($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            erLhcoreClassModule::redirect('mailing/campaign');
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
        'url' => erLhcoreClassDesign::baseurl('mailing/campaign'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Campaigns')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>