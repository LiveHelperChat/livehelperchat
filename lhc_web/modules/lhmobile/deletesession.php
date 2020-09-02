<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$usession = erLhcoreClassModelUserSession::fetch($Params['user_parameters']['id']);
$usession->removeThis();

erLhcoreClassModule::redirect('mobile/sessions');
exit;

?>