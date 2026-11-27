<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

$requestData = json_decode(file_get_contents('php://input'),true);

erLhcoreClassGenericBotValidator::validateTrigger($requestData);

echo json_encode(array('error' => false));
exit;

?>