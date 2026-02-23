<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$identifier = null;
$defaultValue = null;

if ($Params['user_parameters_unordered']['action'] == 'tabs') {
    $identifier = 'hide_tabs';
    $defaultValue = 1;
} elseif ($Params['user_parameters_unordered']['action'] == 'static_order') {
    $identifier = 'static_order';
    $defaultValue = 0;
} elseif ($Params['user_parameters_unordered']['action'] == 'mode') {
    $identifier = 'dark_mode';
    $defaultValue = 0;
} elseif ($Params['user_parameters_unordered']['action'] == 'left_list') {
    $identifier = 'left_list';
    $defaultValue = 0;
} elseif ($Params['user_parameters_unordered']['action'] == 'column_chats') {
    $identifier = 'column_chats';
    $defaultValue = 0;
} elseif ($Params['user_parameters_unordered']['action'] == 'new_editor') {
    $identifier = 'new_editor';
    $defaultValue = 0;
}

if ($identifier !== null) {
    $oldSetting = (int)erLhcoreClassModelUserSetting::getSetting($identifier, $defaultValue);
    erLhcoreClassModelUserSetting::setSetting($identifier, $oldSetting == 1 ? 0 : 1);
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>