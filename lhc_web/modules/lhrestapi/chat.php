<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'),true);

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['id']);
        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }

        foreach ($requestBody as $attr => $value) {
            if ($attr != 'id') { // we never update ID
                $chat->{$attr} = $value;
            }
        }

        $chat->saveThis();

    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $chat = erLhcoreClassModelChat::fetch((int)$Params['user_parameters']['id']);
        if (!($chat instanceof erLhcoreClassModelChat)) {
            throw new Exception('Chat could not be found!');
        }
        $chat->removeThis();

        erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => true));
        exit;
    }

    erLhcoreClassRestAPIHandler::outputResponse(array
        (
            'error' => false,
            'result' => $chat
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

