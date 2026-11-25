<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhrestapi', 'object_api')) {
        throw new Exception('You do not have permission. `lhrestapi`, `object_api` is required.');
    }

    $objects = erLhcoreClassModelGroupObject::getGroups((int)$Params['user_parameters']['object_id'], (int)$Params['user_parameters']['type']);

    erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => false,
            'result' => $objects
        )
    );

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();

