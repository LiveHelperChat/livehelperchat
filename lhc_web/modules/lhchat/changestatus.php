<?php 

$tpl = erLhcoreClassTemplate::getInstance('lhchat/changestatus.tpl.php');
$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$tpl->set('chat',$chat);

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {
	$currentUser = erLhcoreClassUser::instance();
	
	
	if ( isset($_POST['ChatStatus']) && is_numeric($_POST['ChatStatus']) ) {	

		$userData = $currentUser->getUserData();
		$changeStatus = (int)$_POST['ChatStatus'];
		
		 if (in_array($changeStatus, array(
		 		erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
		 		erLhcoreClassModelChat::STATUS_PENDING_CHAT,
		 		erLhcoreClassModelChat::STATUS_CLOSED_CHAT,
		 		erLhcoreClassModelChat::STATUS_CHATBOX_CHAT,
		 		erLhcoreClassModelChat::STATUS_OPERATORS_CHAT
		 ))) {

		 	 if ($changeStatus == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {		 	 	
		 		if ($chat->status != erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
		 			$chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
		 			$chat->wait_time = time() - $chat->time;
		 		}

		 		if ($chat->user_id == 0)
		 		{
		 			$chat->user_id = $currentUser->getUserID();
		 		}
		 		
		 		$chat->updateThis();
		 		
		 	 } elseif ($changeStatus == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {	
		 	 	
		 	 	$chat->user_id = 0;
		 	 	$chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;		 	 	
		 	 	$chat->support_informed = 0;
		 	 	$chat->has_unread_messages = 1;	
		 	 	
		 	 	$chat->updateThis();
		 	 	
		 	 } elseif ($changeStatus == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote')) {
		 	 	
		 	 	if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT){		 	 	
		 	 		$chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
		 	 		$chat->chat_duration = time() - ($chat->time + $chat->wait_time);
		 	 			 	 				 	 	
		 	 		$msg = new erLhcoreClassModelmsg();
		 	 		$msg->msg = (string)$userData.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin','has closed the chat!');
		 	 		$msg->chat_id = $chat->id;
		 	 		$msg->user_id = -1;
		 	 	
		 	 		$chat->last_user_msg_time = $msg->time = time();
		 	 	
		 	 		erLhcoreClassChat::getSession()->save($msg);
		 	 	
		 	 		$chat->updateThis();
		 	 	
		 	 		CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', $chat->id);
		 	 	
		 	 		// Execute callback for close chat
		 	 		erLhcoreClassChat::closeChatCallback($chat,$userData);
		 	 	}	
		 	 		 	 	
		 	 } elseif ($changeStatus == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) {	 
		 	 	$chat->status = erLhcoreClassModelChat::STATUS_CHATBOX_CHAT;
		 	 	erLhcoreClassChat::getSession()->update($chat);
		 	 } elseif ($changeStatus == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {	 
		 	 	$chat->status = erLhcoreClassModelChat::STATUS_OPERATORS_CHAT;
		 	 	erLhcoreClassChat::getSession()->update($chat);
		 	 }		 	 	
		 	
			 echo json_encode(array('error' => 'false'));
			 exit;
		 } else {
		 	echo json_encode(array('error' => 'true','result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Invalid chat status')));
		 	exit;
		 }
	}
}

print $tpl->fetch();
exit;


?>