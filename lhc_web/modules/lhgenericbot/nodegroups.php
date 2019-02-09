<?php

header ( 'content-type: application/json; charset=utf-8' );

$bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

$groups = erLhcoreClassModelGenericBotGroup::getList(array(
    'filterin' => array('bot_id' => $bot->getBotIds()),
    'sort' => 'id ASC'
));

echo json_encode(array_values($groups));
exit;
?>