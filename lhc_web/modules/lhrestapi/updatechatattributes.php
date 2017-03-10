<?php

try {
	
    erLhcoreClassRestAPIHandler::validateRequest();
    
	if (isset($_POST['chat_id'])) {
	    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $_POST['chat_id']);
	} else {
	    throw new Exception('chat_id has to be provided!');
	}
	
	if (isset($_POST['hash'])) {
	    $hash = $_POST['hash'];
	} else {
	    throw new Exception('hash has to be provided!');
	}
	
	if ($chat->hash == $_POST['hash']) {
		
		$data = $_POST ['data'];
		$jsonData = json_decode ( $data, true );
		
		erLhcoreClassChatValidator::validateUpdateAttribute ( $chat, $jsonData);
		
		$chat->user_typing = time();
		$chat->user_typing_txt = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/refreshcustomfields','Custom chat data was saved');
		$chat->operation_admin .= "lhinst.updateVoteStatus(".$chat->id.");";
		$chat->saveThis();
		
		// Force operators to check for new messages
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed_chat', array(
            'chat' => & $chat
		));
		
		echo erLhcoreClassRestAPIHandler::outputResponse(array('stored' => 'true'));
		exit;
	}
} catch ( Exception $e ) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => array('errors' => $e->getMessage())
    ));
}
exit ();

?>