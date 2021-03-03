<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = erLhcoreClassModelGenericBotTrigger::fetch((int)$Params['user_parameters']['id']);
$trigger->group_id = (int)$Params['user_parameters']['group_id'];
$trigger->saveThis();

echo json_encode(
    array(
    )
);

exit;
?>