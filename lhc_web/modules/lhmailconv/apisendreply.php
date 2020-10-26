<?php

session_write_close();

erLhcoreClassRestAPIHandler::setHeaders();

try {

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    $response = null;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) ) {
        $requestPayload = json_decode(file_get_contents('php://input'),true);
        erLhcoreClassMailconvValidator::sendReply($requestPayload, $response, $message);
    }

    // We have tried to send an e-mail
    $response['send_tried'] = true;

    if ($response['send'] == true) {

        // There are no more unresponded messages in this conversation we can close this conversation
        if (erLhcoreClassModelMailconvMessage::getCount(['filternot' => ['status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED],'filter' => ['conversation_id' => $conv->id]]) == 0) {
            erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conv, 'user_id' => $currentUser->getUserID()]);
        }

        echo json_encode($response);
    } else {
        http_response_code(400);
        echo json_encode($response);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['send_tried' => true, 'errors' => ['general' => $e->getMessage()]]);
}

exit;

?>