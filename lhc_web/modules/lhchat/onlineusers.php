<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/onlineusers.tpl.php');

if (is_numeric($Params['user_parameters_unordered']['clear_list']) && $Params['user_parameters_unordered']['clear_list'] == 1) {

	if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
		die('Invalid CSRF Token');
		exit;
	}

    erLhcoreClassModelChatOnlineUser::cleanAllRecords();
    erLhcoreClassModule::redirect('chat/onlineusers');
    exit;
}

if (is_numeric($Params['user_parameters_unordered']['deletevisitor']) && $Params['user_parameters_unordered']['deletevisitor'] > 0) {
	
	if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
		die('Invalid CSRF Token');
		exit;
	}
	
	try {
		$visitor = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters_unordered']['deletevisitor']);
		$visitor->removeThis();
	} catch (Exception $e) {
        print_r($e);
        exit;
	}

    erLhcoreClassModule::redirect('chat/onlineusers');
    exit;
}

$is_ajax = isset($Params['user_parameters_unordered']['method']) && $Params['user_parameters_unordered']['method'] == 'ajax';
$timeout = isset($Params['user_parameters_unordered']['timeout']) && is_numeric($Params['user_parameters_unordered']['timeout']) ? (int)$Params['user_parameters_unordered']['timeout'] : 30;
$maxrows = isset($Params['user_parameters_unordered']['maxrows']) && is_numeric($Params['user_parameters_unordered']['maxrows']) ? (int)$Params['user_parameters_unordered']['maxrows'] : 50;

$filter = array('offset' => 0, 'limit' => $maxrows, 'sort' => 'last_visit DESC','filtergt' => array('last_visit' => (time()-$timeout)));
$department = isset($Params['user_parameters_unordered']['department']) && is_numeric($Params['user_parameters_unordered']['department']) ? (int)$Params['user_parameters_unordered']['department'] : false;
if ($department !== false){
	$filter['filter']['dep_id'] = $department;
}

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

if ($is_ajax == true) {
    header('content-type: application/json; charset=utf-8');
	$items = erLhcoreClassModelChatOnlineUser::getList($filter);
	
	erLhcoreClassChat::$trackActivity = (int)erLhcoreClassModelChatConfig::fetchCache('track_activity')->current_value == 1;
	erLhcoreClassChat::$trackTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('checkstatus_timeout')->current_value;
	
	erLhcoreClassChat::prefillGetAttributes($items,array('online_attr_system_array','notes_intro','last_check_time_ago','visitor_tz_time','last_visit_seconds_ago','lastactivity_ago','time_on_site_front','can_view_chat','operator_user_send','operator_user_string','first_visit_front','last_visit_front','online_status'),array('notes','online_attr_system'),array('do_not_clean' => true));
	echo json_encode(array_values($items));
	exit;
}

$tpl->set('departmentParams',$departmentParams);
$tpl->set('tracking_enabled',erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1);
$tpl->set('geo_location_data',erLhcoreClassModelChatConfig::fetch('geo_location_data')->data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.onlineusers_path',array('result' => & $Result));

$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.online.min.js').'"></script>';

?>