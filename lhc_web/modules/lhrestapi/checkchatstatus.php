<?php

try {
    
erLhcoreClassRestAPIHandler::validateRequest();

$ott = '';
$ru = '';
$user = false;

if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
    try {
        $theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
    } catch (Exception $e) {

    }
} else {
    $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
    if ($defaultTheme > 0) {
        try {
            $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
        } catch (Exception $e) {
             
        }
    }
}

$responseArray = array();

try {
	
	if (isset($_GET['hash'])) {
		$hash = $_GET['hash'];
	} else {
		throw new Exception('Please provide hash');
	}
	
	if (isset($_GET['chat_id'])) {
		$chat_id = $_GET['chat_id'];
	} else {
		throw new Exception('Please provide chat_id');
	}


    $chat = erLhcoreClassModelChat::fetch($chat_id);

    if ($chat->hash === $hash) {

    	// Main unasnwered chats callback
    	if ( $chat->na_cb_executed == 0 && $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && erLhcoreClassModelChatConfig::fetch('run_unaswered_chat_workflow')->current_value > 0) {    		
    		$delay = time()-(erLhcoreClassModelChatConfig::fetch('run_unaswered_chat_workflow')->current_value*60);    		
    		if ($chat->time < $delay) {    		
    			erLhcoreClassChatWorkflow::unansweredChatWorkflow($chat);
    		}
    	}
    	
    	if ( $chat->nc_cb_executed == 0 && $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {      		  		
    		$department = $chat->department;    		   		
    		if ($department !== false) {    			
    			$options = $department->inform_options_array;   		 				
    			$delay = time()-$department->inform_delay;    			
    			if ($chat->time < $delay) {
    				erLhcoreClassChatWorkflow::newChatInformWorkflow(array('department' => $department,'options' => $options),$chat);
    			}
    		} else {
    			$chat->nc_cb_executed = 1;
    			$chat->updateThis();
    		}
    	}
    	
    	$contactRedirected = false;
    	
    	if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
    		$department = $chat->department;
    		if ($department !== false) {
    			$delay = time()-$department->delay_lm;
    			if ($department->delay_lm > 0 && $chat->time < $delay) {
    				$baseURL = (isset($Params['user_parameters_unordered']['mode']) && $Params['user_parameters_unordered']['mode'] == 'widget') ? erLhcoreClassDesign::baseurl('chat/chatwidget') : erLhcoreClassDesign::baseurl('chat/startchat');
    				$ru = $baseURL.'/(department)/'.$department->id.'/(offline)/true/(leaveamessage)/true/(chatprefill)/'.$chat->id.'_'.$chat->hash;
    				
    				$msg = new erLhcoreClassModelmsg();
    				$msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Visitor has been redirected to contact form');
    				$msg->chat_id = $chat->id;
    				$msg->user_id = -1;
    				$msg->time = time();    				
    				erLhcoreClassChat::getSession()->save($msg);
    				
    				// We do not store last msg time for chat here, because in any case none of opeators has opened it
    				$contactRedirected = true;
    				
    				if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM) {
        				$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM;
        				$chat->updateThis();
    				}
    				
    			} else {
    				erLhcoreClassChatWorkflow::autoAssign($chat,$department);
    			}
    		}   		
    	}    	
    	
	    if ( erLhcoreClassChat::isOnline($chat->dep_id,false,array('online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'])) ) {
	        $is_online = true;
	    } else {
	        $is_online = false;
	    }

	    if ( $chat->chat_initiator == erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE ) {
	        $is_proactive_based = true;
	    } else {
	        $is_proactive_based = false;
	    }

	    if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
	       $is_activated = true;
	       $ott = ($chat->user !== false) ? $chat->user->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','is typing now...') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...');
	    } else {
	       $is_activated = false;
	    }

	    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
	    	$is_activated = true;
	    	$is_closed = true;
	    } else {
	    	$is_closed = false;
	    }
	    
	    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM && $contactRedirected == false) {
	    	$is_activated = false;
	    	$department = $chat->department;
	    	if ($department !== false) {
	    		$baseURL = (isset($Params['user_parameters_unordered']['mode']) && $Params['user_parameters_unordered']['mode'] == 'widget') ? erLhcoreClassDesign::baseurl('chat/chatwidget') : erLhcoreClassDesign::baseurl('chat/startchat');
	    		$ru = $baseURL.'/(department)/'.$department->id.'/(offline)/true/(leaveamessage)/true/(chatprefill)/'.$chat->id.'_'.$chat->hash;
	    		
	    		$msg = new erLhcoreClassModelmsg();
	    		$msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/checkchatstatus','Visitor has been redirected to contact form');
	    		$msg->chat_id = $chat->id;
	    		$msg->user_id = -1;
	    		$msg->time = time();
	    		erLhcoreClassChat::getSession()->save($msg);
	    		// We do not store last msg time for chat here, because in any case none of opeators has opened it
	    	}
	    }
    }

    $status = '';
    $nameSupport = '';
	if ($is_activated == true || $is_proactive_based == true) {
		if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT && ($user = $chat->user) !== false) {
			$status = $user->name_support . ' ' . erTranslationClassLhTranslation::getInstance ()->getTranslation ( 'chat/checkchatstatus', 'has joined this chat' );
			$nameSupport = $user->name_support;
		} elseif ($is_proactive_based == true) {
			if ($theme !== false && $theme->support_joined != '') {
				$status = $theme->support_joined;
			} else {
				$status = erTranslationClassLhTranslation::getInstance ()->getTranslation ( 'chat/checkchatstatus', 'A support staff member has joined this chat' );
			}
		}
	} elseif ($is_closed == true) {
		if ($theme !== false && $theme->support_closed != '') {
			$status = $theme->support_closed;
		} else {
			$status = erTranslationClassLhTranslation::getInstance ()->getTranslation ( 'chat/checkchatstatus', 'A support staff member has closed this chat' );
		}
	} elseif ($is_online == true) {
		if ($chat->number_in_queue > 1) {
			$status = erTranslationClassLhTranslation::getInstance ()->getTranslation ( 'chat/checkchatstatus', 'You are number' ) . ' ' . $chat->number_in_queue . ' ' . erTranslationClassLhTranslation::getInstance ()->getTranslation ( 'chat/checkchatstatus', 'in the queue. Please wait...' );
		} else {
			if ($theme !== false && $theme->pending_join != '') {
				$status = $theme->pending_join;
			} else {
				$status = erTranslationClassLhTranslation::getInstance ()->getTranslation ( 'chat/checkchatstatus', 'Pending a support staff member to join, you can write your questions, and as soon as a support staff member confirms this chat, he will get your messages' );
			}
		}
	} else {
		if ($theme !== false && $theme->noonline_operators != '') {
			$status = $theme->noonline_operators;
		} else {
			$status = erTranslationClassLhTranslation::getInstance ()->getTranslation ( 'chat/checkchatstatus', 'At this moment there are no logged in support staff members, but you can leave your messages' );
		}
	}

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
    exit;
}

$responseArray['error'] = false;
$responseArray['result'] = array(
    'status' => $status,
    'ru' => $ru,
    'ott' => $ott,
    'user' => $user,
    'activated' => $is_activated,
    'closed' => $is_closed,
	'name_support' => $nameSupport
);

echo erLhcoreClassRestAPIHandler::outputResponse($responseArray);

} catch ( Exception $e ) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => array('errors' => $e->getMessage())
    ));
}

exit;
?>