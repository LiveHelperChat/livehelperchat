<?php
header('Content-type: application/json');

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$userData = $currentUser->getUserData(true);
echo json_encode(array('online' => $userData->hide_online == 1));

exit;
?>