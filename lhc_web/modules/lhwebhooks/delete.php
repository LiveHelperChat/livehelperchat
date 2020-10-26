<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$webhook = erLhcoreClassModelChatWebhook::fetch($Params['user_parameters']['id']);
$webhook->removeThis();

erLhcoreClassModule::redirect('webhooks/configuration');
exit;

?>