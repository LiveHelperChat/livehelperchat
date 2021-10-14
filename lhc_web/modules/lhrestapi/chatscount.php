<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();
    
    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhchat', 'use')) {
        throw new Exception('You do not have permission. `lhchat`, `use` is required.');
    }

    erLhcoreClassRestAPIHandler::outputResponse(erLhcoreClassRestAPIHandler::validateChatListCount());
} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();