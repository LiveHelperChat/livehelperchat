<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

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

