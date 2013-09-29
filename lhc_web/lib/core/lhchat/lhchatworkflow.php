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

    		// Our new department also has a transfer rule
    		if ($departmentTransfer->department_transfer !== false) {
    			$chat->transfer_if_na = 1;
    			$chat->transfer_timeout_ac = $departmentTransfer->transfer_timeout;
    		}
    	}

    	$chat->updateThis();
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
}

?>