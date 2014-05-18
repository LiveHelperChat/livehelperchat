<?php

// For IE to support headers if chat is installed on different domain
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

$embedMode = false;
$modeAppend = '';

if ((string)$Params['user_parameters_unordered']['embedmode'] == 'embed') {
	$embedMode = true;
	$modeAppend = '/(mode)/embed';
}

try {
	$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
	if ($chat->hash == $Params['user_parameters']['hash'] && erLhcoreClassChat::canReopen($chat,true) )
	{		
		if ($chat->status != erLhcoreClassModelChat::STATUS_ACTIVE_CHAT && $chat->status != erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
			
			// Reset to fresh state to workflow triggers to work			
			$chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
			$chat->nc_cb_executed = 0;
			$chat->na_cb_executed = 0;
			$chat->time = time(); // Set time to new		
			
			$chat->updateThis();
		}
		
		if ($Params['user_parameters_unordered']['mode'] == 'widget'){
			// Redirect user
			erLhcoreClassModule::redirect('chat/chatwidgetchat','/' . $chat->id . '/' . $chat->hash . $modeAppend );
			exit;
		} else {
			// Redirect user
			erLhcoreClassModule::redirect('chat/chat','/' . $chat->id . '/' . $chat->hash );
			exit;
		}
	}

} catch(Exception $e) {
   //
}
exit;




?>