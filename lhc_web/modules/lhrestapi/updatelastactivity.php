<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    $user = erLhcoreClassModelUser::fetch((int) $Params['user_parameters']['user_id']);

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhrestapi', 'updatelastactivity') && $user->id != erLhcoreClassRestAPIHandler::getUserId()) {
        throw new Exception('You do not have permission. `lhrestapi`, `updatelastactivity` is required or be the owner of an user.');
    }

    if ($user instanceof erLhcoreClassModelUser) {

        $ts = time();
        erLhcoreClassUserDep::updateLastActivityByUser($user->id, $ts);

        echo erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => false,
            'result' => array('update' => true, 'ts' => $ts)
        ));

    } else {
        throw new Exception('User could not be found!');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();