<?php

namespace LiveHelperChat\mailConv\helpers;

class DownloadHelper
{
    public static function download($mail)
    {
        $mailbox = \erLhcoreClassModelMailconvMailbox::fetch($mail->mailbox_id);

        if ($mailbox->auth_method == \erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {

            try {
                $mailboxHandler = \LiveHelperChat\mailConv\OAuth\OAuth::getClient($mailbox);
                $mailboxFolderOAuth = $mailboxHandler->getFolderByPath($mail->mb_folder);

                if ($mailboxFolderOAuth !== null) {
                    $messagesCollection = $mailboxFolderOAuth->search()->whereUid($mail->uid)->get();
                    if ($messagesCollection->total() == 1) {
                        $email = $messagesCollection->shift();
                        $bodyRaw = "";
                        $bodyRaw .= json_decode(json_encode($email->getHeader()), true)['raw'];
                        $bodyRaw .= $email->getRawBody();
                    } else {
                        $bodyRaw = '';
                    }
                } else {
                    $bodyRaw = '';
                }

            } catch (\Exception $e) {
                $bodyRaw = '';
            }

        } else {
            $mailboxHandler = new \PhpImap\Mailbox(
                $mailbox->imap, // IMAP server incl. flags and optional mailbox folder
                $mailbox->username, // Username for the before configured mailbox
                $mailbox->password, // Password for the before configured username
                false
            );

            try {
                $bodyRaw = $mailboxHandler->getRawMail($mail->uid);
            } catch (\Exception $e) {
                $bodyRaw = '';
            }
        }

        // Construct manually *.eml file
        if (empty($bodyRaw)) {

            $mailReply = new \PHPMailer(true);
            $mailReply->CharSet = "UTF-8";
            $mailReply->Subject = $mail->subject;
            $mailReply->MessageDate = $mail->date;
            $mailReply->setFrom($mail->from_address, $mail->from_name);

            $toDataArray = $mail->to_data_array;

            if (empty($toDataArray)) {
                $mailReply->AddAddress($mailbox->mail, $mailbox->name);
            } else {
                foreach ($toDataArray as $mailData) {
                    $mailReply->AddAddress($mailData['email'], $mailData['name']);
                }
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
                $mailReply->IsHTML(false);
            } else {
                $mailReply->Body = \erLhcoreClassMailconvValidator::prepareMailContent($mail->body, $mailReply);
                $mailReply->isHTML(true);
                if (!empty($mail->alt_body) ) {
                    $mailReply->AltBody = $mail->alt_body;
                }
            }

            if (isset($mail->custom_headers) && is_array($mail->custom_headers)) {
                foreach ($mail->custom_headers as $header => $headerValue) {
                    $mailReply->addCustomHeader($header, $headerValue);
                }
            }

            $files = \erLhcoreClassModelMailconvFile::getList(['filter' => ['message_id' => $mail->id]]);

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

            return $mailReply->getSentMIMEMessage();
        } else {
            return $bodyRaw;
        }
    }
}
