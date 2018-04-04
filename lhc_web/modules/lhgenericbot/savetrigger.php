<?php

header ( 'content-type: application/json; charset=utf-8' );

$requestData = json_decode(file_get_contents('php://input'),true);

erLhcoreClassGenericBotValidator::validateTriggerSave($requestData);

echo json_encode(array('error' => false));
exit;

?>