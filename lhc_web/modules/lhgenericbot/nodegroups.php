<?php

header ( 'content-type: application/json; charset=utf-8' );

echo json_encode(array(
    array(
        'name' => 'Group 1',
        'id' => 1,
    ),
    array(
        'name' => 'Group 2',
        'id' => 2,
    )
));

exit;
?>