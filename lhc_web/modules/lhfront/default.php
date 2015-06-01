<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfront/default.tpl.php');
$tpl->set('geo_location_data',erLhcoreClassModelChatConfig::fetch('geo_location_data')->data);
$tpl->set('tracking_enabled',erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1);
/**
 * Append user departments filter
 * */
$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
if ($userDepartments !== true){
    $departmentParams['filterin']['id'] = $userDepartments;
    if (!$currentUser->hasAccessTo('lhchat','sees_all_online_visitors')) {
        $filter['filterin']['dep_id'] = $userDepartments;
    }
}

$tpl->set('departmentParams',$departmentParams);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.online.min.js').'"></script>';
$Result['hide_right_column'] = erLhcoreClassModelChatConfig::fetch('hide_right_column_frontpage')->current_value == 1;


?>