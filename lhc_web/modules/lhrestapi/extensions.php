<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhrestapi', 'list_extensions')) {
        throw new Exception('You do not have permission. `lhrestapi`, `list_extensions` is required.');
    }

    $cfg = erConfigClassLhConfig::getInstance();

    $extensions = $cfg->getOverrideValue('site','extensions');

    erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => false,
        'result' => array_values($extensions)
    ));

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();