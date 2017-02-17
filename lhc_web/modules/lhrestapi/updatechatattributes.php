<?php
header ( 'content-type: application/json; charset=utf-8' );
header ( 'Access-Control-Allow-Origin: *' );
header ( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept' );

$hash = $_POST['hash'];
$chatID = $_POST['chat_id'];

try {
	$chat = erLhcoreClassChat::getSession ()->load ( 'erLhcoreClassModelChat', $chatID );
	
	if ($chat !== false && $chat->hash == $hash) {
		
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
		
		echo json_encode(array('error' => false, 'stored' => true));
		exit;
	}
} catch ( Exception $e ) {
    echo json_encode(array('error' => true, 'result' => $e->getMessage()));
    exit;
}
exit ();

?>