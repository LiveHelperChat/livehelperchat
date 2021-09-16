<?php

include 'lib/vendor/autoload.php';

class erLhcoreClassMailconvParser {

    public static function getMailBox($mailbox) {

        $mail_con = imap_open($mailbox->imap, $mailbox->username,  $mailbox->password);

        if ($mail_con === false) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Connection could not be established. Please check your logins.'));
        }

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

    public static function syncMailbox($mailbox, $params = []) {

        $statsImport = array();

        $filteredMatchingRules = array();
        $matchingRulesByMailbox = erLhcoreClassModelMailconvMatchRule::getList(['filter' => ['active' => 1]]);
        foreach ($matchingRulesByMailbox as $matchingRule) {
            if (in_array($mailbox->id,$matchingRule->mailbox_ids)) {
                $filteredMatchingRules[] = $matchingRule;
            }
        }

        $messages = [];

        try {

            $mailboxFolders = $mailbox->mailbox_sync_array;

            if (empty($mailboxFolders)) {
                throw new Exception('Please choose folder to sync first!');
            }

            if (!($mailbox instanceof erLhcoreClassModelMailconvMailbox)) {
                throw new Exception('$mailbox argument should be instance of erLhcoreClassModelMailconvMailbox');
            }

            $db = ezcDbInstance::get();
            $db->beginTransaction();

            $mailbox = erLhcoreClassModelMailconvMailbox::fetchAndLock($mailbox->id);

            if (!isset($params['live']) || $params['live'] == false){
                // This mailbox is still in sync
                // Skip sync only if in progress and less than 10 minutes.
                if ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS && $mailbox->sync_started > 0 && (time() - $mailbox->sync_started) < 10 * 60 ) {
                    $db->commit();
                    return;
                }

                // Sync has not passed required timeout
                if ($mailbox->last_sync_time > time() - $mailbox->sync_interval) {
                    // Skip only if it's not in progress
                    if (!isset($params['ignore_timeout'])) {
                        $db->commit();
                        return;
                    }
                }
            }

            $mailbox->sync_started = time();
            $mailbox->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS;
            $mailbox->saveThis(['update' => ['sync_status','sync_started']]);

            $db->commit();

            if (empty($filteredMatchingRules)) {
                throw new Exception('No mail matching rules were found!');
            }

            $mailbox->failed = 0;

            foreach ($mailboxFolders as $mailboxFolder)
            {

                // This folder is not synced
                if ($mailboxFolder['sync'] === false) {
                    continue;
                }

                if (isset($params['only_send']) && $params['only_send'] == true && (!isset($mailboxFolder['send_folder']) || $mailboxFolder['send_folder'] === false)) {
                    continue;
                }

                $mailboxHandler = new PhpImap\Mailbox(
                    $mailboxFolder['path'], // IMAP server incl. flags and optional mailbox folder
                    $mailbox->username, // Username for the before configured mailbox
                    $mailbox->password, // Password for the before configured username
                    false
                );

                $uuidStatusArray = $mailbox->uuid_status_array;

                // We can survive this
                try {
                    $statusMailbox = $mailboxHandler->statusMailbox();
                    if (isset($uuidStatusArray[$mailboxFolder['path']]) && isset($statusMailbox->uidnext) && $statusMailbox->uidnext == $uuidStatusArray[$mailboxFolder['path']]) {
                        $statsImport[] = 'Skipping check '.$mailboxFolder['path'].' '.json_encode($statusMailbox);
                        // Nothing has changed since last check
                         continue;
                    } elseif (isset($statusMailbox->uidnext)) {
                        $statsImport[] = $mailboxFolder['path'].' uidnext change detected to - '.$statusMailbox->uidnext;
                        $uuidStatusArray[$mailboxFolder['path']] = $statusMailbox->uidnext;
                    }
                } catch (Exception $e) {
                    $statsImport[] = 'Failed getting message box status - ' . $e->getMessage();
                }

                $mailbox->uuid_status_array = $uuidStatusArray;
                $mailbox->uuid_status = json_encode($uuidStatusArray);

                // We disable server encoding because exchange servers does not support UTF-8 encoding in search.
                $mailsIds = $mailboxHandler->searchMailbox('SINCE "'.date('d M Y',($mailbox->last_sync_time > 0 ? $mailbox->last_sync_time : time()) - 2*24*3600).'"',true);

                if (empty($mailsIds)) {
                    continue;
                }

                $mailsInfo = $mailboxHandler->getMailsInfo($mailsIds);

                $db->reconnect();

                foreach ($mailsInfo as $mailInfo) {

                    $vars = get_object_vars($mailInfo);

                    if ($mailbox->import_since > 0 && $mailbox->import_since > (int)$vars['udate']) {
                        $statsImport[] = date('Y-m-d H:i:s').' | Skipping because of import since - ' . $vars['message_id'] . ' - ' . $mailInfo->uid;
                        continue;
                    }

                    $existingMail = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['mailbox_id' => $mailbox->id, 'message_id' => $vars['message_id']]));

                    // check that we don't have already this e-mail
                    if ($existingMail instanceof erLhcoreClassModelMailconvMessage) {
                        $messages[] = $existingMail;
                        $statsImport[] = date('Y-m-d H:i:s').' | Skipping e-mail - ' . $vars['message_id'] . ' - ' . $mailInfo->uid;
                        continue;
                    }

                    $head = $mailboxHandler->getMailHeader($mailInfo->uid);

                    $presentPriority = $mailbox->import_priority;

                    // Handle multiple TO's
                    if (isset($head->to)) {
                        foreach (array_keys($head->to) as $recipient) {
                            // Check is there any mailbox with higher priority
                            if ($mailbox->mail != $recipient && erLhcoreClassModelMailconvMailbox::getCount(array('filtergt' => array('import_priority' => $presentPriority), 'filter' => array('mail' => $recipient))) > 0) {
                                $statsImport[] = date('Y-m-d H:i:s').' | Skipping e-mail TO - ' . $vars['message_id'] . ' - because import priority is lower than - ' . $recipient . ' - ' . $mailInfo->uid;

                                // Skip this e-mail
                                continue 2;
                            }
                        }
                    }

                    // Handle multiple CC's
                    if (isset($head->cc)) {
                        foreach (array_keys($head->cc) as $recipient) {
                            // Check is there any mailbox with higher priority
                            if ($mailbox->mail != $recipient && erLhcoreClassModelMailconvMailbox::getCount(array('filtergt' => array('import_priority' => $presentPriority), 'filter' => array('mail' => $recipient))) > 0) {
                                $statsImport[] = date('Y-m-d H:i:s').' | Skipping e-mail CC - ' . $vars['message_id'] . ' - because import priority is lower than - ' . $recipient . ' - ' . $mailInfo->uid;

                                // Skip this e-mail
                                continue 2;
                            }
                        }
                    }

                    $followUpConversationId = 0;
                    $followUpUserId = 0;

                    // Create a new conversations if message is just to old
                    $newConversation = false;
                    if (isset($mailInfo->in_reply_to)) {
                        $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['mailbox_id' => $mailbox->id, 'message_id' => $vars['in_reply_to']]));
                        if (
                            $previousMessage instanceof erLhcoreClassModelMailconvMessage &&
                            $previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation &&
                            $mailbox->reopen_timeout > 0 && $previousMessage->conversation->lr_time > 0 &&
                            $previousMessage->conversation->lr_time < time() - $mailbox->reopen_timeout * 24 * 3600
                        ) {
                            $followUpConversationId = $previousMessage->conversation->id;

                            if ($mailbox->assign_parent_user == 1) {
                                $followUpUserId = $previousMessage->conversation->user_id;
                            }

                            $newConversation = true;
                        }
                    }

                    // It's a new mail. Store it as new conversation.
                    if (!isset($mailInfo->in_reply_to) || $newConversation == true) {
                        $statsImport[] =  date('Y-m-d H:i:s').' | Importing - ' . $vars['message_id'] .  ' - ' . $mailInfo->uid;

                        $message = new erLhcoreClassModelMailconvMessage();
                        $message->setState($vars);
                        $message->mb_folder = $mailboxFolder['path'];

                        $message->from_host = (string)$head->fromHost;
                        $message->from_name = erLhcoreClassMailconvEncoding::toUTF8((string)$head->fromName);
                        $message->from_address = (string)$head->fromAddress;

                        $message->sender_host = (string)$head->senderHost;
                        $message->sender_name = (string)$head->senderName;
                        $message->sender_address = (string)$head->senderAddress;
                        $message->mailbox_id = $mailbox->id;

                        // Perhaps it was initial message
                        $message->user_id = (\preg_match("/X-LHC-ID\:(.*)/i", $head->headersRaw, $matches)) ? (int)\trim($matches[1]) : 0;

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
                        $mail = $mailboxHandler->getMail($mailInfo->uid, false);

                        if ($mail->textHtml) {
                            $message->body = erLhcoreClassMailconvEncoding::toUTF8($mail->textHtml);
                        }

                        if ($mail->textPlain) {
                            $message->alt_body = erLhcoreClassMailconvEncoding::toUTF8($mail->textPlain);
                        }

                        $matchingRuleSelected = self::getMatchingRuleByMessage($message, $filteredMatchingRules);

                        if (!($matchingRuleSelected instanceof erLhcoreClassModelMailconvMatchRule)) {
                            $statsImport[] = 'No matching rule - Skipping e-mail - ' . $vars['message_id'] . ' - ' . $mailInfo->uid;
                            continue;
                        }

                        if ($mail->deliveryStatus) {
                            $message->delivery_status_array = self::parseDeliveryStatus($mail->deliveryStatus);
                            $message->delivery_status = json_encode($message->delivery_status_array);
                            $message->undelivered = 1;
                        }

                        if ($mail->RFC822) {
                            $message->rfc822_body = $mail->RFC822;
                        }

                        // Message was undelivered
                        // But there is returned message data
                        // So just extract this data from message
                        if ($message->user_id == 0 && $message->undelivered == 1 && !empty($message->rfc822_body)) {
                            $message->user_id = (\preg_match("/X-LHC-ID\:(.*)/i", $message->rfc822_body, $matches)) ? (int)\trim($matches[1]) : 0;
                            $head = \imap_rfc822_parse_headers($mail->RFC822);

                            if (isset($head->to)) {
                                foreach ($head->to as $to) {
                                    $to_parsed = $mailboxHandler->possiblyGetEmailAndNameFromRecipient($to);
                                    if ($to_parsed) {
                                        list($toEmail, $toName) = $to_parsed;
                                        // Switch message data to the e-mail whom we send it
                                        $message->from_name = erLhcoreClassMailconvEncoding::toUTF8((string)$toName);
                                        $message->from_address = (string)$toEmail;
                                        $message->reply_to_data = json_encode([$message->from_address => $message->from_name]);
                                    }
                                }
                            }

                            if (isset($head->Subject)) {
                                $message->subject = (string)erLhcoreClassMailconvEncoding::toUTF8($mailboxHandler->decodeMimeStr($head->Subject));
                            }

                            // @todo To what message reply it was
                            if (isset($head->in_reply_to) and !empty(\trim($head->in_reply_to))) {
                                $inReplyTo = $mailboxHandler->cleanReferences($head->in_reply_to);
                                $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['mailbox_id' => $mailbox->id, 'message_id' => $inReplyTo]));
                                if ($previousMessage instanceof erLhcoreClassModelMailconvMessage) {
                                    if ($previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation) {
                                        $followUpConversationId = $previousMessage->conversation->id;
                                        $message->user_id = $previousMessage->conversation->user_id;
                                    }
                                }
                            }
                        }

                        $message->saveThis();

                        $conversations = new erLhcoreClassModelMailconvConversation();
                        $conversations->dep_id = $matchingRuleSelected->dep_id;
                        $conversations->subject = erLhcoreClassMailconvEncoding::toUTF8((string)$message->subject);
                        $conversations->undelivered = $message->undelivered;

                        $conversations->from_name = erLhcoreClassMailconvEncoding::toUTF8((string)$message->from_name);
                        $conversations->from_address = $message->from_address;

                        $internalInit = false;
                        // set from address to recipient
                        if ($message->from_address == $mailbox->mail) {
                            $internalInit = true;
                            foreach ($message->to_data_array as $toData) {
                                $conversations->from_address = (string)$toData['email'];
                                $conversations->from_name = (string)$toData['name'];
                                break;
                            }
                        }

                        $conversations->body = erLhcoreClassMailconvEncoding::toUTF8($message->alt_body != '' ? $message->alt_body : strip_tags($message->body));
                        $conversations->last_message_id = $conversations->message_id = $message->id;
                        $conversations->udate = $message->udate;
                        $conversations->date = $message->date;
                        $conversations->mailbox_id = $mailbox->id;
                        $conversations->match_rule_id = $matchingRuleSelected->id;
                        $conversations->priority = $matchingRuleSelected->priority;
                        $conversations->total_messages = 1;
                        $conversations->pnd_time = time();
                        $conversations->user_id = $message->user_id;
                        $conversations->follow_up_id = $followUpConversationId;

                        if ($conversations->user_id == 0 && $followUpUserId > 0) {
                            $conversations->user_id = $followUpUserId;
                        }

                        // It was just a send e-mail. We can mark conversations as finished. Until someone replies back to us.
                        if ($internalInit == true) {
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

                        // Update attachment status
                        if ($message->has_attachment > $conversations->has_attachment) {
                            $conversations->has_attachment = $message->has_attachment;
                            $conversations->updateThis(['update' => ['has_attachment']]);
                        }

                        if (isset($matchingRuleSelected->options_array['close_conversation']) && $matchingRuleSelected->options_array['close_conversation'] == true) {
                            erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conversations]);
                        }

                        if ($conversations->start_type == erLhcoreClassModelMailconvConversation::START_IN && $conversations->status != erLhcoreClassModelMailconvConversation::STATUS_CLOSED) {
                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_started',array(
                                'mail' => & $message,
                                'conversation' => & $conversations
                            ));
                        }

                    // It's an reply
                    } else {

                        $conversation = null;

                        $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['mailbox_id' => $mailbox->id, 'message_id' => $vars['in_reply_to']]));

                        if ($previousMessage instanceof erLhcoreClassModelMailconvMessage && $previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation) {
                            $conversation = $previousMessage->conversation;
                        }

                        $message = self::importMessage($vars, $mailbox, $mailboxHandler, $conversation);

                        // Set folder from where message was taken;
                        $message->mb_folder = $mailboxFolder['path'];
                        $message->updateThis(['update' => ['mb_folder']]);

                        $messages[] = $message;

                        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->udate < $message->udate) {
                            $conversation->last_message_id = $message->id;
                            $conversation->conv_duration = erLhcoreClassChat::getCount(['filter' => ['conversation_id' => $conversation->id]],'lhc_mailconv_msg','SUM(conv_duration)');
                            $conversation->updateThis(['update' => ['last_message_id', 'conv_duration']]);
                            self::setLastConversationByMessage($conversation, $message);
                        }

                        // Update attachment status
                        if ($conversation instanceof erLhcoreClassModelMailconvConversation &&
                            $conversation->has_attachment != erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX &&
                            $message->has_attachment != erLhcoreClassModelMailconvMessage::ATTACHMENT_EMPTY
                        ) {
                            if (
                                ($message->has_attachment == erLhcoreClassModelMailconvMessage::ATTACHMENT_MIX) ||
                                (
                                    $conversation->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE &&
                                    $message->has_attachment == erLhcoreClassModelMailconvMessage::ATTACHMENT_FILE
                                ) ||
                                (
                                    $conversation->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE &&
                                    $message->has_attachment == erLhcoreClassModelMailconvMessage::ATTACHMENT_INLINE
                                )
                            ) {
                                $conversation->has_attachment = erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX;
                                $conversation->updateThis(['update' => ['has_attachment']]);
                            } elseif ($conversation->has_attachment == erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY) {
                                $conversation->has_attachment = $message->has_attachment;
                                $conversation->updateThis(['update' => ['has_attachment']]);
                            }
                        }

                        // If conversations is active we set accept time to import time
                        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->status == erLhcoreClassModelMailconvConversation::STATUS_ACTIVE) {
                            $message->accept_time = time();
                            $message->wait_time = $message->accept_time - $message->ctime;

                            if ($message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED) {
                                $message->status = erLhcoreClassModelMailconvMessage::STATUS_ACTIVE;
                            }

                            $message->saveThis(['update' => ['accept_time','status','wait_time']]);
                        }

                        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $message->response_type != erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL) {
                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_reply',array(
                                'mail' => & $message,
                                'conversation' => & $conversation
                            ));
                        }

                        $statsImport[] = date('Y-m-d H:i:s').' | Importing reply - ' . $vars['message_id'] . ' - ' .  $mailInfo->uid;
                   }
                }
            }
        } catch (Exception $e) {
            $statsImport[] = date('Y-m-d H:i:s').' | ' . $e->getMessage() . ' - ' . $e->getTraceAsString() . ' - ' . $e->getFile() . ' - ' . $e->getLine();
            $mailbox->failed = 1;
        }

        self::setConversations($messages);

        // We have to create a conversations for forwarded messages
        // Because they have in reply-to-header
        foreach ($messages as $message) {
            if ($message->conversation_id == 0) {

                $matchingRuleSelected = self::getMatchingRuleByMessage($message, $filteredMatchingRules);

                if (!($matchingRuleSelected instanceof erLhcoreClassModelMailconvMatchRule)) {
                    $statsImport[] = 'No matching rule - Skipping e-mail - ' . $message->message_id . ' - ' . $message->uid;
                    continue;
                }

                $conversations = new erLhcoreClassModelMailconvConversation();
                $conversations->dep_id = $matchingRuleSelected->dep_id;
                $conversations->subject = erLhcoreClassMailconvEncoding::toUTF8((string)$message->subject);
                $conversations->from_name = erLhcoreClassMailconvEncoding::toUTF8((string)$message->from_name);
                $conversations->from_address = $message->from_address;
                $conversations->body = erLhcoreClassMailconvEncoding::toUTF8($message->alt_body != '' ? $message->alt_body : strip_tags($message->body));
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

                if (isset($matchingRuleSelected->options_array['close_conversation']) && $matchingRuleSelected->options_array['close_conversation'] == true) {
                    erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conversations]);
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_started',array(
                    'mail' => & $message,
                    'conversation' => & $conversations
                ));
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
        $mailbox->updateThis(['update' => ['failed','uuid_status','sync_status','last_sync_log','last_sync_time']]);
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

    public static function saveAttatchements($mail, & $message) {

        $dispositions = [];

        foreach ($mail->getAttachments() as $attachment) {
            $mailAttatchement = new erLhcoreClassModelMailconvFile();
            $mailAttatchement->message_id = $message->id;
            $mailAttatchement->attachment_id = $attachment->id;
            $mailAttatchement->content_id = (string)$attachment->contentId;
            $mailAttatchement->disposition = (string)$attachment->disposition;
            $mailAttatchement->size = $attachment->sizeInBytes;
            $mailAttatchement->name = (string)$attachment->name;
            $mailAttatchement->description = (string)$attachment->description;
            $mailAttatchement->extension = mb_substr((string)strtolower($attachment->subtype),0,10);
            $mailAttatchement->type = (string)$attachment->mime;
            $mailAttatchement->conversation_id = $message->conversation_id;
            $mailAttatchement->saveThis();

            if (!in_array(strtolower($mailAttatchement->disposition),$dispositions)) {
                $dispositions[] = strtolower($mailAttatchement->disposition);
            }

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

        if (in_array('attachment',$dispositions) && in_array('inline',$dispositions)) {
            $message->has_attachment = erLhcoreClassModelMailconvMessage::ATTACHMENT_MIX;
        } elseif (in_array('attachment',$dispositions)) {
            $message->has_attachment = erLhcoreClassModelMailconvMessage::ATTACHMENT_FILE;
        } elseif (in_array('inline',$dispositions)) {
            $message->has_attachment = erLhcoreClassModelMailconvMessage::ATTACHMENT_INLINE;
        }

        if ($message->has_attachment > 0) {
            $message->updateThis(['update' => ['has_attachment']]);
        }
    }

    public static function parseDeliveryStatus($text) {

        $arr = preg_split('~([\w-]+: )~',$text,-1,PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

        for ($res = array(), $i = 0; $i < count($arr); $i+=2) {
            $key = strtr($arr[$i],array(': '=>'','-'=>'_'));
            $res[$key] = trim($arr[$i+1]);
        }
        return $res;
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

            // Check conditions
            $conditions = $matchingRule->conditions_array;
            if (!empty($conditions)) {
                // Same logic applies to matching rules and webhooks
                if (erLhcoreClassChatWebhookHttp::isValidConditions($matchingRule, $message) !== true) {
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

            // Do nothing as we will find ourself
            // And avoid infinitive loop
            if ($message->message_id == $message->in_reply_to) {
                return $message->conversation_id;
            }

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
                        if ($conversation instanceof erLhcoreClassModelMailconvConversation) {
                            $message->conversation_id = $conversation->id;
                            $message->dep_id = $conversation->dep_id;
                            $message->saveThis(array('update' => array('conversation_id','dep_id')));
                            self::setLastConversationByMessage($message->conversation, $message);
                            return $message->conversation_id;
                        }
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
            $conversation->udate = $message->udate;
            $conversation->date = $message->date;
            $conversation->subject = $message->subject;

            // We have to reopen conversation
            if ($conversation->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED && $message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED) {
                $conversation->pnd_time = time();
                $conversation->accept_time = 0;
                $conversation->tslasign = 0;
                // $conversation->user_id = 0;       // Keep operator so he can follow up
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

        $message->from_host = (string)$head->fromHost;
        $message->from_name = erLhcoreClassMailconvEncoding::toUTF8((string)$head->fromName);
        $message->from_address = erLhcoreClassMailconvEncoding::toUTF8((string)$head->fromAddress);

        $message->sender_host = (string)$head->senderHost;
        $message->sender_name = (string)$head->senderName;
        $message->sender_address = $head->senderAddress;
        $message->mailbox_id = $mailbox->id;

        // Find out what operator send this message if any
        $message->user_id = (\preg_match("/X-LHC-ID\:(.*)/i", $head->headersRaw, $matches)) ? (int)\trim($matches[1]) : 0;

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
            $message->body = erLhcoreClassMailconvEncoding::toUTF8((string)$mail->textHtml);
        }

        if ($mail->textPlain) {
            $message->alt_body = erLhcoreClassMailconvEncoding::toUTF8((string)$mail->textPlain);
        }

        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->id > 0) {
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

    public static function purgeMessage($message)
    {
        $mailbox = erLhcoreClassModelMailconvMailbox::fetch($message->mailbox_id);

        if ($mailbox->delete_mode == erLhcoreClassModelMailconvMailbox::DELETE_ALL) {
            $mailboxHandler = new PhpImap\Mailbox(
                $message->mb_folder, // We use message mailbox folder.
                $mailbox->username, // Username for the before configured mailbox
                $mailbox->password, // Password for the before configured username
                false
            );

            // Check that we have trash mailbox configured
            if ($mailbox->trash_mailbox != null) {
                $mailboxHandler->moveMail($message->uid,$mailbox->trash_mailbox);
                $mailboxHandler->expungeDeletedMails();
            }
        }
    }
}

?>