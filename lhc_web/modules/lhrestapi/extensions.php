<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    $cfg = erConfigClassLhConfig::getInstance();

    $extensions = $cfg->getOverrideValue('site','extensions');

    erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => false,
        'result' => $extensions
    ));

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();