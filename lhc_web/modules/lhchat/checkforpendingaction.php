<?php 

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) && $chat->operation != '') {
	$operation = explode("\n", trim($chat->operation));
	$chat->operation = '';
	$chat->updateThis();
	echo json_encode(array('error' => 'false','result' => $operation));
} else {
	echo json_encode(array('error' => 'true','result' => ''));
}

exit;

?>