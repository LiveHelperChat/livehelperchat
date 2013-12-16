<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) && $currentUser->hasAccessTo('lhfile','use_operator') === true )
{
	$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
	$data = (array)$fileData->data;

	$userData = $currentUser->getUserData();

	$upload_handler = new erLhcoreClassFileUpload(array('name_support' => $userData->name_support, 'user_id' => $currentUser->getUserID(), 'max_file_size' => $data['fs_max']*1024, 'accept_file_types_lhc' => '/\.('.$data['ft_op'].')$/i','chat' => $chat, 'download_via_php' => true, 'upload_dir' => 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$chat->id.'/'));

	echo json_encode(array('error' => 'false'));
}

exit;

?>