<?php

try {
    erLhcoreClassRestAPIHandler::setHeaders();

    erLhcoreClassRestAPIHandler::validateRequest();

    erLhcoreClassRestAPIHandler::outputResponse(erLhcoreClassRestAPIHandler::validateCampaignConversionList());

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();