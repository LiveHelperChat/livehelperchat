<?php

session_write_close();

header ( 'content-type: application/json; charset=utf-8' );

$messageId = $_POST['message_id'];
$mailboxId = (int)$_POST['mailbox_id'];
$scheduled = (int)$_POST['scheduled'];

$mailbox = erLhcoreClassModelMailconvMailbox::fetch($mailboxId);

if ($scheduled == 0 && $mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PENDING) {
    $scheduled = 1;
    $cfg = erConfigClassLhConfig::getInstance();
    $worker = $cfg->getSetting( 'webhooks', 'worker' );

    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
        // We should start this job ASAP it's queue is free
        $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('inst_id' => $inst_id, 'ignore_timeout' => true, 'mailbox_id' => $mailboxId));
    } else {
        erLhcoreClassMailconvParser::syncMailbox(erLhcoreClassModelMailconvMailbox::fetch($mailboxId), ['live' => true, 'only_send' => true]);
    }
}

$message = erLhcoreClassModelMailconvMessage::findOne(array('filter' => array('message_id' => $messageId, 'mailbox_id' => $mailboxId)));

// Message record is created first
// We have to check was conversation assigned to a message already
if ($message instanceof erLhcoreClassModelMailconvMessage && $message->conversation_id > 0) {
    $template = "<a target=\"_blank\" href=\"". erLhcoreClassDesign::baseurl('front/default') . '/(mid)/' . $message->conversation_id ."/#!#chat-id-mc" . $message->conversation_id ."\"><span class='material-icons'>open_in_new</span>". $message->conversation_id . "</a>";
    echo json_encode(array('found' => true, 'conversation' => $template));
} else {

    $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Checking for ticket.') . ' [' . (int)$_POST['counter'] . ']';

    if ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PENDING) {
        $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Scheduling fetching.') . ' [' . (int)$_POST['counter'] . ']';
    } elseif ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS && $scheduled == 0) {
        $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Waiting for previous job to finish.') . ' [' . (int)$_POST['counter'] . ']';
    } elseif ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS && $scheduled == 1) {
        $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Fetching in progress.') . ' [' . (int)$_POST['counter'] . ']';
    }

    $template = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Working') . '. ' . $subStatus;

    echo json_encode(array('found' => false, 'scheduled' => $scheduled, 'progress' => $template),\JSON_INVALID_UTF8_IGNORE);
}

exit;
?>