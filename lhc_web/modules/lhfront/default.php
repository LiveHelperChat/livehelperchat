<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfront/default_new.tpl.php');

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

if (is_numeric($Params['user_parameters_unordered']['mid'])) {
    $tpl->set('load_mail_id',$Params['user_parameters_unordered']['mid']);
}

$Result['content'] = $tpl->fetch();
/*$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.online.min.js;vendor/jqueryui/core.min.js;vendor/jqueryui/mouse.min.js;vendor/jqueryui/widget.min.js;vendor/jqueryui/sortable.min.js;js/lhc.dashboard.min.js').'"></script>';*/
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('vendor/jqueryui/core.min.js;vendor/jqueryui/mouse.min.js;vendor/jqueryui/widget.min.js;vendor/jqueryui/sortable.min.js;js/lhc.dashboard.min.js').'"></script>';
$Result['additional_footer_js'] .= '<script type="module" src="'.erLhcoreClassDesign::designJSStatic('js/svelte/public/build/onlinevisitors.js').'"></script>';

?>