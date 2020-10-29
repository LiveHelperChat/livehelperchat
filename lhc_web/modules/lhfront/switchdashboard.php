<?php

$identifier = 'new_dashboard';
$defaultValue = 1;

if ($Params['user_parameters_unordered']['action'] == 'tabs') {
    $identifier = 'hide_tabs';
}

$oldSetting = (int)erLhcoreClassModelUserSetting::getSetting($identifier,1);
erLhcoreClassModelUserSetting::setSetting($identifier,$oldSetting == 1 ? 0 : 1);

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>