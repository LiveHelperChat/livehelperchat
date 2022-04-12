<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/newpersonalmailboxgroup.tpl.php');

$item = new erLhcoreClassModelMailconvPersonalMailboxGroup();

if (ezcInputForm::hasPostData()) {

    $items = array();

    $Errors = erLhcoreClassMailconvValidator::validatePersonalMailboxGroup($item);

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $Errors[] = 'Invalid CSRF Token!';
    }

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            erLhcoreClassModule::redirect('mailconv/personalmailboxgroups');
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
        'url' => erLhcoreClassDesign::baseurl('mailconv/personalmailboxgroups'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Personal Mailbox Group')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>