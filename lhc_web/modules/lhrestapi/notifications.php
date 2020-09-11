<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'),true);

    $session = erLhcoreClassModelUserSession::findOne(array('filter' => array('token' => $Params['user_parameters']['token'])));

    if ($session instanceof erLhcoreClassModelUserSession) {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($requestBody['status']) && $requestBody['status'] == true) {
                $session->notifications_status = 1;
            } else {
                $session->notifications_status = 0;
            }

            $session->updateThis();

            erLhcoreClassRestAPIHandler::outputResponse(array(
                    'status' => $session->notifications_status
                )
            );

        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
            erLhcoreClassRestAPIHandler::outputResponse(array(
                    'status' => $session->notifications_status
                )
            );
        } else {
            throw new Exception('Unsupported request method');
        }

    } else {
        throw new Exception('User session could not be found!');
    }


} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();

