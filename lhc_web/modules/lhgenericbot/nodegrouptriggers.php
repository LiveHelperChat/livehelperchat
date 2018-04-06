<?php

header ( 'content-type: application/json; charset=utf-8' );

$triggers = erLhcoreClassModelGenericBotTrigger::getList(array('sort' => 'id ASC', 'filter' => array('group_id' => (int)$Params['user_parameters']['id'])));

echo json_encode(array_values($triggers));

exit;
?>