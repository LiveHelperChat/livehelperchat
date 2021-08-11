<?php
header ( 'content-type: application/json; charset=utf-8' );

$search = rawurldecode($_GET['q']);
$return = array();

if ($Params['user_parameters']['scope'] == 'users') {
    $db = ezcDbInstance::get();

    $filter = array('sort' => 'name ASC', 'limit' => 50);

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

    $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.searchprovider', array('search' => $search, 'scope' => $Params['user_parameters']['scope']));

    // There was no callbacks or file not found etc, we try to download from standard location
    if ($response !== false) {
        echo $response['data'];
        exit;
    }
}

echo json_encode(array('items' => $return, 'props' => array('list_id' => 'user_ids')));

exit;

?>