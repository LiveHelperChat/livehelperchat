<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'),true);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $bot = new erLhcoreClassModelGenericBotBot();

    } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {

        $bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);
        if (!($bot instanceof erLhcoreClassModelGenericBotBot)) {
            throw new Exception('Bot could not be found!');
        }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);
        if (!($bot instanceof erLhcoreClassModelGenericBotBot)) {
            throw new Exception('Bot could not be found!');
        }
        $bot->removeThis();

        erLhcoreClassRestAPIHandler::outputResponse(array('error' => false,'result' => true));
        exit;
    }

    $Errors = erLhcoreClassGenericBot::validateBot($bot, array('payload_data' => $requestBody));

    if (count($Errors) == 0) {
        $bot->saveThis();

        $userPhotoErrors = erLhcoreClassGenericBot::validateBotPhotoPayload($bot, array('payload' => $requestBody));

        if ($userPhotoErrors !== false && count($userPhotoErrors) == 0) {
            $bot->saveThis();
        }

    } else {
        throw new Exception(implode("\n",$Errors));
    }
    
    erLhcoreClassRestAPIHandler::outputResponse(array
        (
            'error' => false,
            'result' => $bot
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

