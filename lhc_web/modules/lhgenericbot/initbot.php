<?php

header ( 'content-type: application/json; charset=utf-8' );

$bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

echo json_encode(array(
    'payloads' => array_values(erLhcoreClassModelGenericBotPayload::getList(array('filterin' => array('bot_id' => $bot->getBotIds())))),
    'rest_api_calls' => array_values(erLhcoreClassModelGenericBotRestAPI::getList(array('ignore_fields' => array('configuration')))),
));

exit;
?>