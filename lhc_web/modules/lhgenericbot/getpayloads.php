<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = erLhcoreClassModelGenericBotTrigger::fetch((int)$Params['user_parameters']['id']);

echo json_encode(
    array(
        'payloads' => array_values(erLhcoreClassModelGenericBotPayload::getList(array('filter' => array('bot_id' => (int)$trigger->bot_id))))
    )
);

exit;
?>