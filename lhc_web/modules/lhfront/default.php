<?php

$detect = new Mobile_Detect;

// New dashboard available only on desktop
$device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);

if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1 && $device_type == 0) {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhfront/default_new.tpl.php');
    $tpl->set('new_dashboard',true);
    $Result['body_class'] = 'h-100 dashboard-height';
    $Result['hide_right_column'] = true;
} else {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhfront/default.tpl.php');
    $Result['hide_right_column'] = erLhcoreClassModelChatConfig::fetch('hide_right_column_frontpage')->current_value == 1;
}

$tpl->set('geo_location_data',erLhcoreClassModelChatConfig::fetch('geo_location_data')->data);
$tpl->set('tracking_enabled',erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1);
/**
 * Append user departments filter
 * */
$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
if ($userDepartments !== true) {
    $departmentParams['filterin']['id'] = $userDepartments;
    if (!$currentUser->hasAccessTo('lhchat','sees_all_online_visitors')) {
        $filter['filterin']['dep_id'] = $userDepartments;
    }
}

$departmentParams['sort'] = 'sort_priority ASC, name ASC';

$tpl->set('departmentParams',$departmentParams);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.online.min.js;vendor/jqueryui/core.min.js;vendor/jqueryui/mouse.min.js;vendor/jqueryui/widget.min.js;vendor/jqueryui/sortable.min.js;js/lhc.dashboard.min.js').'"></script>';


?>