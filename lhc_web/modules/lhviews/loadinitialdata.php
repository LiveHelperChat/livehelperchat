<?php

header ( 'content-type: application/json; charset=utf-8' );

$views = erLhAbstractModelSavedSearch::getList(['limit' => false, 'filter' => ['status' => erLhAbstractModelSavedSearch::ACTIVE, 'user_id' =>  erLhcoreClassUser::instance()->getUserID()]]);

erLhcoreClassChat::prefillGetAttributes($views, array('id', 'passive', 'name', 'scope', 'total_records', 'updated_ago'), array(), array('remove_all' => true));

$response = [
    'views' => array_values($views),
    'invites' => (int)erLhAbstractModelSavedSearch::getCount(['limit' => false, 'filter' => ['status' => erLhAbstractModelSavedSearch::INVITE, 'user_id' =>  erLhcoreClassUser::instance()->getUserID()]])
];



echo json_encode($response);

exit;

?>