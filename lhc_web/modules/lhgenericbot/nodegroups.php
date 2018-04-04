<?php

header ( 'content-type: application/json; charset=utf-8' );

$groups = erLhcoreClassModelGenericBotGroup::getList(array(
    'filter' => array('bot_id' => (int)$Params['user_parameters']['id'])
));

echo json_encode(array_values($groups));
exit;
?>