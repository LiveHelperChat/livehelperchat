<?php
header ( 'content-type: application/json; charset=utf-8' );

$search = rawurldecode($_GET['q']);
$return = array();

if ($Params['user_parameters']['scope'] == 'users') {
    $db = ezcDbInstance::get();
    //$userListParams['sort'] = 'name ASC';
    $items = erLhcoreClassModelUser::getList(array('sort' => 'name ASC', 'limit' => 50, 'customfilter' => array('`name` LIKE ('. $db->quote('%'.$search.'%')  .') OR `surname` LIKE ('. $db->quote('%'.$search.'%').')')));
    foreach ($items as $item) {
        $return[] = array('id' => $item->id, 'name' => $item->name_official);
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