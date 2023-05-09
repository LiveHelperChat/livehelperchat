<?php

$detect = new Mobile_Detect;

// New dashboard available only on desktop
//$device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);

$new_dashboard = (int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.setting.new_dashboard',array('new_dashboard' => & $new_dashboard));

if ($new_dashboard == 1) {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhfront/default_new.tpl.php');
    $tpl->set('new_dashboard',true);
} else {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhfront/default.tpl.php');
}

$tpl->set('geo_location_data',erLhcoreClassModelChatConfig::fetch('geo_location_data')->data);
$tpl->set('tracking_enabled',erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1);
/**
 * Append user departments filter
 * */
$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
if ($userDepartments !== true) {
    $departmentParams['filterin']['id'] = $userDepartments;
    if (!$currentUser->hasAccessTo('lhchat','sees_all_online_visitors')) {
        $filter['filterin']['dep_id'] = $userDepartments;
    }
}

$departmentParams['sort'] = 'sort_priority ASC, name ASC';

$tpl->set('departmentParams',$departmentParams);

if (is_numeric($Params['user_parameters_unordered']['cid'])) {
    $tpl->set('load_chat_id',$Params['user_parameters_unordered']['cid']);
}

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.online.min.js;vendor/jqueryui/core.min.js;vendor/jqueryui/mouse.min.js;vendor/jqueryui/widget.min.js;vendor/jqueryui/sortable.min.js;js/lhc.dashboard.min.js').'"></script>';


?>