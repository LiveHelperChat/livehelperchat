<?php

$chat = erLhcoreClassChat::getSession ()->load ( 'erLhcoreClassModelChat', $Params ['user_parameters'] ['chat_id'] );

if (erLhcoreClassChat::hasAccessToRead ( $chat )) {
	$currentUser = erLhcoreClassUser::instance ();
	
	if (! isset ( $_SERVER ['HTTP_X_CSRFTOKEN'] ) || ! $currentUser->validateCSFRToken ( $_SERVER ['HTTP_X_CSRFTOKEN'] )) {
		echo json_encode ( array (
				'error' => 'true',
				'result' => 'Invalid CSRF Token' 
		) );
		exit ();
	}
	
	$userData = $currentUser->getUserData();
	
	$msg = new erLhcoreClassModelmsg ();
	$msg->msg = ( string ) $userData . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin','has redirected user to contact form!');
	$msg->chat_id = $chat->id;
	$msg->user_id = - 1;
	
	$chat->last_user_msg_time = $msg->time = time ();
	erLhcoreClassChat::getSession()->save($msg);
	
	// Set last message ID
	if ($chat->last_msg_id < $msg->id) {		
		if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
			$chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
		}
		$chat->last_msg_id = $msg->id;		
	}
	
	if ($chat->user_id == 0)
	{
		$chat->user_id = $currentUser->getUserID();
	}
	
	$chat->support_informed = 1;
	$chat->has_unread_messages = 0;
	
	$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM;
	$chat->updateThis ();
}

echo json_encode ( array (
		'error' => 'false' 
) );

exit ();

?>