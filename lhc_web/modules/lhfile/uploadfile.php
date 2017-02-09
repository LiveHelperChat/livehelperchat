<?php

$fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

if (isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) {
	$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
	if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
	{
	    $errors = array();
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.before_user_uploadfile.file_store', array('errors' => & $errors));

        if (empty($errors)) {
            $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
            $data = (array)$fileData->data;
            $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/' . $chat->id . '/';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_path', array('path' => & $path, 'storage_id' => $chat->id));

            $upload_handler = new erLhcoreClassFileUpload(array('user_id' => 0, 'max_file_size' => $data['fs_max'] * 1024, 'accept_file_types_lhc' => '/\.(' . $data['ft_us'] . ')$/i', 'chat' => $chat, 'download_via_php' => true, 'upload_dir' => $path));

            if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_store', array('chat_file' => $upload_handler->uploadedFile));
            }

            $chat->user_typing = time();
            $chat->user_typing_txt = '100%';
            erLhcoreClassChat::getSession()->update($chat);

            echo json_encode(array('error' => 'false'));
        } else {
            echo json_encode(array('error' => 'true', 'error_msg' => implode(PHP_EOL, $errors)));
        }
	}
}

exit;

?>