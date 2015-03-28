<?php

$onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['online_user_id']);

if ( $currentUser->hasAccessTo('lhfile','use_operator') === true )
{
	$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
	$data = (array)$fileData->data;

	$userData = $currentUser->getUserData();

	$path = 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/ou'.$onlineUser->id.'/';
	
	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfileadmin.file_path',array('path' => & $path, 'storage_id' => $onlineUser->id));
	
	$upload_handler = new erLhcoreClassFileUpload(array('name_support' => $userData->name_support, 'user_id' => $currentUser->getUserID(), 'max_file_size' => $data['fs_max']*1024, 'accept_file_types_lhc' => '/\.('.$data['ft_op'].')$/i', 'online_user' => $onlineUser, 'download_via_php' => true, 'upload_dir' => $path));

	if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile)
	{
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfileadmin.file_store', array('chat_file' => $upload_handler->uploadedFile));
	}
	
	echo json_encode(array('error' => 'false'));
}

exit;

?>