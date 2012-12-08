<?php

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

//$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
//$user_id = $currentUser->getUserID();

$onlineUsers = erLhcoreClassChat::getOnlineUsers(array($currentUser->getUserID()));

echo json_encode(array('result' => $onlineUsers));

exit;

?>