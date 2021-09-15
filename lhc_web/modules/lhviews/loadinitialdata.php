<?php

header ( 'content-type: application/json; charset=utf-8' );

$views = erLhAbstractModelSavedSearch::getList(['limit' => false, 'filter' => ['user_id' =>  erLhcoreClassUser::instance()->getUserID()]]);

erLhcoreClassChat::prefillGetAttributes($views, array('id', 'passive', 'name', 'scope', 'total_records', 'updated_ago'), array(), array('remove_all' => true));

$response = [
    'views' => array_values($views)
];



echo json_encode($response);

exit;

?>