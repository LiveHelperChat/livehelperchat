<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhmailconv', 'use_admin')) {
        throw new Exception('You do not have permission. `lhmailconv`, `use_admin` is required.');
    }

    erLhcoreClassRestAPIHandler::outputResponse( \LiveHelperChat\mailConv\helpers\RestAPIValidator::validateConversationList());

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();