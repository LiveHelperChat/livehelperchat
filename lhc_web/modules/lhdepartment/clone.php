<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

$Departament = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['departament_id']);
$Departament->id = null;
$Departament->created_at = 0;
$Departament->name = 'Clone of ' . $Departament->name;
erLhcoreClassAbstract::setAuditTrail($Departament, $currentUser->getUserID());
$Departament->saveThis();

erLhcoreClassModule::redirect('department/departments');
exit;

?>