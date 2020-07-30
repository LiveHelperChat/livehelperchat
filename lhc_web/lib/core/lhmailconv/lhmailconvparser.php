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

        $filteredMatchingRules = array();
        $matchingRulesByMailbox = erLhcoreClassModelMailconvMatchRule::getList(['filter' => ['active' => 1]]);
        foreach ($matchingRulesByMailbox as $matchingRule) {
            if (in_array($mailbox->id,$matchingRule->mailbox_ids)) {
                $filteredMatchingRules[] = $matchingRule;
            }
        }

        try {

            $mailboxFolders = $mailbox->mailbox_sync_array;

            if (empty($mailboxFolders)) {
                throw new Exception('Please choose folder to sync first!');
            }

            $messages = [];

            // This mailbox is still in sync
            /*if ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS) {
                return;
            }*/

            // Sync has not passed required timeout
            /*if ($mailbox->last_sync_time > time() - $mailbox->sync_interval) {
                return;
            }*/

            $mailbox->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS;
            $mailbox->saveThis(['update' => ['sync_status']]);

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

                $mailsIds = $mailboxHandler->searchMailbox('SINCE "'.date('d M Y',($mailbox->last_sync_time > 0 ? $mailbox->last_sync_time : time()) - 4*24*3600).'"');

                if (empty($mailsIds)) {
                    continue;
                }

                $mailsInfo = $mailboxHandler->getMailsInfo($mailsIds);

                foreach ($mailsInfo as $mailInfo) {

                    $vars = get_object_vars($mailInfo);

                    $existingMail = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['mailbox_id' => $mailbox->id, 'message_id' => $vars['message_id']]));

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
                        $message->from_name = (string)$head->fromName;
                        $message->from_address = $head->fromAddress;

                        $message->sender_host = $head->senderHost;
                        $message->sender_name = (string)$head->senderName;
                        $message->sender_address = $head->senderAddress;
                        $message->mailbox_id = $mailbox->id;

                        if (isset($head->to)) {
                            $message->to_data = json_encode($head->to);
                        }

                        if (isset($head->replyTo)) {
                            $message->reply_to_data = json_encode($head->replyTo);
                        }

                        if (isset($head->cc)) {
                            $message->cc_data = json_encode($head->cc);
                        }

                        if (isset($head->bcc)) {
                            $message->bcc_data = json_encode($head->bcc);
                        }

                        $matchingRuleSelected = self::getMatchingRuleByMessage($message, $filteredMatchingRules);

                        if (!($matchingRuleSelected instanceof erLhcoreClassModelMailconvMatchRule)) {
                            $statsImport[] = 'No matching rule - Skipping e-mail - ' . $vars['message_id'] . ' - ' . $vars['subject'];
                            continue;
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
                        $conversations->dep_id = $matchingRuleSelected->dep_id;
                        $conversations->subject = (string)$message->subject;
                        $conversations->from_name = (string)$message->from_name;
                        $conversations->from_address = $message->from_address;
                        $conversations->body = $message->alt_body != '' ? $message->alt_body : strip_tags($message->body);
                        $conversations->last_message_id = $conversations->message_id = $message->id;
                        $conversations->udate = $message->udate;
                        $conversations->date = $message->date;
                        $conversations->mailbox_id = $mailbox->id;
                        $conversations->match_rule_id = $matchingRuleSelected->id;
                        $conversations->priority = $matchingRuleSelected->priority;
                        $conversations->total_messages = 1;
                        $conversations->pnd_time = time();

                        // It was just a send e-mail. We can mark conversations as finished. Until someone replies back to us.
                        if ($conversations->from_address == $mailbox->mail) {
                            $conversations->status = erLhcoreClassModelMailconvConversation::STATUS_CLOSED;
                            $conversations->cls_time = time();
                            $conversations->start_type = erLhcoreClassModelMailconvConversation::START_OUT;

                            // It was just a send messages we can set all required attributes as this messages was processed
                            $message->response_type = erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL;
                            $message->status = erLhcoreClassModelMailconvMessage::STATUS_RESPONDED;
                            $message->lr_time = time();
                            $message->accept_time = time();
                            $message->cls_time = time();
                        }

                        $conversations->saveThis();

                        $message->conversation_id = $conversations->id;
                        $message->dep_id = $conversations->dep_id;
                        $message->updateThis(['update' => ['dep_id','conversation_id','response_type','status','lr_time','accept_time','cls_time']]);

                        $messages[] = $message;

                        if ($mail->hasAttachments() == true) {
                            self::saveAttatchements($mail, $message);
                        }
                    // It's an reply
                    } else {

                        $conversation = null;

                        $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['mailbox_id' => $mailbox->id, 'message_id' => $vars['in_reply_to']]));

                        if ($previousMessage instanceof erLhcoreClassModelMailconvMessage && $previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation) {
                            $conversation = $previousMessage->conversation;
                        }

                        $message = self::importMessage($vars, $mailbox, $mailboxHandler, $conversation);

                        $messages[] = $message;

                        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->udate < $message->udate) {
                            self::setLastConversationByMessage($conversation, $message);
                        }

                        // If conversations is active we set accept time to import time
                        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->status == erLhcoreClassModelMailconvConversation::STATUS_ACTIVE) {
                            $message->accept_time = time();
                            $message->wait_time = $message->accept_time - $message->ctime;

                            if ($message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED) {
                                $message->status = erLhcoreClassModelMailconvMessage::STATUS_ACTIVE;
                            }

                            $message->user_id = $conversation->user_id;
                            $message->saveThis(['update' => ['accept_time','status','wait_time','user_id']]);
                        }

                        $statsImport[] = date('Y-m-d H:i:s').' | Importing reply - ' . $vars['message_id'] . ' - ' . $vars['subject'];
                   }
                }
            }
        } catch (Exception $e) {
            $statsImport[] = date('Y-m-d H:i:s').' | ' . $e->getMessage() . ' - ' . $e->getTraceAsString() . ' - ' . $e->getFile() . ' - ' . $e->getLine();
        }

        self::setConversations($messages);

        // We have to create a conversations for forwarded messages
        // Because they have in reply-to-header
        foreach ($messages as $message) {
            if ($message->conversation_id == 0) {

                $matchingRuleSelected = self::getMatchingRuleByMessage($message, $filteredMatchingRules);

                if (!($matchingRuleSelected instanceof erLhcoreClassModelMailconvMatchRule)) {
                    $statsImport[] = 'No matching rule - Skipping e-mail - ' . $vars['message_id'] . ' - ' . $vars['subject'];
                    continue;
                }

                $conversations = new erLhcoreClassModelMailconvConversation();
                $conversations->dep_id = $matchingRuleSelected->dep_id;
                $conversations->subject = (string)$message->subject;
                $conversations->from_name = (string)$message->from_name;
                $conversations->from_address = $message->from_address;
                $conversations->body = $message->alt_body != '' ? $message->alt_body : strip_tags($message->body);
                $conversations->last_message_id = $conversations->message_id = $message->id;
                $conversations->udate = $message->udate;
                $conversations->date = $message->date;
                $conversations->mailbox_id = $mailbox->id;
                $conversations->match_rule_id = $matchingRuleSelected->id;
                $conversations->priority = $matchingRuleSelected->priority;
                $conversations->total_messages = 1;
                $conversations->pnd_time = time();
                $conversations->saveThis();

                // Assign conversation
                $message->conversation_id = $conversations->id;
                $message->dep_id = $conversations->dep_id;
                $message->updateThis(['update' => ['conversation_id','dep_id']]);
            }
        }

        // We did not found any conversation for particular message
        foreach ($messages as $message) {
            if ($message->conversation_id == 0) {
                $message->removeThis();
            }
        }

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

    public static function saveAttatchements($mail, $message) {
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
            $mailAttatchement->conversation_id = $message->conversation_id;
            $mailAttatchement->saveThis();

            $fileBody = $attachment->getContents();

            $dir = 'var/tmpfiles/';
            $fileName = md5($mailAttatchement->id . '_' . $mailAttatchement->name . '_' . $mailAttatchement->attachment_id);

            $cfg = erConfigClassLhConfig::getInstance();

            $defaultGroup = $cfg->getSetting( 'site', 'default_group', false );
            $defaultUser = $cfg->getSetting( 'site', 'default_user', false );

            erLhcoreClassFileUpload::mkdirRecursive( $dir, true, $defaultUser, $defaultGroup);

            $localFile = $dir . $fileName;
            file_put_contents($localFile, $fileBody);

            $dir = 'var/storagemail/' . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $mailAttatchement->id . '/';

            erLhcoreClassFileUpload::mkdirRecursive( $dir, true, $defaultUser, $defaultGroup);

            rename($localFile, $dir . $fileName);
            chmod($dir . $fileName, 0644);

            if ($defaultUser != '') {;
                chown($dir, $defaultUser);
            }

            if ($defaultGroup != '') {
                chgrp($dir, $defaultGroup);
            }

            $mailAttatchement->file_name = $fileName;
            $mailAttatchement->file_path = $dir;
            $mailAttatchement->saveThis();
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
                    $message->dep_id = $messageReply->dep_id;
                    $message->saveThis(array('update' => array('conversation_id','dep_id')));
                    self::setLastConversationByMessage($message->conversation, $message);
                    return $message->conversation_id;
                } else {
                    $conversationId = self::setConversation($messageReply);
                    if ($conversationId > 0) {
                        $conversation = erLhcoreClassModelMailconvConversation::fetch($conversationId);
                        $message->conversation_id = $conversation->conversation_id;
                        $message->dep_id = $conversation->dep_id;
                        $message->saveThis(array('update' => array('conversation_id','dep_id')));
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

            // We have to reopen conversation
            if ($conversation->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED && $message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED) {
                $conversation->pnd_time = time();
                $conversation->accept_time = 0;
                $conversation->tslasign = 0;
                $conversation->user_id = 0;         // Reset operator
                $conversation->cls_time = 0;        // Reset close time
                $conversation->status = erLhcoreClassModelMailconvConversation::STATUS_PENDING;
            }

            $conversation->saveThis();
        }
    }

    public static function importMessage($mailInfo, $mailbox, $mailboxHandler, $conversation = null)
    {
        $message = new erLhcoreClassModelMailconvMessage();
        $message->setState($mailInfo);

        $head = $mailboxHandler->getMailHeader($mailInfo['uid']);

        $message->from_host = $head->fromHost;
        $message->from_name = (string)$head->fromName;
        $message->from_address = $head->fromAddress;

        $message->sender_host = $head->senderHost;
        $message->sender_name = (string)$head->senderName;
        $message->sender_address = $head->senderAddress;
        $message->mailbox_id = $mailbox->id;

        if (isset($head->to)) {
            $message->to_data = json_encode($head->to);
        }

        if (isset($head->replyTo)) {
            $message->reply_to_data = json_encode($head->replyTo);
        }

        if (isset($head->cc)) {
            $message->cc_data = json_encode($head->cc);
        }

        if (isset($head->bcc)) {
            $message->bcc_data = json_encode($head->bcc);
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
            $message->dep_id = $conversation->dep_id;
        }

        if ($message->from_address == $mailbox->mail) {
            // It was just a send messages we can set all required attributes as this messages was processed
            $message->response_type = erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL;
            $message->status = erLhcoreClassModelMailconvMessage::STATUS_RESPONDED;
            $message->lr_time = time();
            $message->accept_time = time();
            $message->cls_time = time();
        }

        $message->saveThis();

        if ($mail->hasAttachments() == true) {
              self::saveAttatchements($mail, $message);
        }

        return $message;
    }
}

?>