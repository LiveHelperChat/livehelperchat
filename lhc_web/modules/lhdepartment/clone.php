<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSRF Token');
    exit;
}

$Departament = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['departament_id']);
$Departament->id = null;
$Departament->name = 'Clone of ' . $Departament->name;
$Departament->saveThis();

erLhcoreClassModule::redirect('department/departments');
exit;

?>