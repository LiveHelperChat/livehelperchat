<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */
/*
$message = erLhcoreClassModelMailconvMessage::fetch(221);
$conversation = erLhcoreClassModelMailconvConversation::fetch(189);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_started',array(
    'mail' => & $message,
    'conversation' => & $conversation
));*/
/*include 'lib/vendor/autoload.php';

$mailboxHandler = new PhpImap\Mailbox(
    'host', // IMAP server incl. flags and optional mailbox folder
    'mail', // Username for the before configured mailbox
    'pass',
    false
);

$mail = $mailboxHandler->getMail(23295, false);
echo erLhcoreClassMailconvEncoding::toUTF8($mail->textPlain),"\n";

$mail = $mailboxHandler->getMail(23325, false);
echo erLhcoreClassMailconvEncoding::toUTF8($mail->textPlain),"\n";*/

//echo mb_convert_encoding($mail->textPlain,'UTF-8','ISO-8859-1');




// php cron.php72 -s site_admin -c cron/test
$mailbox = erLhcoreClassModelMailconvMailbox::fetch(23);
erLhcoreClassMailconvParser::syncMailbox($mailbox, ['live' => true]);


?>