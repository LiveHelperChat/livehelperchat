<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat', 'module_file' => 'bot_search', 'format_filter' => true, 'use_override' => true));

    $items = erLhcoreClassModelGenericBotBot::getList(array_merge($filterParams['filter'],array('limit' => false)));

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

