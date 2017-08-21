<?php

$fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

if (isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) {

    $db = ezcDbInstance::get();

    try {
        $db->beginTransaction();

        $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

        if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
        {
            $errors = array();
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.before_user_uploadfile.file_store', array('errors' => & $errors));

            if (empty($errors)) {
                $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
                $data = (array)$fileData->data;
                $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/' . $chat->id . '/';

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_path', array('path' => & $path, 'storage_id' => $chat->id));

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
                    'chat' => $chat,
                    'download_via_php' => true,
                    'upload_dir' => $path));

                if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_store', array('chat_file' => $upload_handler->uploadedFile));
                    $chat->user_typing_txt = '100%';
                } elseif (is_object($upload_handler->uploadedFile)) {
                    $chat->user_typing_txt = $upload_handler->uploadedFile->error;
                    echo json_encode(array('error' => 'true', 'error_msg' => $upload_handler->uploadedFile->error ));
                    exit;
                }

                $chat->user_typing = time();
                erLhcoreClassChat::getSession()->update($chat);

                echo json_encode(array('error' => 'false'));
            } else {
                echo json_encode(array('error' => 'true', 'error_msg' => implode(PHP_EOL, $errors)));
            }
        }

        $db->commit();

    } catch (Exception $e) {
        echo json_encode(array('error' => 'true', 'error_msg' => $e->getMessage()));
        $db->rollback();
    }
}

exit;

?>