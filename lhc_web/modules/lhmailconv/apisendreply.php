<?php

erLhcoreClassRestAPIHandler::setHeaders();

$message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

$conv = $message->conversation;

$response = null;

if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) ) {
    $requestPayload = json_decode(file_get_contents('php://input'),true);
    erLhcoreClassMailconvValidator::sendReply($requestPayload, $response, $message);
}

echo json_encode($response);
exit;

?>