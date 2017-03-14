<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    $chat = erLhcoreClassModelChat::fetch((int)$_GET['chat_id']);

    if (erLhcoreClassRestAPIHandler::hasAccessToRead($chat) == true) {
        erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => false,
            'chat' => erLhcoreClassModelChat::fetch((int)$_GET['chat_id'])
        ));
    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'You do not have permission to read this chat!'));
    }

} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();