<?php

$group = erLhcoreClassModelGroup::fetch((int)$Params['user_parameters']['group_id']);

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$groupRoles = erLhcoreClassModelGroupRole::getList(array('limit' => false, 'filter' => array('group_id' => $group->id)));

$workWith = erLhcoreClassModelGroupWork::getList(array('filter' => array('group_id' => $group->id)));

// Role copy
$group->id = null;
$group->name = erTranslationClassLhTranslation::getInstance()->getTranslation('permission/roles','Copy of') . ' ' . $group->name;
$group->saveThis();

// Role functions copy
foreach ($groupRoles as $groupRole) {
    $groupRole->id = null;
    $groupRole->group_id = $group->id;
    $groupRole->saveThis();
}

// Copy groups current group can work with
foreach ($workWith as $work) {
    $work->id = null;
    $work->group_id = $group->id;
    $work->saveThis();
}

erLhcoreClassModule::redirect('user/grouplist');
exit;

?>