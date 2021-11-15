<?php
header ( 'content-type: application/json; charset=utf-8' );
$filter = array('offset' => 0, 'limit' => (int)$Params['user_parameters_unordered']['maxrows'],'sort' => 'last_visit DESC');

$department = isset($Params['user_parameters_unordered']['department']) && is_array($Params['user_parameters_unordered']['department']) && !empty($Params['user_parameters_unordered']['department']) ? $Params['user_parameters_unordered']['department'] : false;
if ($department !== false) {
    $filter['filterin']['`lh_chat_online_user`.`dep_id`'] = $department;
}

$timeout = (int)$Params['user_parameters_unordered']['timeout'];

if ($timeout > 0) {
	$filter['filtergt']['last_visit'] = (time()-$timeout);
}

/**
 * Append user departments filter
 * */
$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
if ($userDepartments !== true){
	$departmentParams['filterin']['id'] = $userDepartments;
	if (!$currentUser->hasAccessTo('lhchat','sees_all_online_visitors')) {
		$filter['filterin']['dep_id'] = $userDepartments;
	}
}

$departmentGroups = isset($Params['user_parameters_unordered']['department_dpgroups']) && is_array($Params['user_parameters_unordered']['department_dpgroups']) && !empty($Params['user_parameters_unordered']['department_dpgroups']) ? $Params['user_parameters_unordered']['department_dpgroups'] : false;
if ($departmentGroups !== false) {
    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department_dpgroups']);
    $db = ezcDbInstance::get();
    $stmt = $db->prepare('SELECT dep_id FROM lh_departament_group_member WHERE dep_group_id IN (' . implode(',',$Params['user_parameters_unordered']['department_dpgroups']) . ')');
    $stmt->execute();
    $depIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($depIds)) {
        if (isset($filter['filterin']['`lh_chat_online_user`.`dep_id`'])){
            $filter['filterin']['`lh_chat_online_user`.`dep_id`'] = array_merge($filter['filterin']['`lh_chat_online_user`.`dep_id`'],$depIds);
        } else {
            $filter['filterin']['`lh_chat_online_user`.`dep_id`'] = $depIds;
        }
    }
}

$items = erLhcoreClassModelChatOnlineUser::getList($filter);

$returnItems = array();

foreach ($items as $item) {
			if ($item->lat != 0 && $item->lon != 0) {
				$returnItems[] = array (
					"Id" => (string)$item->id,
					"Latitude" => $item->lat,
					"Longitude" => $item->lon,
					"icon" => $item->chat_id > 0 ? erLhcoreClassDesign::design('images/icons/home-chat.png') :  ($item->operator_message == '' ? erLhcoreClassDesign::design('images/icons/home-unsend.png') : erLhcoreClassDesign::design('images/icons/home-send.png'))
				);
			}
}

echo erLhcoreClassChat::safe_json_encode(array('result' => $returnItems));

exit();