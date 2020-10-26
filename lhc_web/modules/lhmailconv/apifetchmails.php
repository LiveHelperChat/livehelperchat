<?php

session_write_close();

erLhcoreClassRestAPIHandler::setHeaders();

try {

    $conv = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

    erLhcoreClassMailconvParser::syncMailbox($conv->mailbox, ['live' => true, 'only_send' => true]);
    echo json_encode(['somedata' => 'synced']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['errors' => $e->getMessage()]);
}

exit;

?>