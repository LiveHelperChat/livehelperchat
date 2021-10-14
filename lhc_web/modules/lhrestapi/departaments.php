<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhdepartment', 'list')) {
        throw new Exception('You do not have permission. `lhdepartment`, `list` is required.');
    }

    erLhcoreClassRestAPIHandler::outputResponse(erLhcoreClassRestAPIHandler::validateDepartaments());
} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();