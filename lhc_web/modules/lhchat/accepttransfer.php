<?php

try {
	$chatTransfer = erLhcoreClassTransfer::getSession()->load( 'erLhcoreClassModelTransfer', $Params['user_parameters']['transfer_id']);
	$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatTransfer->chat_id);
} catch (Exception $e) {
	exit;
}

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();

if  ($chatTransfer->dep_id > 0) {
	$chat->dep_id = $chatTransfer->dep_id;

	// User does not have access to chat in this department, that mean we do not have to do anything
	if (!erLhcoreClassChat::hasAccessToRead($chat)){
		exit;
	} else {
		$chat->user_id = $currentUser->getUserID();
		$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
	}
}

if ($chatTransfer->transfer_to_user_id == $currentUser->getUserID()){
	$chat->user_id = $currentUser->getUserID();
	$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
}

if ( !erLhcoreClassChat::hasAccessToRead($chat) )
{
	if ($currentUser->getUserID() == $chatTransfer->transfer_to_user_id) {
		$dep_id = erLhcoreClassUserDep::getDefaultUserDepartment();
		if ($dep_id > 0) {
			$chat->dep_id = $dep_id;
			$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
		}
	} else {
		exit; // User does not have permission to assign chat to himself
	}
}

// All ok, we can make changes
erLhcoreClassChat::getSession()->update($chat);
erLhcoreClassTransfer::getSession()->delete($chatTransfer);

if ($Params['user_parameters_unordered']['postaction'] == 'singlewindow') {
	erLhcoreClassModule::redirect('chat/single/' . $chat->id);
	exit;
}

echo json_encode(array('error' => 'false', 'chat_id' => $chat->id));
exit;
?>