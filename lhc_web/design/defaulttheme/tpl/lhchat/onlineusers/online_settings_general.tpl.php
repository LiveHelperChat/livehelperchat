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

$onlineAttributeFilter = [
    'attrf_key_1' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_key_1',''),
    'attrf_val_1' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_val_1',''),

    'attrf_key_2' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_key_2',''),
    'attrf_val_2' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_val_2',''),

    'attrf_key_3' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_key_3',''),
    'attrf_val_3' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_val_3',''),

    'attrf_key_4' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_key_4',''),
    'attrf_val_4' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_val_4',''),

    'attrf_key_5' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_key_5',''),
    'attrf_val_5' => (string)erLhcoreClassModelUserSetting::getSetting('oattrf_val_5','')
];

?>

