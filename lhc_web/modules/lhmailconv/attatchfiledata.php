<?php

erLhcoreClassRestAPIHandler::setHeaders();

$file = erLhcoreClassModelChatFile::fetch($Params['user_parameters']['id']);

echo json_encode([
    'name' => $file->upload_name,
    'id' => $file->id,
    'new' => false,
    'url' => '//' . erLhcoreClassDesign::baseurl('file/downloadfile') . "/{$file->id}/{$file->security_hash}"
]);

exit;

?>