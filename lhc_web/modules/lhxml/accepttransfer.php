<?php
$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

try {
	$chatTransfer = erLhcoreClassTransfer::getSession()->load( 'erLhcoreClassModelTransfer', $Params['user_parameters']['transfer_id']);
	$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatTransfer->chat_id);
} catch (Exception $e) {
	exit;
}

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();
$chat->user_id = $currentUser->getUserID();

if  ($chatTransfer->dep_id > 0) {
	$chat->dep_id = $chatTransfer->dep_id;

	// User does not have access to chat in this department, that mean we do not have to do anything
	if (!erLhcoreClassChat::hasAccessToRead($chat)){
		exit;
	}
}

if ( !erLhcoreClassChat::hasAccessToRead($chat) )
{
	if ($currentUser->getUserID() == $chatTransfer->transfer_to_user_id) {
		$chat->dep_id = erLhcoreClassUserDep::getDefaultUserDepartment();
	} else {
		exit; // User does not have permission to assign chat to himself
	}
}

// All ok, we can make changes
erLhcoreClassChat::getSession()->update($chat);
erLhcoreClassTransfer::getSession()->delete($chatTransfer);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfer_accepted',array('chat' => & $chat));

echo json_encode(array('error' => 'false'));
exit;
?>