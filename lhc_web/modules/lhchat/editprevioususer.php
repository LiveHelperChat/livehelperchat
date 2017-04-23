<?php 

header('content-type: application/json; charset=utf-8');

try {
	$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
  
	if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))
	{			
		$lastMessage = erLhcoreClassChat::getGetLastChatMessageEdit($chat->id,0);
		
		if (isset($lastMessage['msg'])) {
			
			$array['id'] = $lastMessage['id'];
			$array['msg'] = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $lastMessage['msg']);
			$array['error'] = 'f';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_edit_previous_user_returned',array('response' => & $array));

			echo json_encode($array);	
			exit;		
		};
	}
	
} catch (Exception $e) {

}

echo json_encode(array('error' => 't'));	 

exit;