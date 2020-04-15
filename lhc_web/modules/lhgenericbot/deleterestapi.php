<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$item = erLhcoreClassModelGenericBotRestAPI::fetch($Params['user_parameters']['id']);
$item->removeThis();

erLhcoreClassModule::redirect('genericbot/listrestapi');
exit;

?>