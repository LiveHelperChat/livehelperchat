<?php 
$browserNotification = (int)erLhcoreClassModelUserSetting::getSetting('new_user_bn',(int)(0));
$soundUserNotification = (int)erLhcoreClassModelUserSetting::getSetting('new_user_sound',(int)(0));
$onlineDepartment = (int)erLhcoreClassModelUserSetting::getSetting('o_department',(int)(0));
$ouserTimeout = (int)erLhcoreClassModelUserSetting::getSetting('ouser_timeout',(int)(3600));
$oupdTimeout = (int)erLhcoreClassModelUserSetting::getSetting('oupdate_timeout',(int)(10));
$omaxRows = (int)erLhcoreClassModelUserSetting::getSetting('omax_rows',(int)(50));
$ogroupBy = (string)erLhcoreClassModelUserSetting::getSetting('ogroup_by','none');
$omapDepartment = (int)erLhcoreClassModelUserSetting::getSetting('omap_depid',0);
$omapMarkerTimeout = (int)erLhcoreClassModelUserSetting::getSetting('omap_mtimeout',30);
	
$onlineCheck = (int)erLhcoreClassModelChatConfig::fetch('checkstatus_timeout')->current_value;
if ($onlineCheck > 0) {
	$onlineCheck = ",online_user:(ou.last_check_time_ago < " . ($onlineCheck+3) . ")";
} else {
	$onlineCheck = '';
}
	
?>