<?php

header('content-type: application/json; charset=utf-8');

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

$user = erLhcoreClassModelUser::fetch($Params['user_parameters']['user_id']);
$userID = $user->id;

$user->id = null;
$user->username = 'copy_' . $user->username;
$user->email = 'copy_' . $user->email;
$user->filepath = '';
$user->filename = '';
$user->saveThis();

foreach (erLhcoreClassModelUserDep::getList(['limit' => false, 'filter' => ['user_id' => $userID]]) as $dep) {
    $dep->id = null;
    $dep->user_id = $user->id;
    $dep->saveThis();
}

foreach (erLhcoreClassModelDepartamentGroupUser::getList(['limit' => false, 'filter' => ['user_id' => $userID]]) as $item) {
    $item->id = null;
    $item->user_id = $user->id;
    $item->saveThis();
}

foreach (erLhcoreClassModelUserSetting::getList(['limit' => false, 'filter' => ['user_id' => $userID]]) as $item) {
    $item->id = null;
    $item->user_id = $user->id;
    $item->saveThis();
}

foreach (erLhcoreClassModelGroupUser::getList(['limit' => false, 'filter' => ['user_id' => $userID]]) as $item) {
    $item->id = null;
    $item->user_id = $user->id;
    $item->saveThis();
}

foreach (erLhcoreClassModelSpeechUserLanguage::getList(['limit' => false, 'filter' => ['user_id' => $userID]]) as $item) {
    $item->id = null;
    $item->user_id = $user->id;
    $item->saveThis();
}

foreach (\LiveHelperChat\Models\Departments\UserDepAlias::getList(['limit' => false, 'filter' => ['user_id' => $userID]]) as $item) {
    $item->id = null;
    $item->filepath = '';
    $item->filename = '';
    $item->user_id = $user->id;
    $item->saveThis();
}

$db->commit();

echo json_encode(['location' => erLhcoreClassDesign::baseurl('user/edit') .'/'. $user->id]);

exit;

?>