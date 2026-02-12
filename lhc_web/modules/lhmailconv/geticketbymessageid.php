<?php

session_write_close();

header ( 'content-type: application/json; charset=utf-8' );

$messageId = $_POST['message_id'];
$mailboxId = (int)$_POST['mailbox_id'];
$scheduled = (int)$_POST['scheduled'];
$copyId = (int)$_POST['copy_id'];

$mailbox = erLhcoreClassModelMailconvMailbox::fetch($mailboxId);

if ($scheduled == 0 && $mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PENDING) {
    $scheduled = 1;
    $cfg = erConfigClassLhConfig::getInstance();
    $worker = $cfg->getSetting( 'webhooks', 'worker' );

    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {

        // Do not schedule job if copy is not created yet
        if ($copyId > 0) {
            $copy = \LiveHelperChat\Models\mailConv\SentCopy::fetch($copyId);
            if ($copy !== false) {
                $scheduled = 0;
            }
        }

        if ($scheduled === 1) {
            // We should start this job ASAP it's queue is free
            $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
            erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('inst_id' => $inst_id, 'ignore_timeout' => true, 'mailbox_id' => $mailboxId));
        }

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

    $copyFound = false;

    // Check copy status
    if ($copyId > 0) {

        $copy = \LiveHelperChat\Models\mailConv\SentCopy::fetch($copyId);

        if ($copy instanceof \LiveHelperChat\Models\mailConv\SentCopy) {
            $copyFound = true;
            if ($copy->status === \LiveHelperChat\Models\mailConv\SentCopy::STATUS_PENDING) {
                $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Pending for copy to be created in send folder') . ' [attempt - ' . (int)$_POST['counter'] . ']';
            } else {
                $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Creating copy in send folder is in progress') . ' [attempt - ' . (int)$_POST['counter'] . ']';
            }
        }
    }

    if ($copyFound === false) {
        $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Checking for ticket.') . ' [attempt - ' . (int)$_POST['counter'] . ']';

        if ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PENDING) {
            $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Scheduling fetching.') . ' [attempt - ' . (int)$_POST['counter'] . ']';

            // Job should not take more than 10 sesconds before it starts
            // Reschedule if it's pending and ticket was not found
            if ((int)$_POST['counter'] % 5 === 0) {
                $scheduled = 1;
                $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
                erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('inst_id' => $inst_id, 'ignore_timeout' => true, 'mailbox_id' => $mailboxId));
                $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Re-scheduling fetching.') . ' [attempt - ' . (int)$_POST['counter'] . ']';
            }

        } elseif ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS && $scheduled == 0) {
            $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Waiting for previous job to finish.') . ' [attempt - ' . (int)$_POST['counter'] . ']';
        } elseif ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS && $scheduled == 1) {
            $subStatus = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Fetching in progress.') . ' [attempt - ' . (int)$_POST['counter'] . ']';
        }
    }

    $link = "<a target=\"_blank\" href=\"". erLhcoreClassDesign::baseurl('mailconv/conversations') . '/(message_id)/' . rawurldecode($messageId) . "/(mailbox_ids)/" . rawurldecode($mailboxId) ."\"><span class='material-icons'>open_in_new</span>Search query</a>";

    $template = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Working') . '. ' . $link . '. ' . $subStatus;

    echo json_encode(array('found' => false, 'scheduled' => $scheduled, 'progress' => $template),\JSON_INVALID_UTF8_IGNORE);
}

exit;
?>