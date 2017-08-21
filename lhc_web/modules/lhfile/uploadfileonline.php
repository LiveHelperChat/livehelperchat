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

    $clamav = false;

    if (isset($data['clamav_enabled']) && $data['clamav_enabled'] == true) {

        $opts = array();

        if (isset($data['clamd_sock']) && !empty($data['clamd_sock'])) {
            $opts['clamd_sock'] = $data['clamd_sock'];
        }

        if (isset($data['clamd_sock_len']) && !empty($data['clamd_sock_len'])) {
            $opts['clamd_sock_len'] = $data['clamd_sock_len'];
        }

        $clamav = new Clamav($opts);
    }

    $upload_handler = new erLhcoreClassFileUpload(array(
        'antivirus' => $clamav,
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
    } elseif (is_object($upload_handler->uploadedFile)) {
        echo json_encode(array('error' => 'true', 'error_msg' => $upload_handler->uploadedFile->error ));
        exit;
    }
    
    echo json_encode(array(
        'error' => 'false'
    ));
}


exit;

?>