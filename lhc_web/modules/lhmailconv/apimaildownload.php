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

    header('Content-Disposition: attachment; filename="'.$mail->id.'.eml"');
    header('Content-type: text/plain');

    try {
        $bodyRaw = $mailboxHandler->getRawMail($mail->uid);
    } catch (Exception $e) {
        $bodyRaw = '';
    }

    // Construct manually *.eml file
    if (empty($bodyRaw)) {

        $mailReply = new PHPMailer(true);
        $mailReply->CharSet = "UTF-8";
        $mailReply->Subject = $mail->subject;
        $mailReply->MessageDate = $mail->date;
        $mailReply->setFrom($mail->from_address, $mail->from_name);

        foreach ($mail->to_data_array as $mailData) {
            $mailReply->AddAddress($mailData['email'], $mailData['name']);
        }

        foreach ($mail->reply_to_data_array as $mailData ) {
            $mailReply->AddReplyTo($mailData['email'], $mailData['name']);
        }

        foreach ($mail->bcc_data_array as $mailData ) {
            $mailReply->addBCC($mailData['email'], $mailData['name']);
        }

        foreach ($mail->cc_data_array as $mailData ) {
            $mailReply->addCC($mailData['email'], $mailData['name']);
        }

        if ($mail->in_reply_to != '') {
            $mailReply->addCustomHeader('In-Reply-To', $mail->in_reply_to);
        }

        if ($mail->references != '') {
            $mailReply->addCustomHeader('References', $mail->references);
        }

        if (empty($mail->body)) {
            $mailReply->Body = $mail->alt_body;
            $mailReply->ContentType = 'text/plain';
            $mailReply->IsHTML(false);
        } else {
            $mailReply->Body = erLhcoreClassMailconvValidator::prepareMailContent($mail->body, $mailReply);
            $mailReply->AltBody = $mail->alt_body;
        }

        if (isset($mail->custom_headers) && is_array($mail->custom_headers)) {
            foreach ($mail->custom_headers as $header => $headerValue) {
                $mailReply->addCustomHeader($header, $headerValue);
            }
        }

        $files = erLhcoreClassModelMailconvFile::getList(['filter' => ['message_id' => $mail->id]]);

        foreach ($files as $file) {
            if ($file->disposition == 'inline' && $file->content_id != '') {
                $mailReply->addEmbeddedImage($file->file_path_server, $file->content_id, $file->name);
            } else {
                $mailReply->addAttachment($file->file_path_server, $file->name);
            }
        }

        // Generate message_id upfront
        $mailReply->MessageID = $mail->message_id;

        $mailReply->preSend();

        echo $mailReply->getSentMIMEMessage();
    } else {
        echo $bodyRaw;
    }

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