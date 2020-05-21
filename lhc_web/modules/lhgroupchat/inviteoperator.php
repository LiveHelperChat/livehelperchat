<?php

header ( 'content-type: application/json; charset=utf-8' );

$item = erLhcoreClassModelGroupChat::fetch($Params['user_parameters']['id']);

erLhcoreClassGroupChat::inviteOperator($item->id, $Params['user_parameters']['op_id']);

$item->updateMembersCount();

echo json_encode(array());

exit;

?>