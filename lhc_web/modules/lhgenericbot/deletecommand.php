<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$command = erLhcoreClassModelGenericBotCommand::fetch($Params['user_parameters']['id']);
$command->removeThis();

erLhcoreClassModule::redirect('genericbot/commands');
exit;

?>