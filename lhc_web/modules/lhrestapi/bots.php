<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $items = erLhcoreClassModelGenericBotBot::getList(array('limit' => false));

    erLhcoreClassRestAPIHandler::outputResponse(array
        (
            'error' => false,
            'result' => array_values($items)
        )
    );

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();

