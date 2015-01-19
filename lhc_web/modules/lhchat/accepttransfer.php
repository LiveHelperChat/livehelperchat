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
		$chat->user_typing_txt = (string)$chat->user.' '.htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/accepttrasnfer','has joined the chat!'),ENT_QUOTES);
		$chat->user_typing  = time();
		
		$msg = new erLhcoreClassModelmsg();
		$msg->msg = (string)$chat->user.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/accepttrasnfer','has accepted a chat!');
		$msg->chat_id = $chat->id;
		$msg->user_id = -1;
	}
}

if ($chatTransfer->transfer_to_user_id == $currentUser->getUserID()){
	$chat->user_id = $currentUser->getUserID();
	$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
	$chat->user_typing_txt = (string)$chat->user.' '.htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/accepttrasnfer','has joined the chat!'),ENT_QUOTES);
	$chat->user_typing  = time();
	
	$msg = new erLhcoreClassModelmsg();
	$msg->msg = (string)$chat->user.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/accepttrasnfer','has accepted a chat!');
	$msg->chat_id = $chat->id;
	$msg->user_id = -1;
	
	// Change department if user cannot read current department, so chat appears in right menu
	$filter = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
	if ($filter !== true && !in_array($chat->dep_id, $filter)) {
		$dep_id = erLhcoreClassUserDep::getDefaultUserDepartment();
		if ($dep_id > 0) {
			$chat->dep_id = $dep_id;
			$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
		}
	}
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

// Store system message
if (isset($msg) && $msg instanceof erLhcoreClassModelmsg) {	
	$chat->last_user_msg_time = $msg->time = time();
	erLhcoreClassChat::getSession()->save($msg);
}

// All ok, we can make changes
erLhcoreClassChat::getSession()->update($chat);
erLhcoreClassTransfer::getSession()->delete($chatTransfer);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfer_accepted',array('chat' => & $chat));

if ($Params['user_parameters_unordered']['postaction'] == 'singlewindow') {
	erLhcoreClassModule::redirect('chat/single/' . $chat->id);
	exit;
}

echo json_encode(array('error' => 'false', 'chat_id' => $chat->id));
exit;
?>