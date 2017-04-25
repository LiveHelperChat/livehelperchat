<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;
$fullHeight = (isset($Params['user_parameters_unordered']['fullheight']) && $Params['user_parameters_unordered']['fullheight'] == 'true') ? true : false;

if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
	$tpl = erLhcoreClassTemplate::getInstance('lhchat/chatcheckoperatormessage.tpl.php');
	
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
	
	$proactiveInviteActive = erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value;
	
	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chatcheckoperatormessage', array('proactive_active' => & $proactiveInviteActive));

	$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('tag' => isset($_GET['tag']) ? $_GET['tag'] : false, 'uactiv' => (int)$Params['user_parameters_unordered']['uactiv'], 'wopen' => (int)$Params['user_parameters_unordered']['wopen'], 'tpl' => & $tpl, 'tz' => $Params['user_parameters_unordered']['tz'], 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'department' => $department, 'identifier' => (string)$Params['user_parameters_unordered']['identifier'], 'pages_count' => ((int)$Params['user_parameters_unordered']['count_page'] == 1 ? true : false), 'vid' => (string)$Params['user_parameters_unordered']['vid'], 'check_message_operator' => true, 'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => $proactiveInviteActive));
	
	// Exit if not required
	$statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value,'',false,$userInstance);
	if ($statusGeoAdjustment['status'] == 'offline' || $statusGeoAdjustment['status'] == 'hidden') {
		echo "lh_inst.stopCheckNewMessage();"; // Stop check for messages and save resources
		exit;
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
		
		$tpl->set('fullheight', $fullHeight);
		$tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
		$tpl->set('department',$department !== false ? implode('/', $department) : false);
		$tpl->set('uarguments',$uarguments !== false ? implode('/', $uarguments) : false);
		$tpl->set('operator',is_numeric($Params['user_parameters_unordered']['operator']) ? (int)$Params['user_parameters_unordered']['operator'] : false);
		$tpl->set('theme',is_numeric($Params['user_parameters_unordered']['theme']) && $Params['user_parameters_unordered']['theme'] > 0 ? (int)$Params['user_parameters_unordered']['theme'] : false);
		$tpl->set('visitor',$userInstance);
		$tpl->set('vid',(string)$Params['user_parameters_unordered']['vid']);
		$tpl->set('survey',is_numeric($Params['user_parameters_unordered']['survey']) ? (int)$Params['user_parameters_unordered']['survey'] : false);
		
		$dynamic = true;
		
		if ($userInstance->reopen_chat == 1 && ($chat = $userInstance->chat) !== false && $chat->user_status == erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN) {
			$tpl->set('reopen_chat',$chat);
			$dynamic = false;
		}
		
		// Execute request only if widget is not open
		if ($userInstance->operation != '' && (int)$Params['user_parameters_unordered']['wopen'] == 0) {
			$tpl->set('operation',$userInstance->operation);
			$userInstance->operation = '';
			$userInstance->operation_chat = '';
			$userInstance->saveThis();
		}
		
		// If there is no assigned default proactive invitations find dynamic one triggers
		if ($dynamic == true && $userInstance->operator_message == '' && $userInstance->message_seen == 0 && (int)$Params['user_parameters_unordered']['wopen'] == 0) {
		     $tpl->set('dynamic_processed',is_array($Params['user_parameters_unordered']['dyn']) ? $Params['user_parameters_unordered']['dyn'] : array());
		     $tpl->set('dynamic',$dynamic);
		     $tpl->set('dynamic_invitation', erLhcoreClassModelChatOnlineUser::getDynamicInvitation(array('online_user' => $userInstance, 'tag' => isset($_GET['tag']) ? $_GET['tag'] : false)));
		}
		
	    echo $tpl->fetch();
	}
}
exit;
?>