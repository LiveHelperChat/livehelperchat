<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

if (($hashSession = CSCacheAPC::getMem()->getSession('chat_hash_widget')) !== false) {
    
    list($chatID,$hash) = explode('_',$hashSession);
    
    try {
        $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);
        
         // User closed chat    
        $chat->user_status = 1;        
        $chat->support_informed = 0;
        erLhcoreClassChat::getSession()->update($chat); 
    } catch (Exception $e) {
        // Do nothing
    }
        
    // This is called then user closes chat widget
    // We mark session variable as user closed the chat
    CSCacheAPC::getMem()->setSession('chat_hash_widget',false);
    
    
}

exit;

?>