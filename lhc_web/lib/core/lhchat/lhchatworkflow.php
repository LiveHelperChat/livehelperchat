<?php

class erLhcoreClassChatWorkflow {

    /**
     * Message for timeout
     */
    public static function timeoutWorkflow(erLhcoreClassModelChat & $chat)
    {
    	$msg = new erLhcoreClassModelmsg();
    	$msg->msg = trim($chat->timeout_message);
    	$msg->chat_id = $chat->id;
    	$msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
    	$msg->user_id = 1;
    	$msg->time = time();
    	erLhcoreClassChat::getSession()->save($msg);

    	if ($chat->last_msg_id < $msg->id) {
    		$chat->last_msg_id = $msg->id;
    	}

    	$chat->timeout_message = '';
    	$chat->wait_timeout_send = 1;
    	$chat->updateThis();
    }

    /**
     * Transfer workflow between departments
     * */
    public static function transferWorkflow(erLhcoreClassModelChat & $chat)
    {
    	$chat->transfer_if_na = 0;
    	$chat->transfer_timeout_ts = time();

    	if ($chat->department !== false && ($departmentTransfer = $chat->department->department_transfer) !== false) {
    		$chat->dep_id = $departmentTransfer->id;

    		$msg = new erLhcoreClassModelmsg();
    		$msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was automatically transferred to').' "'.$departmentTransfer.'" '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','from').' "'.$chat->department.'"';
    		$msg->chat_id = $chat->id;
    		$msg->user_id = -1;

    		$chat->last_user_msg_time = $msg->time = time();

    		erLhcoreClassChat::getSession()->save($msg);

    		if ($chat->last_msg_id < $msg->id) {
    			$chat->last_msg_id = $msg->id;
    		}

    		if ($departmentTransfer->inform_unread == 1) {
    			$chat->reinform_timeout = $departmentTransfer->inform_unread_delay;
    			$chat->unread_messages_informed = 0;
    		}
    		
    		// Our new department also has a transfer rule
    		if ($departmentTransfer->department_transfer !== false) {
    			$chat->transfer_if_na = 1;
    			$chat->transfer_timeout_ac = $departmentTransfer->transfer_timeout;
    		}
    		
    		if ($chat->department->nc_cb_execute == 1) {
    			$chat->nc_cb_executed = 0;
    		}
    		
    		if ($chat->department->na_cb_execute == 1) {
    			$chat->na_cb_executed = 0;
    		}    		
    	}
       	
    	$chat->updateThis();
    }

    public static function mainUnansweredChatWorkflow() {    
    	$output = '';
	    if ( erLhcoreClassModelChatConfig::fetch('run_unaswered_chat_workflow')->current_value > 0) {
	    
	    	$output .= "Starting unaswered chats workflow\n";
	    
	    	$delay = time()-(erLhcoreClassModelChatConfig::fetch('run_unaswered_chat_workflow')->current_valu*60);
	    
	    	foreach (erLhcoreClassChat::getList(array('limit' => 500, 'filterlt' => array('time' => $delay), 'filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT, 'na_cb_executed' => 0))) as $chat) {
	    		erLhcoreClassChatWorkflow::unansweredChatWorkflow($chat);
	    		$output .= "executing unanswered callback for chat - ".$chat->id."\n";
	    	}
	    
	    	$output .= "Ended unaswered chats workflow\n";
	    }
	    
	    return $output;
    }
    /*
     * Chat was unanswered for n minits, execute callback.
     * */
    public static function unansweredChatWorkflow(erLhcoreClassModelChat & $chat){

    	$chat->na_cb_executed = 1;
    	$chat->updateThis();

    	// Execute callback if it exists
    	$extensions = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'extensions' );
    	$instance = erLhcoreClassSystem::instance();

    	foreach ($extensions as $ext) {
    		$callbackFile = $instance->SiteDir . '/extension/' . $ext . '/callbacks/unanswered_chat.php';
    		if (file_exists($callbackFile)) {
    			include $callbackFile;
    		}
    	}
    }

    public static function unreadInformWorkflow($options = array(), & $chat) {
    	 
    	$chat->unread_messages_informed = 1;
    	$chat->updateThis();

    	if (in_array('mail', $options['options'])) {
    		erLhcoreClassChatMail::sendMailUnacceptedChat($chat,7);
    	}

    	if (in_array('xmp', $options['options'])) {
    		erLhcoreClassXMP::sendXMPMessage($chat);
    	}
    	 
    	// Execute callback if it exists
    	$extensions = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'extensions' );
    	$instance = erLhcoreClassSystem::instance();
    	 
    	foreach ($extensions as $ext) {
    		$callbackFile = $instance->SiteDir . '/extension/' . $ext . '/callbacks/unread_message_chat.php';
    		if (file_exists($callbackFile)) {
    			include $callbackFile;
    		}
    	}
    	 
    }
    
    public static function chatAcceptedWorkflow($options = array(), & $chat) {    	
    	if (in_array('mail_accepted', $options['options'])) {
    		erLhcoreClassChatMail::sendMailUnacceptedChat($chat,9);
    	}
    	
    	if (in_array('xmp_accepted', $options['options'])) {    	
    		erLhcoreClassXMP::sendXMPMessage($chat,array('template' => 'xmp_accepted_message'));
    	}
    }

    
    public static function newChatInformWorkflow($options = array(), & $chat) {
    	
    	$chat->nc_cb_executed = 1;
    	$chat->updateThis();
    	
    	if (in_array('mail', $options['options'])) {    	
    		erLhcoreClassChatMail::sendMailUnacceptedChat($chat);
    	}

    	if (in_array('xmp', $options['options'])) {
    		erLhcoreClassXMP::sendXMPMessage($chat);
    	}
    	
    	// Execute callback if it exists
    	$extensions = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'extensions' );
    	$instance = erLhcoreClassSystem::instance();
    	
    	foreach ($extensions as $ext) {
    		$callbackFile = $instance->SiteDir . '/extension/' . $ext . '/callbacks/new_chat.php';
    		if (file_exists($callbackFile)) {
    			include $callbackFile;
    		}
    	}    	
    	
    }
}

?>