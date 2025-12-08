<?php

$item = \LiveHelperChat\Models\mailConv\PendingImport::fetch($Params['user_parameters']['id']);

if ($item instanceof \LiveHelperChat\Models\mailConv\PendingImport) {
    $item->removeThis();
}

erLhcoreClassModule::redirect('mailconv/pendingimport');
exit;
