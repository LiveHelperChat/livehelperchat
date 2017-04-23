<?php

header('content-type: application/json; charset=utf-8');
try {
	$chat = erLhcoreClassChat::getSession ()->load ( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id'] );
	
	if ($chat->hash == $Params['user_parameters']['hash']) 	// Allow add messages only if chat is active
	{
		$msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msgid']);
				
		if ($msg->chat_id == $chat->id) {		
			$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncuser.tpl.php');
			$tpl->set('messages',array((array)$msg));
			$tpl->set('chat',$chat);
			$tpl->set('sync_mode',isset($Params['user_parameters_unordered']['mode']) ? $Params['user_parameters_unordered']['mode'] : '');
		
			echo json_encode(array('msg' => $tpl->fetch(),'error' => 'f'));
			exit;
		}
	}
		
} catch ( Exception $e ) {
	
}

echo json_encode(array('error' => 't'));
exit;

?>