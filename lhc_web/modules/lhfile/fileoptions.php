<?php

erLhcoreClassRestAPIHandler::setHeaders();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

echo json_encode([
    'options' => [
        'ft_msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Not an accepted file type'),
        'fs_msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Filesize is too big'),
        'hash' => $chat->hash,
        'chat_id' => $chat->id,
        'fs' => $fileData['fs_max']*1024,
        'ft_us' => $fileData['ft_us']
    ],
    'html' => '<a class="file-uploader" href="#"><i class="material-icons chat-setting-item text-muted">attach_file</i><input id="fileupload" type="file" name="files[]" multiple></a>'
]);

exit();