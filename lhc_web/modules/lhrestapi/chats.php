<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();
    erLhcoreClassRestAPIHandler::outputResponse(erLhcoreClassRestAPIHandler::validateChatList());
} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();