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
        $operatorAccepted = false;
        $chatDataChanged = false;
        
        if ($chat->user_id == 0) {
        	$currentUser = erLhcoreClassUser::instance();
        	$chat->user_id = $currentUser->getUserID();
        	$chatDataChanged = true;
        }
         
        // If status is pending change status to active
        if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
        	$chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
        
        	if ($chat->wait_time == 0) {
        		$chat->wait_time = time() - $chat->time;
        	}
        
        	$chat->user_id = $currentUser->getUserID();
        	$operatorAccepted = true;
        	$chatDataChanged = true;
        }
         
        if ($chat->support_informed == 0 || $chat->has_unread_messages == 1 ||  $chat->unread_messages_informed == 1) {
        	$chatDataChanged = true;
        }
         
        $chat->support_informed = 1;
        $chat->has_unread_messages = 0;
        $chat->unread_messages_informed = 0;
        erLhcoreClassChat::getSession()->update($chat);
                
        $ownerString = 'No data';
        $user = $chat->getChatOwner();
        if ($user !== false)
        {
            $ownerString = $user->name.' '.$user->surname;
        }
        
        $cannedmsg = erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id,$currentUser->getUserID());
        
    	echo json_encode(array('operator' => (string)$currentUser->getUserData(true)->name_support,'error' => false, 'canned_messages' => $cannedmsg, 'chat' => $chat, 'ownerstring' => $ownerString));
    	
	    flush();
	    session_write_close();
	    
	    if ( function_exists('fastcgi_finish_request') ) {
	        fastcgi_finish_request();
	    };
	    
	    if ($chatDataChanged == true) {
	    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed',array('chat' => & $chat,'user' => $currentUser));
	    }
    	    
	    if ($operatorAccepted == true) {
	    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.accept',array('chat' => & $chat,'user' => $currentUser));
	    	erLhcoreClassChat::updateActiveChats($chat->user_id);
	    	erLhcoreClassChatWorkflow::presendCannedMsg($chat);
	    	$options = $chat->department->inform_options_array;
	    	erLhcoreClassChatWorkflow::chatAcceptedWorkflow(array('department' => $chat->department,'options' => $options),$chat);
	    };	    
	    
} else {
    echo json_encode(array('error' => true,'error_string' => 'You do not have permission to read this chat!'));
}

exit;
?>