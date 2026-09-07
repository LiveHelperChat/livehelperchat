<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

$Departament = erLhcoreClassModelCannedMsgReplace::fetch((int)$Params['user_parameters']['id']);

if (!empty($Departament->configuration_array['restricted_access']) && !erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','use_replace_sensitive')) {
    erLhcoreClassModule::redirect('cannedmsg/listreplace');
    exit;
}

$Departament->id = null;
$Departament->identifier = 'Clone of ' . $Departament->identifier;
$Departament->saveThis();

erLhcoreClassModule::redirect('cannedmsg/listreplace');
exit;

?>