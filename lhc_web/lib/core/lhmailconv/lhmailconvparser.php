<?php

include 'lib/vendor/autoload.php';

class erLhcoreClassMailconvParser {

    public static function getMailBox($mailbox) {

        $mail_con = imap_open($mailbox->imap, $mailbox->username,  $mailbox->password);
        $mailboxList = imap_list($mail_con, $mailbox->imap, '*');

        $mailboxPresentItems = $mailbox->mailbox_sync_array;

        foreach ($mailboxList as $mailboxItem) {
            $exists = false;
            foreach ($mailboxPresentItems as $mailboxPresentItem){
                if ($mailboxPresentItem['path'] == $mailboxItem){
                    $exists = true;
                }
            }

            if ($exists == false) {
                $mailboxPresentItems[] = ['sync' => false, 'path' => $mailboxItem];
            }
        }

        $mailbox->mailbox_sync_array = $mailboxPresentItems;
        $mailbox->mailbox_sync = json_encode($mailbox->mailbox_sync_array);
        $mailbox->saveThis();
    }

    public static function syncMailbox($mailbox) {

        $statsImport = array();

        try {

            $mailboxFolders = $mailbox->mailbox_sync_array;

            if (empty($mailboxFolders)) {
                throw new Exception('Please choose folder to sync first!');
            }

            $messages = [];

            // This mailbox is still in sync
            if ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS) {
                return;
            }

            // Sync has not passed required timeout
            if ($mailbox->last_sync_time > time() - $mailbox->sync_interval) {
                return;
            }

            $mailbox->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS;
            $mailbox->saveThis(['update' => ['sync_status']]);

            $filteredMatchingRules = array();
            $matchingRulesByMailbox = erLhcoreClassModelMailconvMatchRule::getList(['filter' => ['active' => 1]]);
            foreach ($matchingRulesByMailbox as $matchingRule) {
                if (in_array($mailbox->id,$matchingRule->mailbox_ids)) {
                    $filteredMatchingRules[] = $matchingRule;
                }
            }

            if (empty($filteredMatchingRules)) {
                throw new Exception('No mail matching rules were found!');
            }

            foreach ($mailboxFolders as $mailboxFolder)
            {

                // This folder is not synced
                if ($mailboxFolder['sync'] === false) {
                    continue;
                }

                $mailboxHandler = new PhpImap\Mailbox(
                    $mailboxFolder['path'], // IMAP server incl. flags and optional mailbox folder
                    $mailbox->username, // Username for the before configured mailbox
                    $mailbox->password, // Password for the before configured username
                    false
                );

                $mailsIds = $mailboxHandler->searchMailbox('SINCE "'.date('d M Y',($mailbox->last_sync_time > 0 ? $mailbox->last_sync_time : time()) - 24*3600).'"');

                if (empty($mailsIds)) {
                    continue;
                }

                $mailsInfo = $mailboxHandler->getMailsInfo($mailsIds);

                foreach ($mailsInfo as $mailInfo) {

                    $vars = get_object_vars($mailInfo);

                    $existingMail = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['message_id' => $vars['message_id']]));

                    // check that we don't have already this e-mail
                    if ($existingMail instanceof erLhcoreClassModelMailconvMessage) {
                        $messages[] = $existingMail;
                        $statsImport[] =  date('Y-m-d H:i:s').' | Skipping e-mail - ' . $vars['message_id'] . ' - ' . $vars['subject'];
                        continue;
                    }

                    // It's a new mail. Store it as new conversation.
                    if (!isset($mailInfo->in_reply_to)) {
                        $statsImport[] =  date('Y-m-d H:i:s').' | Importing - ' . $vars['message_id'] . ' - ' . $vars['subject'];

                        $message = new erLhcoreClassModelMailconvMessage();
                        $message->setState($vars);

                        $head = $mailboxHandler->getMailHeader($mailInfo->uid);

                        $message->from_host = $head->fromHost;
                        $message->from_name = $head->fromName;
                        $message->from_address = $head->fromAddress;

                        $message->sender_host = $head->senderHost;
                        $message->sender_name = $head->senderName;
                        $message->sender_address = $head->senderAddress;
                        $message->mailbox_id = $mailbox->id;

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

                        $matchingRuleSelected = self::getMatchingRuleByMessage($message, $filteredMatchingRules);

                        // Apply priority rule data
                        print_r($matchingRuleSelected);

                        if (!($matchingRuleSelected instanceof erLhcoreClassModelMailconvMatchRule)) {
                            throw new Exception('Matching rule could not be found!');
                        }

                        // Parse body
                        $mail = $mailboxHandler->getMail($mailInfo->uid, false);

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
                        $conversations->last_message_id = $conversations->message_id = $message->id;
                        $conversations->udate = $message->udate;
                        $conversations->date = $message->date;
                        $conversations->mailbox_id = $mailbox->id;
                        $conversations->match_rule_id = $matchingRuleSelected->id;
                        $conversations->saveThis();

                        $message->conversation_id = $conversations->id;
                        $message->updateThis(['update' => ['conversation_id']]);

                        $messages[] = $message;

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
                    // It's an reply
                    } else {

                        $conversation = null;

                        $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['message_id' => $vars['in_reply_to']]));

                        if ($previousMessage instanceof erLhcoreClassModelMailconvMessage && $previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation) {
                            $conversation = $previousMessage->conversation;
                        }

                        $message = self::importMessage($vars, $mailbox, $mailboxHandler, $conversation);

                        $messages[] = $message;

                        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->udate < $message->udate) {
                            self::setLastConversationByMessage($conversation, $message);
                        }
                        $statsImport[] = date('Y-m-d H:i:s').' | Importing reply - ' . $vars['message_id'] . ' - ' . $vars['subject'];
                    }
                }
            }
        } catch (Exception $e) {
            $statsImport[] = date('Y-m-d H:i:s').' | ' . $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine();
        }

        self::setConversations($messages);

        $mailbox->last_sync_time = time();
        $log = $mailbox->last_sync_log_array;
        array_unshift ($log, $statsImport);
        $log = array_slice($log,0,20);
        $mailbox->last_sync_log_array = $log;
        $mailbox->last_sync_log = json_encode($mailbox->last_sync_log_array);
        $mailbox->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PENDING;
        $mailbox->saveThis();
    }

    // Set conversations for the messages
    public static function setConversations($messages)
    {
        foreach ($messages as $message) {
            if ($message->conversation_id == 0) {
                self::setConversation($message);
            }
        }
    }

    public static function getMatchingRuleByMessage($message, $filteredMatchingRules) {

        foreach ($filteredMatchingRules as $matchingRule) {
            $matched = true;

            $from_mail_array = $matchingRule->from_mail_array;

            if (!empty($from_mail_array) && !in_array($message->from_address, $from_mail_array)) {
                $matched = false;
            }

            if (!empty($matchingRule->from_name)) {
                $fromNameRules = explode("\n",$matchingRule->from_name);
                $ruleFound = false;
                foreach ($fromNameRules as $fromNameRule)
                {
                    $mustCombinations = explode('&&',$fromNameRule);
                    $wordsFound = true;
                    foreach ($mustCombinations as $mustCombination) {
                        if (!erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$mustCombination),$message->from_name)) {
                            $wordsFound = false;
                            break;
                        }
                    }

                    if ($wordsFound == true) {
                        $ruleFound = true;
                    }
                }

                if ($ruleFound == false) {
                    $matched = false;
                }
            }

            if (!empty($matchingRule->subject_contains)) {
                $fromNameRules = explode("\n",$matchingRule->subject_contains);
                $ruleFound = false;
                foreach ($fromNameRules as $fromNameRule)
                {
                    $mustCombinations = explode('&&',$fromNameRule);
                    $wordsFound = true;
                    foreach ($mustCombinations as $mustCombination) {
                        if (!erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$mustCombination),$message->subject)) {
                            $wordsFound = false;
                            break;
                        }
                    }

                    if ($wordsFound == true) {
                        $ruleFound = true;
                    }
                }

                if ($ruleFound == false) {
                    $matched = false;
                }
            }

            if ($matched == true) {
                return $matchingRule;
            }
        }
    }

    public static function setConversation($message) {
        if ($message->in_reply_to != '') {
            $messageReply = erLhcoreClassModelMailconvMessage::findOne(['filter' => ['message_id' => $message->in_reply_to]]);
            if ($messageReply instanceof erLhcoreClassModelMailconvMessage)
            {
                if ($messageReply->conversation_id > 0) {
                    $message->conversation_id = $messageReply->conversation_id;
                    $message->saveThis(array('update' => array('conversation_id')));
                    self::setLastConversationByMessage($message->conversation, $message);
                    return $message->conversation_id;
                } else {
                    $conversationId = self::setConversation($messageReply);
                    if ($conversationId > 0) {
                        $message->conversation_id = $conversationId;
                        $message->saveThis(array('update' => array('conversation_id')));
                        self::setLastConversationByMessage($message->conversation, $message);
                        return $message->conversation_id;
                    }
                }
            }
        }
    }

    public static function setLastConversationByMessage($conversation, $message) {
        if ($conversation instanceof erLhcoreClassModelMailconvConversation &&
            $message instanceof erLhcoreClassModelMailconvMessage &&
            $message->udate > $conversation->udate
        ) {
            $conversation->body = $message->alt_body != '' ? $message->alt_body : strip_tags($message->body);
            $conversation->message_id = $message->id;
            $conversation->udate = $message->udate;
            $conversation->date = $message->date;
            $conversation->subject = $message->subject;
            $conversation->saveThis();
        }
    }

    public static function importMessage($mailInfo, $mailbox, $mailboxHandler, $conversation = null)
    {
        $message = new erLhcoreClassModelMailconvMessage();
        $message->setState($mailInfo);

        $head = $mailboxHandler->getMailHeader($mailInfo['uid']);

        $message->from_host = $head->fromHost;
        $message->from_name = $head->fromName;
        $message->from_address = $head->fromAddress;

        $message->sender_host = $head->senderHost;
        $message->sender_name = $head->senderName;
        $message->sender_address = $head->senderAddress;
        $message->mailbox_id = $mailbox->id;

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
        $mail = $mailboxHandler->getMail($mailInfo['uid'], false);

        if ($mail->textHtml) {
            $message->body = $mail->textHtml;
        }

        if ($mail->textPlain) {
            $message->alt_body = $mail->textPlain;
        }

        if ($conversation instanceof erLhcoreClassModelMailconvConversation) {
            $message->conversation_id = $conversation->id;
        }

        $message->saveThis();

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

        return $message;
    }
}

?>