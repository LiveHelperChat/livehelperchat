<?php

header ( 'content-type: application/json; charset=utf-8' );

$triggerEvent = erLhcoreClassModelGenericBotTriggerEvent::fetch($Params['user_parameters']['id']);
$triggerEvent->removeThis();

echo json_encode(array('errors' => 'false'));
exit;

?>