<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = erLhcoreClassModelGenericBotTrigger::fetch($Params['user_parameters']['id']);

echo json_encode(
    array(array('id' => 448, 'name' => '448 name'),array('id' => 452, 'name' => '452 name'))
);

exit;
?>