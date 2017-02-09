<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) && $currentUser->hasAccessTo('lhfile','use_operator') === true )
{
    $errors = array();
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.before_admin_uploadfile.file_store', array('errors' => & $errors));

    if (empty($errors)) {
        $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
        $data = (array)$fileData->data;

        $userData = $currentUser->getUserData();

        $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/' . $chat->id . '/';

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfileadmin.file_path', array('path' => & $path, 'storage_id' => $chat->id));

        $upload_handler = new erLhcoreClassFileUpload(array('name_support' => $userData->name_support, 'user_id' => $currentUser->getUserID(), 'max_file_size' => $data['fs_max'] * 1024, 'accept_file_types_lhc' => '/\.(' . $data['ft_op'] . ')$/i', 'chat' => $chat, 'download_via_php' => true, 'upload_dir' => $path));

        if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfileadmin.file_store', array('chat_file' => $upload_handler->uploadedFile));
        }

        echo json_encode(array('error' => 'false'));
    } else {
        echo json_encode(array('error' => 'true', 'error_msg' => implode(PHP_EOL, $errors)));
    }
}

exit;

?>