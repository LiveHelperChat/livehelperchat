<?php

$online_user = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters']['vid']);

if ($online_user !== false && isset($online_user->online_attr_system_array['ishare_enabled']) && $online_user->online_attr_system_array['ishare_enabled'] == 1) // Allow add messages only if chat is active
{
    $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
    $data = (array) $fileData->data;
    $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/ou' . $online_user->id . '/';
    
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_path', array(
        'path' => & $path,
        'storage_id' => $online_user->id
    ));
    
    $upload_handler = new erLhcoreClassFileUpload(array(
        'user_id' => 0,
        'max_file_size' => $data['fs_max'] * 1024,
        'accept_file_types_lhc' => '/\.(' . $data['ft_us'] . ')$/i',
        'online_user' => $online_user,
        'download_via_php' => true,
        'upload_dir' => $path
    ));
    
    if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_store', array(
            'chat_file' => $upload_handler->uploadedFile
        ));
    };
    
    echo json_encode(array(
        'error' => 'false'
    ));
}


exit;

?>