<?php
header ( 'content-type: application/json; charset=utf-8' );

$search = isset($_GET['q']) ? rawurldecode($_GET['q']) : '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$return = array();
$returnNames = array();
$listId = 'user_ids';

if ($Params['user_parameters']['scope'] == 'depbydepgroup') {
    if (isset($_GET['d']) && is_numeric($_GET['d'])) {
        foreach (erLhcoreClassModelDepartamentGroupMember::getList(['filter' => ['dep_group_id' => (int)$_GET['d']]]) as $depMember) {
            $return[] = $depMember->dep_id;
        }
    }
} else if ($Params['user_parameters']['scope'] == 'canned') {

    $db = ezcDbInstance::get();

    $filter = array('filter' => ['department_id' => 0], 'sort' => 'title ASC', 'limit' => 10, 'offset' => $offset);

    if (!empty($search)) {
        $filter['customfilter'] = array('(`title` LIKE ('. $db->quote('%'.$search.'%')  .') OR `explain` LIKE ('. $db->quote('%'.$search.'%')  .') OR `fallback_msg` LIKE ('. $db->quote('%'.$search.'%')  .') OR `msg` LIKE ('. $db->quote('%'.$search.'%').'))');
    }

    $items = erLhcoreClassModelCannedMsg::getList($filter);
    foreach ($items as $item) {
        $return[] = array('id' => $item->id, 'name' => $item->title);
    }

} else if ($Params['user_parameters']['scope'] == 'depswidget') {

    $db = ezcDbInstance::get();

    $filter = array('sort' => 'sort_priority ASC, name ASC', 'limit' => 20, 'offset' => $offset);

    $dwFilters = json_decode(erLhcoreClassModelUserSetting::getSetting('dw_filters', '{}', false, false, true),true);
    $filterDep = [];
    foreach (['actived','departmentd','unreadd','pendingd','operatord','closedd','mcd','botd','subjectd','department_online'] as $list) {
        if (isset($dwFilters[$list]) && !empty($dwFilters[$list])) {
            $filterDep = array_unique(array_merge($filterDep,explode("/",$dwFilters[$list])));
        }
    }

    $orConditions = [];

    if (!empty($search)) {
        $orConditions[] = '`name` LIKE ' . $db->quote('%'.$search.'%');
    }

    // Always return already selected departments
    if (!empty($filterDep)) {
        erLhcoreClassChat::validateFilterIn($filterDep);
        $orConditions[] = '`id` IN ('.implode(',',$filterDep).')';
        $filter['limit'] = $filter['limit'] + count($filterDep);
    }

    if (!empty($orConditions)){
        $filter['customfilter'][] = '('.implode(' OR ',$orConditions).')';
    }

    $items = erLhcoreClassModelDepartament::getList(array_merge_recursive(erLhcoreClassUserDep::conditionalDepartmentFilter(),$filter));

    $loggedDepartments = erLhcoreClassChat::getLoggedDepartmentsIds(array_keys($items), false);
    $loggedDepartmentsExplicit = erLhcoreClassChat::getLoggedDepartmentsIds(array_keys($items), true);

    foreach ($items as $department) {
        $returnNames[$department->id] = $department->name;
        $return[] = array(
            'id' => $department->id,
            'name' => $department->name,
            'hidden' => $department->hidden,
            'disabled' => $department->disabled == 1,
            'ogen' => in_array($department->id, $loggedDepartments),            // Online general
            'oexp' => in_array($department->id, $loggedDepartmentsExplicit),    // Online explicit
            'slc' => in_array($department->id, $filterDep)
        );
    }

    usort($return, function($a, $b) use ($filterDep) {
        return  in_array($a['id'],$filterDep) ? 1 : (!strcmp($a['name'],$b['name']) ? 1 : 0);
    });

    $listId = 'department_ids';

} else if ($Params['user_parameters']['scope'] == 'deps') {

    $db = ezcDbInstance::get();

    $filter = array('sort' => 'name ASC', 'limit' => 50, 'offset' => $offset);

    if (!empty($search)) {
        $filter['filterlike']['name'] = $search;
    }

    $items = erLhcoreClassModelDepartament::getList(array_merge_recursive(erLhcoreClassUserDep::conditionalDepartmentFilter(),$filter));

    foreach ($items as $item) {
        $return[] = array('id' => $item->id, 'name' => $item->name);
    }

    $listId = 'department_ids';

} else if ($Params['user_parameters']['scope'] == 'users') {
    $db = ezcDbInstance::get();

    $filter = array('sort' => 'name ASC', 'limit' => 50, 'offset' => $offset);

    if (!empty($search)) {
        $filter['customfilter'] = array('(`chat_nickname` LIKE ('. $db->quote('%'.$search.'%')  .') OR `name` LIKE ('. $db->quote('%'.$search.'%')  .') OR `surname` LIKE ('. $db->quote('%'.$search.'%').'))');
    }

    if (isset($_GET['exclude_disabled']) && $_GET['exclude_disabled'] == 1) {
        $filter['filter']['disabled'] = 0;
    }

    $items = erLhcoreClassModelUser::getList($filter);
    foreach ($items as $item) {
        $return[] = array('id' => $item->id, 'name' => $item->name_official, 'nick' => $item->chat_nickname);
    }

} else if ($Params['user_parameters']['scope'] == 'users_ids') {
    $db = ezcDbInstance::get();
    $userIDS = explode(',',str_replace(',,',',',$search));
    erLhcoreClassChat::validateFilterIn($userIDS);
    if (!empty($userIDS)){
        $items = erLhcoreClassModelUser::getList(array('sort' => 'name ASC', 'limit' => false, 'filterin' => array('id' => $userIDS)));
        foreach ($items as $item) {
            $return[] = array('id' => $item->id, 'name' => $item->name_official);
        }
    }
} else if ($Params['user_parameters']['scope'] != '') {

    $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.searchprovider', array('offset' => $offset, 'search' => $search, 'scope' => $Params['user_parameters']['scope']));

    // There was no callbacks or file not found etc, we try to download from standard location
    if ($response !== false) {
        echo $response['data'];
        exit;
    }
}

echo json_encode(array('items' => $return, 'items_names' => $returnNames, 'props' => array('list_id' => $listId)));

exit;

?>