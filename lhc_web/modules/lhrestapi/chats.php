<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    erLhcoreClassRestAPIHandler::validateRequest();
    erLhcoreClassRestAPIHandler::outputResponse(erLhcoreClassRestAPIHandler::validateChatList());
} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();