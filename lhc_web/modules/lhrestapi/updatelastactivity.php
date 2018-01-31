<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    $user = erLhcoreClassModelUser::fetch((int) $Params['user_parameters']['user_id']);

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
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();