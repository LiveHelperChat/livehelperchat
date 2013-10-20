<?php


$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
{
	$upload_handler = new erLhcoreClassFileUpload(array('chat' => $chat, 'download_via_php' => true, 'upload_dir' => 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$chat->id.'/'));

} else {

}




exit;

?>