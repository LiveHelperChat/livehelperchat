<?php

session_write_close();

header ( 'content-type: application/json; charset=utf-8' );

$messageId = $_POST['message_id'];
$mailboxId = (int)$_POST['mailbox_id'];

if ($_POST['counter'] == 0) {
    $cfg = erConfigClassLhConfig::getInstance();
    $worker = $cfg->getSetting( 'webhooks', 'worker' );

    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
        // We should start this job ASAP it's queue is free
        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('ignore_timeout' => true, 'mailbox_id' => $mailboxId));
    } else {
        erLhcoreClassMailconvParser::syncMailbox(erLhcoreClassModelMailconvMailbox::fetch($mailboxId), ['live' => true, 'only_send' => true]);
    }
}

$message = erLhcoreClassModelMailconvMessage::findOne(array('filter' => array('message_id' => $messageId, 'mailbox_id' => $mailboxId)));

if ($message instanceof erLhcoreClassModelMailconvMessage) {
    $template = "<a target=\"_blank\" href=\"". erLhcoreClassDesign::baseurl('mailconv/view') . '/' . $message->conversation_id ."\"><span class='material-icons'>open_in_new</span>". $message->conversation_id. "</a>";
    echo json_encode(array('found' => true, 'conversation' => $template));
} else {
    echo json_encode(array('found' => false));
}

exit;
?>