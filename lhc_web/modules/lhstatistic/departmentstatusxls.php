<?php 

$departmentParams = array();
$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
if ($userDepartments !== true){
    $departmentParams['filterin']['id'] = $userDepartments;
    if (!$currentUser->hasAccessTo('lhchat','sees_all_online_visitors')) {
        $filter['filterin']['dep_id'] = $userDepartments;
    }
}

// Same sort as in widget
$departmentParams['sort'] = 'pending_chats_counter DESC';

erLhcoreClassChatExport::exportDepartmentStats(erLhcoreClassModelDepartament::getList($departmentParams));
exit;

?>