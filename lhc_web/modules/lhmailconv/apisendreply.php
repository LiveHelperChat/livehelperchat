<?php

session_write_close();

erLhcoreClassRestAPIHandler::setHeaders();

try {

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    $response = null;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) ) {
        $requestPayload = json_decode(file_get_contents('php://input'),true);
        erLhcoreClassMailconvValidator::sendReply($requestPayload, $response, $message, $currentUser->getUserID());
    }

    // We have tried to send an e-mail
    $response['send_tried'] = true;

    if ($response['send'] == true) {

        $conv->lr_time = time();
        $conv->updateThis(['update' => ['lr_time']]);

        // Our response is the last one
        // So we can close conversation
        if (erLhcoreClassModelMailconvMessage::getCount(['filtergt' => ['id' => $message->id], 'filternot' => ['status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED],'filter' => ['conversation_id' => $conv->id]]) == 0) {
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