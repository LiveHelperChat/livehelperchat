<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

try {
    erLhcoreClassRestAPIHandler::validateRequest();
    
    $ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;
    
    $ip = isset($_POST['ip']) ? $_POST['ip'] : erLhcoreClassIPDetect::getIP();
    
    if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored($ip, explode(',',$ignorable_ip))) {
    	
    	if (is_array($Params['user_parameters_unordered']['department'])){
    		erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
    		$department = $Params['user_parameters_unordered']['department'];
    	} else {
    		$department = false;
    	}
    	
    	if (is_array($Params['user_parameters_unordered']['ua'])){
    		$uarguments = $Params['user_parameters_unordered']['ua'];
    	} else {
    		$uarguments = false;
    	}
    	
    	$vid = (string)$Params['user_parameters_unordered']['vid'];
    	
    	if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
        	// Generate VID if not provided
        	if ($vid == '') {	    
        	    $allchar = "abcdefghijklmnopqrstuvwxyz1234567890";
        	    $str = "" ;
        	    mt_srand (( double) microtime() * 1000000 );
        	    for ( $i = 0; $i < 20 ; $i++ ) {
        	        $str .= substr( $allchar, mt_rand (0,36), 1 );
        	    }	    
        	    $vid = $str;
        	}
    	}
    	
    	$proactiveInviteActive = erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value;
    	
    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chatcheckoperatormessage', array('proactive_active' => & $proactiveInviteActive));
    
    	$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('ip' => $ip, 'tag' => isset($_POST['tag']) ? $_POST['tag'] : false, 'uactiv' => (int)$Params['user_parameters_unordered']['uactiv'], 'wopen' => (int)$Params['user_parameters_unordered']['wopen'], 'tz' => $Params['user_parameters_unordered']['tz'], 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'department' => $department, 'identifier' => (string)$Params['user_parameters_unordered']['identifier'], 'pages_count' => ((int)$Params['user_parameters_unordered']['count_page'] == 1 ? true : false), 'vid' => (string)$vid, 'check_message_operator' => true, 'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => $proactiveInviteActive));
    	
    	// Exit if not required
    	$statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value,'',false,$userInstance);
    	if ($statusGeoAdjustment['status'] == 'offline' || $statusGeoAdjustment['status'] == 'hidden') {
    		echo json_encode(array('error' => false, 'result' => array('vid' => $vid, 'action' => 'disable_check')));
    	}
    	
    	if ((int)$Params['user_parameters_unordered']['count_page'] == 1 && erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1 && isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    		erLhcoreClassModelChatOnlineUserFootprint::addPageView($userInstance);
    	}
    	
    	if ($userInstance !== false) {
    		
    		if ($userInstance->invitation_id == -1) {
    			$userInstance->invitation_id = 0;
    			$userInstance->invitation_assigned = true;
    			$userInstance->saveThis();
    		}
    			
    		$priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false;
    		$department = $department !== false ? implode('/', $department) : false;
    		$uarguments = $uarguments !== false ? implode('/', $uarguments) : false;
    		$operator = is_numeric($Params['user_parameters_unordered']['operator']) ? (int)$Params['user_parameters_unordered']['operator'] : false;
    		$theme = is_numeric($Params['user_parameters_unordered']['theme']) && $Params['user_parameters_unordered']['theme'] > 0 ? (int)$Params['user_parameters_unordered']['theme'] : false;
    		$survey = is_numeric($Params['user_parameters_unordered']['survey']) ? (int)$Params['user_parameters_unordered']['survey'] : false;
    		
    		if ($userInstance->has_message_from_operator) {
    		    $urlAppend = ($department !== false ? '/(department)/'.$department : '') . ($theme !== false ? '/(theme)/'.$theme : '') . ($operator !== false ? '/(operator)/'.$operator : '') . ($priority !== false ? '/(priority)/'.$priority : '') . ($uarguments !== false ? '/(ua)/'.$uarguments : '') . ($survey !== false ? '/(survey)/'.$survey : '') . '/(vid)/' . $vid . ($userInstance->invitation_assigned == true ? '/(playsound)/true' : '');
    		    
    		    $name_support = null;
    		    if ($userInstance->operator_user !== false) {
    		        $name_support = $userInstance->operator_user->name_support;
    		    }
    		    
    		    echo json_encode(array('error' => false, 'result' => array('vid' => $userInstance->vid, 'action' => 'read_message', 'args' => array('name_support' => $name_support, 'message' => $userInstance->operator_message, 'url_append' => $urlAppend))));
    		    exit;
    		}
    		
    		if ($userInstance->reopen_chat == 1 && ($chat = $userInstance->chat) !== false && $chat->user_status == erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN) {		    
    		    echo json_encode(array('error' => false, 'result' => array('vid' => $userInstance->vid, 'action' => 'reopen_chat', 'args' => array('chat_id' => $chat->id, 'hash' => $chat->hash))));
    		    exit;
    		}
    		
    		// Execute request only if widget is not open
    		if ($userInstance->operation != '' && (int)$Params['user_parameters_unordered']['wopen'] == 0) {
    			$tpl->set('operation',$userInstance->operation);
    			$userInstance->operation = '';
    			$userInstance->operation_chat = '';
    			$userInstance->saveThis();
    		}
    		
    		echo erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => array('action' => 'continue',  'vid' => $userInstance->vid,)));
    	} else {
    	    echo erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => array('action' => 'disable_check')));
    	}
    } else {
        echo erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => array('action' => 'disable_check')));
    }
} catch ( Exception $e ) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => array('errors' => $e->getMessage())
    ));
}

exit;
?>