<?php

$currentUser = erLhcoreClassUser::instance();

// Handle delete action
if (isset($Params['user_parameters_unordered']['action']) && $Params['user_parameters_unordered']['action'] == 'delete') {
    if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
        die('Invalid CSRF Token');
        exit;
    }
    
    $item = \LiveHelperChat\Models\mailConv\PendingImport::fetch((int)$Params['user_parameters_unordered']['id']);
    if ($item instanceof \LiveHelperChat\Models\mailConv\PendingImport) {
        $item->removeThis();
    }
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/manualimport.tpl.php');

$mailboxes = erLhcoreClassModelMailconvMailbox::getList(['limit' => false, 'sort' => 'name ASC']);

$tpl->set('mailboxes', $mailboxes);

if (isset($Params['user_parameters_unordered']['id']) && is_numeric($Params['user_parameters_unordered']['id']) && $Params['user_parameters_unordered']['id'] > 0) {
    $item = \LiveHelperChat\Models\mailConv\PendingImport::fetch($Params['user_parameters_unordered']['id']);
    if (!($item instanceof \LiveHelperChat\Models\mailConv\PendingImport)) {
        die('Invalid record!');
    }
} else {
    $item = new \LiveHelperChat\Models\mailConv\PendingImport();
    $item->status = \LiveHelperChat\Models\mailConv\PendingImport::PENDING;
    $item->created_at = time();
    $item->updated_at = time();
}

if (ezcInputForm::hasPostData() && !(!isset($_POST['csfr_token']) || !erLhcoreClassUser::instance()->validateCSFRToken($_POST['csfr_token']))) {
    $Errors = array();
    $item->mailbox_id = (int)($_POST['mailbox_id'] ?? 0);
    $item->uid = (int)($_POST['uid'] ?? 0);
    $item->status = (int)($_POST['status'] ?? 0);
    $item->attempt = (int)($_POST['attempt'] ?? 0);
    $item->updated_at = time();

    // Basic validation
    if ($item->mailbox_id == 0) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailbox is required');
    }

    if ($item->uid == 0) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','UID is required');
    }

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            $tpl->set('updated', true);
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }
    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('item', $item);

echo $tpl->fetch();
exit;
