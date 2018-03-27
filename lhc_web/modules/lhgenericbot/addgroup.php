<?php

header ( 'content-type: application/json; charset=utf-8' );

echo json_encode(
    array(
        'name' => 'Group 3',
        'id' => 3,
    )
);

exit;
?>