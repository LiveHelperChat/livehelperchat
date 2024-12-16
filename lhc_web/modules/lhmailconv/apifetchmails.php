<?php

session_write_close();

erLhcoreClassRestAPIHandler::setHeaders();

try {

    $conv = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

    $cfg = erConfigClassLhConfig::getInstance();
    $worker = $cfg->getSetting( 'webhooks', 'worker' );

    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
        // We should start this job ASAP it's queue is free
        $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('inst_id' => $inst_id, 'ignore_timeout' => true, 'mailbox_id' => $conv->mailbox_id));
    } else {
        erLhcoreClassMailconvParser::syncMailbox($conv->mailbox, ['live' => true, 'only_send' => true]);
    }

    $mailbox = $conv->mailbox;
    $updated = $mailbox->last_sync_time > (int)$Params['user_parameters']['ts'] && $mailbox->sync_started > (int)$Params['user_parameters']['ts'];

    if ($updated == false && $conv->pending_sync == 0) {
        $conv->pending_sync = 1;
        $conv->updateThis(['update' => ['pending_sync']]);
    } elseif ($updated == true && $conv->pending_sync == 1) {
        $conv->pending_sync = 0;
        $conv->updateThis(['update' => ['pending_sync']]);
    }

    echo json_encode(['updated' => $updated]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['errors' => $e->getMessage()]);
}

exit;

?>