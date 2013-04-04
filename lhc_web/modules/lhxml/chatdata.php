<?php
$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
		// If status is pending change status to active
		if ($chat->status == 0) {
		    	$chat->status = 1;
		}

        if ($chat->user_id == 0)
        {
            $currentUser = erLhcoreClassUser::instance();
            $chat->user_id = $currentUser->getUserID();
        }

        erLhcoreClassChat::getSession()->update($chat);

        $ownerString = 'No data';
        $user = $chat->getChatOwner();
        if ($user !== false)
        {
            $ownerString = $user->name.' '.$user->surname;
        }

        $cannedmsg = erLhcoreClassModelCannedMsg::getList();

    echo json_encode(array('error' => false, 'canned_messages' => $cannedmsg, 'chat' => $chat, 'ownerstring' => $ownerString));

} else {
    echo json_encode(array('error' => true,'error_string' => 'You do not have permission to read this chat!'));
}

exit;
?>