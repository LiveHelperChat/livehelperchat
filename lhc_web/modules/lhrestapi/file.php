<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhfile', 'upload_new_file')) {
            throw new Exception('You do not have permission. `lhfile`, `upload_new_file` is required.');
        }

        $errors = array();
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.before_admin_uploadfile.file_store', array('errors' => & $errors));

        if (empty($errors)) {

            $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
            $data = (array)$fileData->data;

            $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/au/';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.new.file_path', array('path' => & $path));

            $upload_handler = new erLhcoreClassFileUploadAdmin(array('chat_id' => (isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) ? $_POST['chat_id'] : 0), 'remove_meta' => (isset($data['remove_meta']) ? $data['remove_meta'] : false), 'user_id' => erLhcoreClassRestAPIHandler::getUserId(), 'persistent' => (isset($_POST['persistent']) && $_POST['persistent'] == 'true'), 'file_name_replace' => (isset($_POST['name_replace']) ? $_POST['name_replace'] : ''), 'file_name_manual' => (isset($_POST['name_prepend']) ? $_POST['name_prepend'] : ''), 'upload_dir' => $path, 'download_via_php' => true, 'max_file_size' => $data['fs_max'] * 1024, 'accept_file_types_lhc' => '/\.(' . $data['ft_op'] . ')$/i'));

            if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.file_new_admin.file_store', array('chat_file' => $upload_handler->uploadedFile));
            }

            $state = $upload_handler->uploadedFile->getState();
            $state['security_hash'] = $upload_handler->uploadedFile->security_hash;

            echo json_encode($upload_handler->uploadedFile);

        } else {
            throw new Exception(implode(PHP_EOL, $errors));
        }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhfile', 'use_operator')) {
            throw new Exception('You do not have permission. `lhrestapi`, `use_operator` is required.');
        }

        $file = erLhcoreClassModelChatFile::fetch((int)$Params['user_parameters']['id']);

        if (!($file instanceof erLhcoreClassModelChatFile)) {
            throw new Exception('File could not be found!');
        }

        $requestBody = json_decode(file_get_contents('php://input'),true);

        foreach ($requestBody as $attr => $attrValue) {
            $file->{$attr} = $attrValue;
            $file->updateThis();
        }

        $state = $file->getState();
        $state['security_hash'] = $file->security_hash;
        echo json_encode($file);

    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $file = erLhcoreClassModelChatFile::fetch((int)$Params['user_parameters']['id']);

        if (!($file instanceof erLhcoreClassModelChatFile)) {
            throw new Exception('File could not be found!');
        }

        if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhrestapi', 'file_download')) {
            throw new Exception('You do not have permission. `lhrestapi`, `file_download` is required.');
        }

        if ((isset($_GET['meta']) && $_GET['meta'] == 'true')) {
            $state = $file->getState();
            $state['security_hash'] = $file->security_hash;
            echo json_encode($file);
        } else {
            header('Content-type: '.$file->type);
            header('Content-Disposition: attachment; filename="'.$file->id.'-'.$file->chat_id.'.'.$file->extension.'"');

            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.download', array('chat_file' => $file));

            // There was no callbacks or file not found etc, we try to download from standard location
            if ($response === false) {
                echo file_get_contents($file->file_path_server);
            } else {
                echo $response['filedata'];
            }
        }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

        $file = erLhcoreClassModelChatFile::fetch((int)$Params['user_parameters']['id']);

        if (!($file instanceof erLhcoreClassModelChatFile)) {
            throw new Exception('File could not be found!');
        }

        if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhfile', 'file_delete')) {
            throw new Exception('You do not have permission to delete a user. `lhfile`, `file_delete` is required.');
        }

        $file->removeThis();

        erLhcoreClassRestAPIHandler::outputResponse(array('result' => true));
    }

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit;

?>