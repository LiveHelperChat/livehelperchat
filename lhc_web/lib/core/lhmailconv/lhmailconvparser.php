<?php



include 'lib/vendor/autoload.php';

class erLhcoreClassMailconvParser {

    public static function syncMailbox($mailbox) {

        //$parser = new PhpMimeMailParser\Parser();

        $mailbox = new PhpImap\Mailbox(
            '{imap.gmail.com:993/imap/ssl}INBOX', // IMAP server incl. flags and optional mailbox folder
            'info@livehelperchat.com', // Username for the before configured mailbox
            'sdjalsd_dia4d54asdv', // Password for the before configured username
            false
        );

        $folders = $mailbox->getMailboxes('*');

        foreach ($folders as $folder) {
            $mailsIds = $mailbox->searchMailbox('SINCE "17 Jun 2020"');

            rsort($mailsIds);

            // Get the last 15 emails only
            array_splice($mailsIds, 15);

            /*foreach ($mailsIds as $mail){
                print_r($mailbox->getMail($mail));
            }*/

            $mailsInfo = $mailbox->getMailsInfo($mailsIds);

            foreach ($mailsInfo as $mailInfo) {

            // It's a new mail. Store it as new conversation.
            if (!isset($mailInfo->in_reply_to)) {

                $vars = get_object_vars($mailInfo);

                $message = new erLhcoreClassModelMailconvMessage();
                $message->setState($vars);

                $head = $mailbox->getMailHeader($mailInfo->uid);

                $message->from_host = $head->fromHost;
                $message->from_name = $head->fromName;
                $message->from_address = $head->fromAddress;

                $message->sender_host = $head->senderHost;
                $message->sender_name = $head->senderName;
                $message->sender_address = $head->senderAddress;

                if (isset($head->headers->to)) {
                    $message->toaddress = $head->headers->toaddress;
                    $message->to_data = json_encode($head->headers->to);
                }

                if (isset($head->headers->from)) {
                    $message->fromaddress = $head->headers->fromaddress;
                    $message->from_data = json_encode($head->headers->from);
                }

                if (isset($head->headers->reply_to)) {
                    $message->reply_toaddress = $head->headers->reply_toaddress;
                    $message->reply_to_data = json_encode($head->headers->reply_to);
                }

                if (isset($head->headers->sender)) {
                    $message->senderaddress = $head->headers->senderaddress;
                    $message->sender_data = json_encode($head->headers->sender);
                }

                // Parse body
                $mail = $mailbox->getMail($mailInfo->uid, false);

                if ($mail->textHtml) {
                    $message->body = $mail->textHtml;
                }

                if ($mail->textPlain) {
                    $message->alt_body = $mail->textPlain;
                }

                $message->saveThis();

                $conversations = new erLhcoreClassModelMailconvConversation();
                $conversations->dep_id = 0;
                $conversations->subject = $message->subject;
                $conversations->from_name = $message->from_name;
                $conversations->from_address = $message->from_address;
                $conversations->body = $message->alt_body != '' ? $message->alt_body : strip_tags($message->body);
                $conversations->last_message_id = $message->id;
                $conversations->saveThis();

                $message->conversation_id = $conversations->id;
                $message->updateThis(['update' => ['conversation_id']]);

                if ($mail->hasAttachments() == true) {
                    foreach ($mail->getAttachments() as $attachment) {
                        $mailAttatchement = new erLhcoreClassModelMailconvFile();
                        $mailAttatchement->message_id = $message->id;
                        $mailAttatchement->attachment_id = $attachment->id;
                        $mailAttatchement->content_id = (string)$attachment->contentId;
                        $mailAttatchement->disposition = (string)$attachment->disposition;
                        $mailAttatchement->size = $attachment->sizeInBytes;
                        $mailAttatchement->name = (string)$attachment->name;
                        $mailAttatchement->description = (string)$attachment->description;
                        $mailAttatchement->extension = (string)strtolower($attachment->subtype);
                        $mailAttatchement->type = (string)$attachment->mime;
                        $mailAttatchement->saveThis();
                    }
                }
            }

            }
        }

    }
}

?>