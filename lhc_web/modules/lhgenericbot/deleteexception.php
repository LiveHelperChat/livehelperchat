<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$gbot = erLhcoreClassModelGenericBotException::fetch($Params['user_parameters']['id']);
$gbot->removeThis();

erLhcoreClassModule::redirect('genericbot/listexceptions');
exit;

?>