<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = erLhcoreClassModelGenericBotTrigger::fetch((int)$Params['user_parameters']['id']);
$trigger->removeThis();

echo json_encode(
    array(
    )
);

exit;
?>