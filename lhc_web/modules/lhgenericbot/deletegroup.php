<?php


header ( 'content-type: application/json; charset=utf-8' );

$botGroup = erLhcoreClassModelGenericBotGroup::fetch($Params['user_parameters']['id']);
$botGroup->removeThis();

echo json_encode(array('errors' => 'false'));
exit;

?>