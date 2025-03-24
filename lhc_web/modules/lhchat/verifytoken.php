<?php
header ( 'content-type: application/json; charset=utf-8' );
$currentUser = erLhcoreClassUser::instance();

echo json_encode(['verified' => (isset($_SERVER['HTTP_X_CSRFTOKEN']) && $currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) && $currentUser->isLogged()]);
exit();

?>