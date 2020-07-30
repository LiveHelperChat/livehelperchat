<?php

$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
$data = (array)$fileData->data;

try {
    $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/au/';

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.new.file_path', array('path' => & $path));

    $upload_handler = new erLhcoreClassFileUploadAdmin(array('remove_meta' => (isset($data['remove_meta']) ? $data['remove_meta'] : false), 'user_id' => $currentUser->getUserID(), 'persistent' => true, 'upload_dir' => $path, 'download_via_php' => true, 'max_file_size' => $data['fs_max'] * 1024, 'accept_file_types_lhc' => '/\.(' . $data['ft_op'] . ')$/i'));

    if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.file_new_admin.file_store', array('chat_file' => $upload_handler->uploadedFile));
    }

    echo json_encode([
        'name' => $upload_handler->uploadedFile->upload_name,
        'id' => $upload_handler->uploadedFile->id,
        'new' => true,
        'url' => erLhcoreClassDesign::baseurl('file/downloadfile') . "/{$upload_handler->uploadedFile->id}/{$upload_handler->uploadedFile->security_hash}"
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;

?>