<?php 
$browserNotification = (int)erLhcoreClassModelUserSetting::getSetting('new_user_bn',(int)(0));
$soundUserNotification = (int)erLhcoreClassModelUserSetting::getSetting('new_user_sound',(int)(0));
$onlineDepartmentSettings = json_decode(erLhcoreClassModelUserSetting::getSetting('dw_filters', '{}', false, false, true),true);

if (isset($onlineDepartmentSettings['department_online']) && $onlineDepartmentSettings['department_online'] != '') {
    $onlineDepartment =  explode('/',$onlineDepartmentSettings['department_online']);
    erLhcoreClassChat::validateFilterIn($onlineDepartment);
} else {
    $onlineDepartment = [];
}

if (isset($onlineDepartmentSettings['department_dpgroups_online']) && $onlineDepartmentSettings['department_dpgroups_online'] != '') {
    $onlineDepartmentGroups =  explode('/',$onlineDepartmentSettings['department_dpgroups_online']);
    erLhcoreClassChat::validateFilterIn($onlineDepartmentGroups);
} else {
    $onlineDepartmentGroups = [];
}

$ouserTimeout = (int)erLhcoreClassModelUserSetting::getSetting('ouser_timeout',(int)(3600));
$oupdTimeout = (int)erLhcoreClassModelUserSetting::getSetting('oupdate_timeout',(int)(10));
$omaxRows = (int)erLhcoreClassModelUserSetting::getSetting('omax_rows',(int)(50));
$ogroupBy = (string)erLhcoreClassModelUserSetting::getSetting('ogroup_by','none');
$oCountry = (string)erLhcoreClassModelUserSetting::getSetting('ocountry','none');

$oTimeOnSite = (string)erLhcoreClassModelUserSetting::getSetting('otime_on_site','');
$oTimeOnSite = $oTimeOnSite == 'none' ? '' : $oTimeOnSite;

$omapDepartment = (int)erLhcoreClassModelUserSetting::getSetting('omap_depid',0);
$omapMarkerTimeout = (int)erLhcoreClassModelUserSetting::getSetting('omap_mtimeout',30);
$onlineVisitorOnly = (int)erLhcoreClassModelUserSetting::getSetting('online_connected',0);

?>

