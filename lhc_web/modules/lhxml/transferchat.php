<?php

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$onlineUsers = erLhcoreClassChat::getOnlineUsers(array($currentUser->getUserID()));

echo json_encode(array('result' => $onlineUsers));

exit;

?>