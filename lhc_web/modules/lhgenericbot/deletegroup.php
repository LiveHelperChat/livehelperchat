<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

$botGroup = erLhcoreClassModelGenericBotGroup::fetch($Params['user_parameters']['id']);
$botGroup->removeThis();

echo json_encode(array('errors' => 'false'));
exit;

?>