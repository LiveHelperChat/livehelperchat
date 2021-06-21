<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$webhook = erLhcoreClassModelChatIncomingWebhook::fetch($Params['user_parameters']['id']);
$webhook->removeThis();

erLhcoreClassModule::redirect('webhooks/incomingwebhooks');
exit;

?>