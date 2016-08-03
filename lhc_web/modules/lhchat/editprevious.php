<?php 

try {
	$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
	
	if ( erLhcoreClassChat::hasAccessToRead($chat) )
	{
		$lastMessage = erLhcoreClassChat::getGetLastChatMessageEdit($chat->id,$currentUser->getUserID());
		
		if (isset($lastMessage['msg'])) {
			
			$array = array();
			$array['id'] = $lastMessage['id'];
			$array['msg'] = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $lastMessage['msg']);
			$array['error'] = 'f';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_edit_previous_admin_returned',array('response' => & $array));

			echo json_encode($array);
			
		} else {
			echo json_encode(array('error' => 't'));
		}
	}
} catch (Exception $e) {
	echo json_encode(array('error' => 't'));
}
exit;


?>