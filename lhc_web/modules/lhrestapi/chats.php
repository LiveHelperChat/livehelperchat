<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'use')) {
        throw new Exception('You do not have permission. `lhchat`, `use` is required.');
    }

    erLhcoreClassRestAPIHandler::outputResponse(erLhcoreClassRestAPIHandler::validateChatList(), 'json', \JSON_INVALID_UTF8_IGNORE);

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();