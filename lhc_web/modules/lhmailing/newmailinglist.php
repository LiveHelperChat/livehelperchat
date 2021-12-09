<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/newmailinglist.tpl.php');

$item = new erLhcoreClassModelMailconvMailingList();

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailing/mailinglist');
        exit;
    }

    $items = array();

    $Errors = erLhcoreClassMailconvMailingValidator::validateMailingList($item);

    if (count($Errors) == 0) {
        try {
            $item->user_id = $currentUser->getUserID();
            $item->saveThis();
            erLhcoreClassModule::redirect('mailing/mailinglist');
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
        'url' => erLhcoreClassDesign::baseurl('mailing/mailinglist'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Mailing list')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>