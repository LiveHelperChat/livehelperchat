<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

$Departament = erLhcoreClassModelCannedMsgReplace::fetch((int)$Params['user_parameters']['id']);
$Departament->id = null;
$Departament->identifier = 'Clone of ' . $Departament->identifier;
$Departament->saveThis();

erLhcoreClassModule::redirect('cannedmsg/listreplace');
exit;

?>