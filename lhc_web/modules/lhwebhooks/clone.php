<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

$item = erLhcoreClassModelChatWebhook::fetch((int)$Params['user_parameters']['id']);

if (!($item instanceof erLhcoreClassModelChatWebhook)) {
    erLhcoreClassModule::redirect('webhooks/configuration');
    exit;
}

$item->id = null;
$item->created_at = 0;
$item->updated_at = 0;
$item->name = mb_substr('Clone of ' . $item->name, 0, 50);
$item->status = '';
$item->disabled = 1;

erLhcoreClassAbstract::setAuditTrail($item, $currentUser->getUserID());
$item->saveThis();

erLhcoreClassModule::redirect('webhooks/edit', '/' . $item->id);
exit;

?>
