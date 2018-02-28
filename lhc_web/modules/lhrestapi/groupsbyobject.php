<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $objects = erLhcoreClassModelGroupObject::getList(array('filter' => array('object_id' => (int)$Params['user_parameters']['object_id'], 'type' => (int)$Params['user_parameters']['type'])));

    erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => false,
            'result' => array_values($objects)
        )
    );

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();

