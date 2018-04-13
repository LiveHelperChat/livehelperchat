<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$gbot = erLhcoreClassModelGenericBotBot::fetch($Params['user_parameters']['id']);
$gbot->removeThis();

erLhcoreClassModule::redirect('genericbot/list');
exit;

?>