<?php
$canListOnlineUsers = false;
$canListOnlineUsersAll = false;
$currentUser = erLhcoreClassUser::instance();

if (erLhcoreClassModelChatConfig::fetch('list_online_operators')->current_value == 1) {
    $canListOnlineUsers = $currentUser->hasAccessTo('lhuser', 'userlistonline');
    $canListOnlineUsersAll = $currentUser->hasAccessTo('lhuser', 'userlistonlineall');
}

$canseedepartmentstats = $currentUser->hasAccessTo('lhuser', 'canseedepartmentstats');

$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list', 1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list', 1);

$closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list', 0) && erLhcoreClassModelChatConfig::fetchCache('list_closed')->current_value == 1;

$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list', 0) && erLhcoreClassModelChatConfig::fetchCache('list_unread')->current_value == 1;

$mchatsTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_mchats_list', 1);

$frontTabsOrder = explode(',', str_replace(' ','',erLhcoreClassModelChatConfig::fetch('front_tabs')->current_value));

$dashboardOrder = json_decode(erLhcoreClassModelUserSetting::getSetting('dwo',''),true);

if ($dashboardOrder === null) {
    if ($dashboardOrder == '') {
        $dashboardOrder = json_decode(erLhcoreClassModelChatConfig::fetch('dashboard_order')->current_value,true);
    }
}

$widgetsUser = array();
foreach ($dashboardOrder as $widgetsColumn) {
    foreach ($widgetsColumn as $widget) {
        $widgetsUser[] = $widget;
    }
}

?>