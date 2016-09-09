<?php

// For IE to support headers if chat is installed on different domain
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

$embedMode = false;
$fullHeight = (isset($Params['user_parameters_unordered']['fullheight']) && $Params['user_parameters_unordered']['fullheight'] == 'true') ? true : false;
$modeAppend = '';

if ((string)$Params['user_parameters_unordered']['embedmode'] == 'embed') {
	$embedMode = true;
	$modeAppend = '/(mode)/embed';
}

$modeAppend .= '/(fullheight)/';
$modeAppend .= ($fullHeight) ? 'true' : 'false';

$modeAppendTheme = '';
if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
		$Result['theme'] = $theme;
		$modeAppendTheme = '/(theme)/'.$theme->id;
	} catch (Exception $e) {

	}
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		try {
			$theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
			$Result['theme'] = $theme;
			$modeAppendTheme = '/(theme)/'.$theme->id;
		} catch (Exception $e) {

		}
	}
}

try {
	$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
	if ($chat->hash == $Params['user_parameters']['hash'] && erLhcoreClassChat::canReopen($chat,true) )
	{	
	    // Is IP blocked directly?
	    if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => erLhcoreClassIPDetect::getIP()))) > 0) {
	        header('Location: ' . $_SERVER['HTTP_REFERER']);
		    exit;
	    }
	    
	    /**
	     * is IP range blocked
	     * */
	    $ignorable_ip = erLhcoreClassModelChatConfig::fetch('banned_ip_range')->current_value;

	    if ( $ignorable_ip != '' && erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
	        header('Location: ' . $_SERVER['HTTP_REFERER']);
		    exit;
	    }	    
	    
		if ($chat->status != erLhcoreClassModelChat::STATUS_ACTIVE_CHAT && $chat->status != erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
			
			if (erLhcoreClassModelChatConfig::fetch('reopen_as_new')->current_value == 1 || $chat->user_id == 0) {
				// Reset to fresh state to workflow triggers to work			
				$chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
				$chat->nc_cb_executed = 0;
				$chat->na_cb_executed = 0;
				$chat->time = time(); // Set time to new		
			} else {
				$chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
			}
			
			$chat->updateThis();
		}
		
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.user_reopened',array('chat' => & $chat));
		
		if ($Params['user_parameters_unordered']['mode'] == 'widget'){
            // Redirect user
			erLhcoreClassModule::redirect('chat/chatwidgetchat','/' . $chat->id . '/' . $chat->hash . $modeAppend . $modeAppendTheme );
			exit;
		} else {
			// Redirect user
			erLhcoreClassModule::redirect('chat/chat','/' . $chat->id . '/' . $chat->hash . $modeAppendTheme );
			exit;
		}
	} else {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

} catch(Exception $e) {
   header('Location: ' . $_SERVER['HTTP_REFERER']);
   exit;
}
exit;




?>