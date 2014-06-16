<?php


$tpl = erLhcoreClassTemplate::getInstance('lhchat/adminchat.tpl.php');

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$tpl->set('chat',$chat);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
	
	$userData = $currentUser->getUserData();
	
	if ($Params['user_parameters_unordered']['remember'] == 'true') {
		CSCacheAPC::getMem()->appendToArray('lhc_open_chats',$chat->id);
	}
	
	if ($userData->invisible_mode == 0) {	
		
		$operatorAccepted = false;
		    
	    if ($chat->user_id == 0) {
	        $currentUser = erLhcoreClassUser::instance();
	        $chat->user_id = $currentUser->getUserID();	        
	    }
	    
	    // If status is pending change status to active
	    if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
	    	$chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
	    	$chat->wait_time = time() - $chat->time;
	    	$chat->user_id = $currentUser->getUserID();
	    	$operatorAccepted = true;
	    }
	    
	    $chat->support_informed = 1;
	    $chat->has_unread_messages = 0;
	    $chat->unread_messages_informed = 0;
	    erLhcoreClassChat::getSession()->update($chat);
		
	    echo $tpl->fetch();	  
	    flush();	    	    
	    session_write_close();	  
		
	    if ( function_exists('fastcgi_finish_request') ) {
	    	fastcgi_finish_request();
	    };
	    
	    if ($operatorAccepted == true) {	 	    	
	    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.accept',array('chat' => & $chat,'user' => $currentUser));	    	
	    	erLhcoreClassChat::updateActiveChats($chat->user_id);	    	
	    	erLhcoreClassChatWorkflow::presendCannedMsg($chat);
	    	$options = $chat->department->inform_options_array;
	    	erLhcoreClassChatWorkflow::chatAcceptedWorkflow(array('department' => $chat->department,'options' => $options),$chat);
	    };
	    exit;	    
	}
    
	echo $tpl->fetch();
	exit;    

} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $tpl->set('show_close_button',true);
    echo $tpl->fetch();
    exit;
}



?>