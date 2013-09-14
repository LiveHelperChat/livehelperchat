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

		$chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
		$chat->updateThis();

		// Store hash if user reloads page etc, we show widget
		// CSCacheAPC::getMem()->setSession('chat_hash_widget',$chat->id.'_'.$chat->hash);

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