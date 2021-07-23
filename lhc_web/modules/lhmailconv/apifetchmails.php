<?php

session_write_close();

erLhcoreClassRestAPIHandler::setHeaders();

try {

    $conv = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

    $cfg = erConfigClassLhConfig::getInstance();
    $worker = $cfg->getSetting( 'webhooks', 'worker' );

    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('mailbox_id' => $conv->mailbox_id));
    } else {
        erLhcoreClassMailconvParser::syncMailbox($conv->mailbox, ['live' => true, 'only_send' => true]);
    }

    echo json_encode(['somedata' => 'synced']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['errors' => $e->getMessage()]);
}

exit;

?>