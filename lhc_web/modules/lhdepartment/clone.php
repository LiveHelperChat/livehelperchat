<?php

$Departament = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['departament_id']);
$Departament->id = null;
$Departament->name = 'Clone of ' . $Departament->name;
$Departament->saveThis();

erLhcoreClassModule::redirect('department/departments');
exit;

?>