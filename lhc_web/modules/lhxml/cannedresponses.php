<?php

header('Content-type: application/json');

$currentUser = erLhcoreClassUser::instance();

if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $cannedmsg = erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id,$currentUser->getUserID());
    echo json_encode(array('error' => false, 'canned_messages' => array_values($cannedmsg)));

    flush();
    session_write_close();

    if ( function_exists('fastcgi_finish_request') ) {
        fastcgi_finish_request();
    };

} else {
    echo json_encode(array('error' => true, 'error_string' => 'You do not have permission to read this chat!'));
}

exit;
?>