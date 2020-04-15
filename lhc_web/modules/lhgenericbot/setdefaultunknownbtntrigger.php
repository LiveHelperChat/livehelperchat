<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = erLhcoreClassModelGenericBotTrigger::fetch((int)$Params['user_parameters']['id']);
$trigger->default_unknown_btn = (int)$Params['user_parameters']['default'] == 1 ? 1 : 0;
$trigger->saveThis();

echo json_encode(
    array(
    )
);

exit;
?>