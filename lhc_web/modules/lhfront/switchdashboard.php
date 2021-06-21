<?php

$identifier = 'new_dashboard';
$defaultValue = 1;

if ($Params['user_parameters_unordered']['action'] == 'tabs') {
    $identifier = 'hide_tabs';
} elseif ($Params['user_parameters_unordered']['action'] == 'mode') {
    $identifier = 'dark_mode';
    $defaultValue = 0;
} elseif ($Params['user_parameters_unordered']['action'] == 'left_list') {
    $identifier = 'left_list';
    $defaultValue = 0;
}

$oldSetting = (int)erLhcoreClassModelUserSetting::getSetting($identifier, $defaultValue);
erLhcoreClassModelUserSetting::setSetting($identifier,$oldSetting == 1 ? 0 : 1);

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>