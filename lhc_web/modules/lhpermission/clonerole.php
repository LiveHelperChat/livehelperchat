<?php

$role = erLhcoreClassModelRole::fetch((int)$Params['user_parameters']['role_id']);

$roleFunctions = erLhcoreClassModelRoleFunction::getList(array('limit' => false, 'filter' => array('role_id' => $role->id)));

// Role copy
$role->id = null;
$role->name = erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Copy of') . ' ' . $role->name;
$role->saveThis();

// Role functions copy
foreach ($roleFunctions as $roleFunction) {
    $roleFunction->id = null;
    $roleFunction->role_id = $role->id;
    $roleFunction->saveThis();
}

erLhcoreClassModule::redirect('permission/roles');
exit;

?>