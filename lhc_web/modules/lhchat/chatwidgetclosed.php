<?php

// For IE to support headers if chat is installed on different domain
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

// Check is there online user instance and user has messsages from operator in that case he have seen message from operator
if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {

    $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest();

    if ($userInstance !== false && $userInstance->has_message_from_operator == true) {
        $userInstance->message_seen = 1;
        $userInstance->saveThis();
    }
}

if (($hashSession = CSCacheAPC::getMem()->getSession('chat_hash_widget')) !== false) {

    list($chatID,$hash) = explode('_',$hashSession);

    try {
	        $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);

	        if ($chat->user_status != 1) {

	        	$db = ezcDbInstance::get();
	        	$db->beginTransaction();

			        // User closed chat
			        $chat->user_status = 1;
			        $chat->support_informed = 1;
			        $chat->user_typing = time()-5;// Show for shorter period these status messages
			        $chat->is_user_typing = 1;
			        $chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','User has left the chat!'),ENT_QUOTES);

			        erLhcoreClassChat::getSession()->update($chat);

		        $db->commit();
	        }

    } catch (Exception $e) {
        // Do nothing
    }

    // This is called then user closes chat widget
    // We mark session variable as user closed the chat
    CSCacheAPC::getMem()->setSession('chat_hash_widget',false);

}

exit;

?>