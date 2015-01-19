<?php

// For IE to support headers if chat is installed on different domain
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

// Check is there online user instance and user has messsages from operator in that case he have seen message from operator
if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {

    $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'vid' => $Params['user_parameters_unordered']['vid']));

    if ($userInstance !== false && $userInstance->has_message_from_operator == true) {
        $userInstance->message_seen = 1;
        $userInstance->message_seen_ts = time();
        $userInstance->saveThis();
    }
}

if ($Params['user_parameters_unordered']['hash'] != '') {
    list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);
    try {
	        $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);
	        if ($chat->hash == $hash && $chat->user_status != 1) {

	        	       	
		        	$db = ezcDbInstance::get();
		        	$db->beginTransaction();
	
				        // User closed chat
				        $chat->user_status = 1;
				        $chat->support_informed = 1;
				        $chat->user_closed_ts = time();
				        				        
				        if ($chat->user_typing < (time()-12)) {
				        	$chat->user_typing = time()-5;// Show for shorter period these status messages
				        	$chat->is_user_typing = 1;
				        	$chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','User has left the chat!'),ENT_QUOTES);
				        }
				        	
				        if ( ($onlineuser = $chat->online_user) !== false) {
				        	$onlineuser->reopen_chat = 0;
				        	$onlineuser->saveThis();
				        }
				        
				        erLhcoreClassChat::getSession()->update($chat);
	
			        $db->commit();
	        
	        }
    } catch (Exception $e) {
        // Do nothing
    }
}

exit;

?>