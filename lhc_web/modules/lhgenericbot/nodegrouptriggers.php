<?php

header ( 'content-type: application/json; charset=utf-8' );

echo json_encode(array(
    array(
        'name' => 'Trigger 1-' . $Params['user_parameters']['id'],
        'id' => $Params['user_parameters']['id'] . '-1',
    ),
    array(
        'name' => 'Trigger 2-' . $Params['user_parameters']['id'],
        'id' => $Params['user_parameters']['id'] . '-2',
    )
));

exit;
?>