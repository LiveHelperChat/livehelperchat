<?php

session_write_close();

erLhcoreClassRestAPIHandler::setHeaders();

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    $response = null;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToWrite($conv) ) {
        $requestPayload = json_decode(file_get_contents('php://input'),true);
        erLhcoreClassMailconvValidator::sendReply($requestPayload, $response, $message, $currentUser->getUserID());
    }

    // We have tried to send an e-mail
    $response['send_tried'] = true;

    if (isset($response['send']) && $response['send'] == true) {

        $conv->lr_time = time();
        $conv->updateThis(['update' => ['lr_time']]);

        // Change to explicit action support
        if (isset($requestPayload['status'])) {
            if ($requestPayload['status'] == 2) {
                erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conv, 'user_id' => $currentUser->getUserID()]);
            } elseif ($requestPayload['status'] == 1) {
                $conv->status = 1;
                $conv->updateThis(['update' => ['status']]);
            } elseif ($requestPayload['status'] == 0) {
                $conv->status = 0;
                $conv->updateThis(['update' => ['status']]);
            }
        }

        $response['conv_status'] = $conv->status;

        echo json_encode($response,\JSON_INVALID_UTF8_IGNORE);
    } else {
        http_response_code(400);
        echo json_encode($response,\JSON_INVALID_UTF8_IGNORE);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['send_tried' => true, 'errors' => ['general' => $e->getMessage()]]);
}

exit;

?>