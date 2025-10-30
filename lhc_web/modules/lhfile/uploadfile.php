<?php

erLhcoreClassRestAPIHandler::setHeaders();

$fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$chatVariables = $chat->chat_variables_array;

if (isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true || (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1)) {

    $db = ezcDbInstance::get();

    try {
        $db->beginTransaction();

        $chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id'], false);

        $chatVariables = $chat->chat_variables_array;

        $auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
        $dataLog = (array)$auditOptions->data;

        if (!(isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true || (isset($chatVariables['lhc_fu']) && $chatVariables['lhc_fu'] == 1))) {
            echo json_encode(array('error' => 'true', 'error_msg' => 'Upload disabled!'));
            $db->rollback();

            if (isset($dataLog['log_files']) && $dataLog['log_files'] == 1) {
                erLhcoreClassLog::write('Upload disabled!',
                    ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'lhc',
                        'category' => 'files',
                        'line' => __LINE__,
                        'file' => __FILE__,
                        'object_id' => (int)$Params['user_parameters']['chat_id']
                    )
                );
            }

            // Make sure nothing changed since last request
            exit;
        }

        if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
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
                    'check_suspicious_pdf' => (isset($data['check_suspicious_pdf']) ? $data['check_suspicious_pdf'] : false),
                    'remove_meta' => (isset($data['remove_meta']) ? $data['remove_meta'] : false),
                    'max_res' => ($data['max_res'] ?? 0),
                    'antivirus' => $clamav,
                    'user_id' => 0,
                    'max_file_size' => $data['fs_max'] * 1024,
                    'accept_file_types_lhc' => '/\.(' . $data['ft_us'] . ')$/i',
                    'chat' => $chat,
                    'file_preview' => (isset($data['file_preview']) && $data['file_preview'] == true),
                    'download_via_php' => true,
                    'upload_dir' => $path));

                if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_store', array('chat'=> $chat, 'chat_file' => $upload_handler->uploadedFile));
                    $chat->user_typing_txt = '100%';

                } elseif (is_object($upload_handler->uploadedFile)) {
                    $db->rollback();

                    $chat->user_typing_txt = $upload_handler->uploadedFile->error;
                    echo json_encode(array('error' => 'true', 'error_msg' => $upload_handler->uploadedFile->error));

                    if (isset($dataLog['log_files']) && $dataLog['log_files'] == 1) {
                        erLhcoreClassLog::write($upload_handler->uploadedFile->error,
                            ezcLog::SUCCESS_AUDIT,
                            array(
                                'source' => 'lhc',
                                'category' => 'files',
                                'line' => __LINE__,
                                'file' => __FILE__,
                                'object_id' => (int)$Params['user_parameters']['chat_id']
                            )
                        );
                    }
                    exit;
                }

                $chat->user_typing = time();
                $chat->updateThis(array('update' => array('user_typing_txt','user_typing')));

                echo json_encode(array(
                    'error' => 'false',
                    'file_id' => $upload_handler->uploadedFile->id,
                    'security_hash' => $upload_handler->uploadedFile->security_hash,
            ));
            } else {
                echo json_encode(array('error' => 'true', 'error_msg' => implode(PHP_EOL, $errors)));
            }
        }

        $db->commit();

        if (isset($upload_handler) && $upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.after_save', array('chat'=> $chat, 'chat_file' => $upload_handler->uploadedFile));

            // Dispatch event only if minimum image width or height is set and file is an image
            if (
                isset($data['img_download_policy']) && $data['img_download_policy'] == 1 &&
                in_array($upload_handler->uploadedFile->extension, array('jfif','jpg', 'jpeg', 'png', 'gif')) &&
                ($upload_handler->uploadedFile->width > (isset($data['img_verify_min_dim']) ? $data['img_verify_min_dim'] : 100) || $upload_handler->uploadedFile->height > (isset($data['img_verify_min_dim']) ? $data['img_verify_min_dim'] : 100))
            ) {
                     erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.verify_img_file', array('chat'=> $chat, 'chat_file' => $upload_handler->uploadedFile));
            }
        }


    } catch (Exception $e) {
        echo json_encode(array('error' => 'true', 'error_msg' => $e->getMessage()));
        $db->rollback();

        if (isset($dataLog['log_files']) && $dataLog['log_files'] == 1) {
            erLhcoreClassLog::write($e->getMessage() . $e->getTraceAsString(),
                ezcLog::SUCCESS_AUDIT,
                array(
                    'source' => 'lhc',
                    'category' => 'files',
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'object_id' => (int)$Params['user_parameters']['chat_id']
                )
            );
        }
    }
}

exit;

?>