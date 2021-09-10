<?php

header ( 'content-type: application/json; charset=utf-8' );

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
    exit;
}

$search = erLhAbstractModelSavedSearch::fetch($Params['user_parameters']['id']);

if ($search->user_id == $currentUser->getUserID()) {
    $search->removeThis();
}

exit;

?>