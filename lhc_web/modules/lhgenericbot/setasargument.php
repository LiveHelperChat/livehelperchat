<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

$trigger = erLhcoreClassModelGenericBotTrigger::fetch((int)$Params['user_parameters']['id']);
$trigger->as_argument = (int)$Params['user_parameters']['default'] == 1 ? 1 : 0;
$trigger->saveThis();

echo json_encode(
    array(
    )
);

exit;
?>