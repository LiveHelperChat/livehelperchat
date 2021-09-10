<?php

header ( 'content-type: application/json; charset=utf-8' );

$response = [
    'views' => array_values(erLhAbstractModelSavedSearch::getList(['limit' => false, 'filter' => ['user_id' =>  erLhcoreClassUser::instance()->getUserID()]]))
];

echo json_encode($response);

exit;

?>