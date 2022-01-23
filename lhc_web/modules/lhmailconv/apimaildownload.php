<?php

include 'lib/vendor/autoload.php';

$mail = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

$mailbox = erLhcoreClassModelMailconvMailbox::fetch($mail->mailbox_id);

$mailboxHandler = new PhpImap\Mailbox(
    $mailbox->imap, // IMAP server incl. flags and optional mailbox folder
    $mailbox->username, // Username for the before configured mailbox
    $mailbox->password, // Password for the before configured username
    false
);

try {
    $bodyRaw = $mailboxHandler->getRawMail($mail->uid);

    header('Content-Disposition: attachment; filename="'.$mail->id.'.eml"');
    header('Content-type: text/plain');

    echo $bodyRaw;

    exit;

} catch (Exception $e) {

    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');

    $tpl->set('errors',[
        erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Message with specified ID could not be found anymore in provided IMAP folder'),
        htmlspecialchars($e->getMessage()),
        erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Message ID').' - '.$mail->uid.' '.$mail->message_id,
        $mail->mb_folder
    ]);

    $Result['content'] = $tpl->fetch();
}

?>