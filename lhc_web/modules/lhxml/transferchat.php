<?php

header('Content-type: application/json');

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$onlineUsers = erLhcoreClassChat::getOnlineUsers(array($currentUser->getUserID()));

// Temporary un-till we release a new version of mobile app
foreach ($onlineUsers as & $onlineUser) {
    $onlineUser['id'] = (string)$onlineUser['id'];
}

echo json_encode(array('result' => $onlineUsers));

exit;

?>