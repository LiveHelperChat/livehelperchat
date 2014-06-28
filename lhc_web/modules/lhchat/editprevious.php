<?php 

try {
	$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
	
	if ( erLhcoreClassChat::hasAccessToRead($chat) )
	{
		$lastMessage = erLhcoreClassChat::getGetLastChatMessageEdit($chat->id,$currentUser->getUserID());
		
		if (isset($lastMessage['msg'])) {
			
			$array = array();
			$array['id'] = $lastMessage['id'];
			$array['msg'] = $lastMessage['msg'];
			$array['error'] = 'f';
			
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