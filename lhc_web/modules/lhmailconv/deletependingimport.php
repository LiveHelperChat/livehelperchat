<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$item = \LiveHelperChat\Models\mailConv\PendingImport::fetch($Params['user_parameters']['id']);

if ($item instanceof \LiveHelperChat\Models\mailConv\PendingImport) {
    $item->removeThis();
}

erLhcoreClassModule::redirect('mailconv/pendingimport');
exit;
