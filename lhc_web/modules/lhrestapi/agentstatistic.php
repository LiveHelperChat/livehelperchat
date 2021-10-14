<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhstatistic', 'viewstatistic')) {
        throw new Exception('You do not have permission. `lhstatistic`, `viewstatistic` is required.');
    }

    $filterParams = erLhcoreClassSearchHandler::getParams(array(
        'module'        => 'chat',
        'module_file'   => 'agent_statistic',
        'format_filter' => true,
        'use_override'  => true,
        'uparams'       => $Params['user_parameters_unordered']
    ));

    $items = erLhcoreClassChatStatistic::getAgentStatistic(30, $filterParams['filter']);

    erLhcoreClassRestAPIHandler::outputResponse(
        array(
            'error'  => false,
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



