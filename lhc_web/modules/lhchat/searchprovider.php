<?php
header ( 'content-type: application/json; charset=utf-8' );

$search = isset($_GET['q']) ? rawurldecode($_GET['q']) : '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$return = array();
$listId = 'user_ids';

if ($Params['user_parameters']['scope'] == 'depbydepgroup') {
    if (isset($_GET['d']) && is_numeric($_GET['d'])) {
        foreach (erLhcoreClassModelDepartamentGroupMember::getList(['filter' => ['dep_group_id' => (int)$_GET['d']]]) as $depMember) {
            $return[] = $depMember->dep_id;
        }
    }
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
} else if ($Params['user_parameters']['scope'] == 'mailbox') {

    $db = ezcDbInstance::get();

    $filter = array('sort' => 'name ASC', 'limit' => 50);

    if (!empty($search)) {
        $filter['customfilter'] = array('(`name` LIKE ('. $db->quote('%'.$search.'%')  .') OR `mail` LIKE ('. $db->quote('%'.$search.'%')  .'))');
    }

    $filter['filter']['active'] = 1;

    $items = erLhcoreClassModelMailconvMailbox::getList($filter);

    foreach ($items as $item) {
        $return[] = array('id' => $item->id, 'name' => $item->name, 'mail' => $item->mail);
    }

} else if ($Params['user_parameters']['scope'] != '') {

    $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.searchprovider', array('offset' => $offset, 'search' => $search, 'scope' => $Params['user_parameters']['scope']));

    // There was no callbacks or file not found etc, we try to download from standard location
    if ($response !== false) {
        echo $response['data'];
        exit;
    }
}

echo json_encode(array('items' => $return, 'props' => array('list_id' => $listId)));

exit;

?>