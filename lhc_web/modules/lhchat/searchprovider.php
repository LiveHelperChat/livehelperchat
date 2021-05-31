<?php
header ( 'content-type: application/json; charset=utf-8' );

$search = rawurldecode($_GET['q']);

if ($Params['user_parameters']['scope'] == 'users') {
    $db = ezcDbInstance::get();
    $items = erLhcoreClassModelUser::getList(array('limit' => 50, 'customfilter' => array('`name` LIKE ('. $db->quote('%'.$search.'%')  .') OR `surname` LIKE ('. $db->quote('%'.$search.'%').')')));
    $return = array();
    foreach ($items as $item) {
        $return[] = array('id' => $item->id, 'name' => $item->name_official);
    }
}

echo json_encode(array('items' => $return, 'props' => array('list_id' => 'user_ids')));

exit;

?>