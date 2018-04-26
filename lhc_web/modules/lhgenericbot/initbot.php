<?php

header ( 'content-type: application/json; charset=utf-8' );

echo json_encode(array('payloads' => array_values(erLhcoreClassModelGenericBotPayload::getList(array('filter' => array('bot_id' => (int)$Params['user_parameters']['id']))))));
exit;
?>