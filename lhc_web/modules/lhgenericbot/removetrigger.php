<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

$trigger = erLhcoreClassModelGenericBotTrigger::fetch((int)$Params['user_parameters']['id']);
$trigger->removeThis();

echo json_encode(
    array(
    )
);

exit;
?>