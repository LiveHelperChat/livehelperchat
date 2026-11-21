<?php

try {
    erLhcoreClassRestAPIHandler::setHeaders();

    // validateRequest() has to run first, otherwise self::$apiKey is null and
    // hasAccessTo() can never return true.
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'administratecampaigs')) {
        throw new Exception('You do not have permission. `lhchat`, `administratecampaigs` is required.');
    }

    erLhcoreClassRestAPIHandler::outputResponse(erLhcoreClassRestAPIHandler::validateCampaignConversionList());

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();