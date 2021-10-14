<?php

try {
    erLhcoreClassRestAPIHandler::setHeaders();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'administratecampaigs')) {
        throw new Exception('You do not have permission. `lhchat`, `administratecampaigs` is required.');
    }

    erLhcoreClassRestAPIHandler::validateRequest();

    erLhcoreClassRestAPIHandler::outputResponse(erLhcoreClassRestAPIHandler::validateCampaignConversionList());

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();