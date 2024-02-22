<?php

class erLhcoreClassMailconvParser {

    public static function getMailBox($mailbox) {

        if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
            \LiveHelperChat\mailConv\OAuth\OAuth::setupFolder($mailbox);
            return;
        }

        $mail_con = imap_open($mailbox->imap, $mailbox->username,  $mailbox->password);

        if ($mail_con === false) {
            throw new Exception(
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Connection could not be established. Please check your logins.') . ' ' . imap_last_error()
            );
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

    public static function convertToRawIMAP($mail) {

        $converted = new stdClass();
        $converted->message_id = '<' . $mail->get('message_id') . '>';
        $converted->udate = $mail->get('date')->toDate()->timestamp;

        if ($mail->get('in_reply_to')->count() > 0) {
            $converted->in_reply_to = '<'.$mail->get('in_reply_to').'>'; // @todo test
        }

        if ($mail->get('references')->count() > 0) {
            $referenceArray = [];
            foreach ($mail->get('references')->toArray() as $reference) { // @todo test
                $referenceArray[] = '<'.$reference.'>';
            }
            $converted->references = implode(' ', $referenceArray); // @todo test
        }

        $converted->uid = (int)$mail->getUid();
        $converted->head = $mail->header;
        $converted->subject = (string)$mail->getSubject();
        try {
            $converted->msgno = (int)$mail->getMsgn(); // Check why it's gone
        } catch (Exception $e) {
            $converted->msgno = 0;
        }

        $converted->size = strlen(json_decode(json_encode($mail->getHeader()), true)['raw'] .  $mail->getRawBody());

        return $converted;
    }

    public static function syncMailbox($mailbox, $params = []) {

        if ($mailbox->active == 0) {
            return;
        }

        $statsImport = array();

        $filteredMatchingRules = array();
        foreach (erLhcoreClassModelMailconvMatchRule::getList(['filternot' => ['dep_id' => 0], 'filter' => ['active' => 1]]) as $matchingRule) {
            if (in_array($mailbox->id,$matchingRule->mailbox_ids)) {
                $filteredMatchingRules[] = $matchingRule;
            }
        }

        $filteredPriorityMatchingRules = array();
        foreach (erLhcoreClassModelMailconvMatchRule::getList(['filter' => ['dep_id' => 0, 'active' => 1]]) as $matchingRule) {
            if (in_array($mailbox->id,$matchingRule->mailbox_ids)) {
                $filteredPriorityMatchingRules[] = $matchingRule;
            }
        }

        $messages = [];

        $db = ezcDbInstance::get();

        try {

            $mailboxFolders = $mailbox->mailbox_sync_array;

            if (empty($mailboxFolders)) {
                throw new Exception('Please choose folder to sync first!');
            }

            if (!($mailbox instanceof erLhcoreClassModelMailconvMailbox)) {
                throw new Exception('$mailbox argument should be instance of erLhcoreClassModelMailconvMailbox');
            }

            $db->beginTransaction();

            $mailbox = erLhcoreClassModelMailconvMailbox::fetchAndLock($mailbox->id);

            if (!isset($params['live']) || $params['live'] == false){
                // This mailbox is still in sync
                // Skip sync only if in progress and less than 10 minutes.
                if ($mailbox->sync_status == erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS && $mailbox->sync_started > 0 && (time() - $mailbox->sync_started) < 80 * 60 ) {
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
                throw new Exception('No mail matching rules were found! ['.$mailbox->id.']');
            }

            $mailbox->failed = 0;

            // Do so first is checked send folder
            usort($mailboxFolders, function ($a, $b) {
                return !(isset($a['send_folder']) && $a['send_folder'] == 1) ? 1 : 0;
            });
            
            $startTime = microtime();
            $workflowOptions = $mailbox->workflow_options_array;
            $last_process_time = $mailbox->last_process_time;

            foreach ($mailboxFolders as $mailboxFolder)
            {

                // This folder is not synced
                if ($mailboxFolder['sync'] === false) {
                    continue;
                }

                if (isset($params['only_send']) && $params['only_send'] == true && (!isset($mailboxFolder['send_folder']) || $mailboxFolder['send_folder'] === false)) {
                    continue;
                }

                if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                    $mailboxHandler = \LiveHelperChat\mailConv\OAuth\OAuth::getClient($mailbox);
                    $mailboxFolderOAuth = $mailboxHandler->getFolderByPath($mailboxFolder['path']);

                    // We use some of the functions
                    $mailboxHandlerHelper = new PhpImap\Mailbox(
                        $mailboxFolder['path'], // IMAP server incl. flags and optional mailbox folder
                        $mailbox->username, // Username for the before configured mailbox
                        $mailbox->password, // Password for the before configured username
                        false
                    );

                } else {
                    $mailboxHandlerHelper = $mailboxHandler = new PhpImap\Mailbox(
                        $mailboxFolder['path'], // IMAP server incl. flags and optional mailbox folder
                        $mailbox->username, // Username for the before configured mailbox
                        $mailbox->password, // Password for the before configured username
                        false
                    );
                }

                $uuidStatusArray = $mailbox->uuid_status_array;

                // We can survive this
                try {

                    if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                        $statusMailbox = json_decode(json_encode($mailboxFolderOAuth->getStatus()),false);
                    } else {
                        $statusMailbox = $mailboxHandler->statusMailbox();
                    }

                    if (isset($uuidStatusArray[$mailboxFolder['path']]) && isset($statusMailbox->uidnext) && $statusMailbox->uidnext == $uuidStatusArray[$mailboxFolder['path']]) {
                         if (isset($workflowOptions['workflow_reimport_frequency']) && $workflowOptions['workflow_reimport_frequency'] > 0 && $last_process_time < (time() - ($workflowOptions['workflow_reimport_frequency'] * 60))) {
                             $statsImport[] = 'Processing check because re-import frequency was met '.$mailboxFolder['path'].' '.json_encode($statusMailbox);
                         } else {
                             $statsImport[] = 'Skipping check uidnext did not changed '.$mailboxFolder['path'].' '.json_encode($statusMailbox);
                             // Nothing has changed since last check
                             continue;
                         }
                    } elseif (isset($statusMailbox->uidnext)) {
                        $statsImport[] = $mailboxFolder['path'].' uidnext change detected to - '.$statusMailbox->uidnext;
                        $uuidStatusArray[$mailboxFolder['path']] = $statusMailbox->uidnext;
                    }
                } catch (Exception $e) {
                    $statsImport[] = 'Failed getting message box status - ' . $e->getMessage();
                }

                $mailbox->uuid_status_array = $uuidStatusArray;
                $mailbox->uuid_status = json_encode($uuidStatusArray);
                $mailbox->last_process_time = time();

                $since = 'SINCE "' . date('d M Y',
                        (!(isset($workflowOptions['workflow_import_present']) && $workflowOptions['workflow_import_present'] == 1) && $mailbox->last_sync_time > 0 ? $mailbox->last_sync_time : time()) -
                        (isset($workflowOptions['workflow_older_than']) && is_numeric($workflowOptions['workflow_older_than']) && $workflowOptions['workflow_older_than'] > 0 ? ((int)$workflowOptions['workflow_older_than'] * 3600) : (2*24*3600))).'"';

                $sinceOAUTH = date('d.m.Y',
                        (!(isset($workflowOptions['workflow_import_present']) && $workflowOptions['workflow_import_present'] == 1) && $mailbox->last_sync_time > 0 ? $mailbox->last_sync_time : time()) -
                        (isset($workflowOptions['workflow_older_than']) && is_numeric($workflowOptions['workflow_older_than']) && $workflowOptions['workflow_older_than'] > 0 ? ((int)$workflowOptions['workflow_older_than'] * 3600) : (2*24*3600)));

                $statsImport[] = 'Search started at '.date('Y-m-d H:i:s') . ' data range - '.$since;

                if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                    $mailsInfo = $mailboxFolderOAuth->search()->since($sinceOAUTH)->get();

                    // We disable server encoding because exchange servers does not support UTF-8 encoding in search.

                   // $mailsInfo = $mailboxFolderOAuth->search()->whereUid(2198477)->get();

                    if (empty($mailsInfo)) {
                        continue;
                    }

                } else {
                    // We disable server encoding because exchange servers does not support UTF-8 encoding in search.
                    $mailsIds = $mailboxHandler->searchMailbox($since, true);

                    if (empty($mailsIds)) {
                        continue;
                    }

                    $mailsInfo = $mailboxHandler->getMailsInfo($mailsIds);
                }

                $statsImport[] = 'Search finished at '.date('Y-m-d H:i:s');


                $statsImport[] = 'Fetching mail info finished '.date('Y-m-d H:i:s');

                $db->reconnect();

                foreach ($mailsInfo as $mailInfoRaw) {

                    if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                        $mailInfo = self::convertToRawIMAP($mailInfoRaw);
                    } else {
                        $mailInfo = $mailInfoRaw;
                    }

                    $start = explode(' ', $startTime);
                    $end = explode(' ', microtime());
                    $time = $end[0] + $end[1] - $start[0] - $start[1];

                    if ($time > (79 * 60)) {
                        throw new Exception('Import takes too long time.' . date('Y-m-d H:i:s',$mailbox->sync_started) . ' - ' . date('Y-m-d H:i:s',time()));
                    }

                    $vars = get_object_vars($mailInfo);

                    if (!isset($vars['message_id'])) {
                        continue;
                    }

                    // Some messages id are longer than 250
                    $vars['message_id'] = mb_substr($vars['message_id'],0,250);

                    if (isset($vars['in_reply_to'])) {
                        $vars['in_reply_to'] = mb_substr($vars['in_reply_to'],0,250);
                    }

                    if ($mailbox->import_since > 0 && $mailbox->import_since > (int)$vars['udate']) {
                        $statsImport[] = date('Y-m-d H:i:s').' | Skipping because of import since - ' . $vars['message_id'] . ' - ' . $mailInfo->uid;
                        continue;
                    }

                    $existingMail = erLhcoreClassModelMailconvMessage::findOne(array('filterin' => ['mailbox_id' => $mailbox->relevant_mailbox_id], 'filter' => ['message_id' => $vars['message_id']]));

                    // check that we don't have already this e-mail
                    if ($existingMail instanceof erLhcoreClassModelMailconvMessage) {
                        $messages[] = $existingMail;
                        $statsImport[] = date('Y-m-d H:i:s').' | Skipping e-mail because record found - ' . $vars['message_id'] . ' - ' . $mailInfo->uid;
                        continue;
                    }

                    $existingMail = null;

                    if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                        $head = $mailInfo->head;
                        if ($head->subject != '') {
                            $existingMail = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['subject' => (string)erLhcoreClassMailconvEncoding::toUTF8($head->subject), 'message_id' => $vars['message_id']]));
                        }
                    } else {
                        $head = $mailboxHandler->getMailHeader($mailInfo->uid);
                        if (isset($head->Subject)) {
                            $existingMail = erLhcoreClassModelMailconvMessage::findOne(array('filter' => ['subject' => (string)erLhcoreClassMailconvEncoding::toUTF8($mailboxHandler->decodeMimeStr($head->Subject)), 'message_id' => $vars['message_id']]));
                        }
                    }

                    if ($existingMail instanceof erLhcoreClassModelMailconvMessage) {
                        $messages[] = $existingMail;
                        $statsImport[] = date('Y-m-d H:i:s').' | Skipping e-mail because of same message_id and subject - ' . $vars['message_id'] . ' - ' . $mailInfo->uid;
                        continue;
                    }

                    $presentPriority = $mailbox->import_priority;

                    if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                        if ($head->to !== null) {
                            foreach ($head->to->toArray() as $recipient) {
                                if ($mailbox->mail != $recipient->mail && erLhcoreClassModelMailconvMailbox::getCount(array('filtergt' => array('import_priority' => $presentPriority), 'filter' => array('mail' => $recipient->mail))) > 0) {
                                    $statsImport[] = date('Y-m-d H:i:s').' | Skipping e-mail TO - ' . $vars['message_id'] . ' - because import priority is lower than - ' . $recipient->mail . ' - ' . $mailInfo->uid;

                                    // Skip this e-mail
                                    continue 2;
                                }
                            }
                        }
                    } elseif (isset($head->to)) {
                        foreach (array_keys($head->to) as $recipient) {
                            // Check is there any mailbox with higher priority
                            if ($mailbox->mail != $recipient && erLhcoreClassModelMailconvMailbox::getCount(array('filtergt' => array('import_priority' => $presentPriority), 'filter' => array('mail' => $recipient))) > 0) {
                                $statsImport[] = date('Y-m-d H:i:s').' | Skipping e-mail TO - ' . $vars['message_id'] . ' - because import priority is lower than - ' . $recipient . ' - ' . $mailInfo->uid;

                                // Skip this e-mail
                                continue 2;
                            }
                        }
                    }

                    if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                        if ($head->cc !== null) {
                            foreach ($head->cc->toArray() as $recipient) {
                                // Check is there any mailbox with higher priority
                                if ($mailbox->mail != $recipient->mail && erLhcoreClassModelMailconvMailbox::getCount(array('filtergt' => array('import_priority' => $presentPriority), 'filter' => array('mail' => $recipient->mail))) > 0) {
                                    $statsImport[] = date('Y-m-d H:i:s').' | Skipping e-mail CC - ' . $vars['message_id'] . ' - because import priority is lower than - ' . $recipient->mail . ' - ' . $mailInfo->uid;

                                    // Skip this e-mail
                                    continue 2;
                                }
                            }
                        }
                    } else if (isset($head->cc)) { // Handle multiple CC's
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

                    $logImport = [];

                    // Create a new conversations if message is just to old
                    $newConversation = false;
                    if (isset($mailInfo->in_reply_to)) {
                        $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filterin' => ['mailbox_id' => $mailbox->relevant_mailbox_id],'filter' => ['message_id' => $vars['in_reply_to']]));
                        if (
                            !($previousMessage instanceof erLhcoreClassModelMailconvMessage) &&
                            isset($vars['references']) && !empty($vars['references']) &&
                            !(isset($workflowOptions['workflow_use_in_reply']) && $workflowOptions['workflow_use_in_reply'] == 1)
                        ) {
                            $matches = [];
                            preg_match_all('/\<(.*?)\>/', $vars['references'],$matches);
                            $relatedMessagesIds = [];
                            if (isset($matches[0])) {
                                foreach ($matches[0] as $messageId) {
                                    if (trim($messageId) != '' && trim($vars['in_reply_to']) != trim($messageId)) {
                                        $relatedMessagesIds[] = str_replace(' ','',$messageId);
                                    }
                                }
                                if (!empty($relatedMessagesIds)) {
                                    $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('sort' => '`id` DESC','filterin' => ['mailbox_id' => $mailbox->relevant_mailbox_id, 'message_id' => $relatedMessagesIds]));
                                }
                            }
                        }

                        if (
                            $previousMessage instanceof erLhcoreClassModelMailconvMessage &&
                            $previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation &&
                            $mailbox->reopen_timeout > 0 && $previousMessage->conversation->lr_time > 0 &&
                            $previousMessage->conversation->lr_time < time() - $mailbox->reopen_timeout * 24 * 3600
                        ) {
                            $followUpConversationId = $previousMessage->conversation->id;

                            if ($mailbox->assign_parent_user == 1) {
                                $followUpUserId = $previousMessage->conversation->user_id;
                                $logImport[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Previous mail owner').' [' .$followUpUserId . ']';
                            } else {
                                $logImport[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Assigning previous mail owner is disabled for the mailbox');
                            }

                            $newConversation = true;
                        }
                    }

                    $attributeToUse = 'headersRaw';

                    if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                        $attributeToUse = 'raw';
                    }

                    // Ignore, this is set while using bot and sending auto reply. Checked - Do not import send e-mail.
                    if (\preg_match("/X-LHC-IGN\:(.*)/i", $head->{$attributeToUse}, $matches)) {
                        continue;
                    }

                    // It's a new mail. Store it as new conversation.
                    if (!isset($mailInfo->in_reply_to) || $newConversation == true) {

                        $message = new erLhcoreClassModelMailconvMessage();

                        $message->setState($vars);
                        $message->mb_folder = $mailboxFolder['path'];

                        if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {

                            $message->from_host = (string)$head->from->first()->host;
                            $message->from_name = mb_substr($head->from->first()->personal,0,250);
                            $message->from_address = mb_substr($head->from->first()->mail,0,250);

                            $message->sender_host = $head->sender->first()->host;
                            $message->sender_name = mb_substr($head->sender->first()->personal,0,250);
                            $message->sender_address = $head->sender->first()->mail;

                        } else {
                            $message->from_host = (string)$head->fromHost;
                            $message->from_name = mb_substr(erLhcoreClassMailconvEncoding::toUTF8((string)$head->fromName),0,250);
                            $message->from_address = mb_substr((string)$head->fromAddress,0,250);

                            $message->sender_host = (string)$head->senderHost;
                            $message->sender_name = mb_substr(erLhcoreClassMailconvEncoding::toUTF8((string)$head->senderName),0,250);
                            $message->sender_address = (string)$head->senderAddress;
                        }

                        $message->mailbox_id = $mailbox->id;

                        // Perhaps it was initial message
                        $message->user_id = (\preg_match("/X-LHC-ID\:(.*)/i", $head->{$attributeToUse}, $matches)) ? (int)\trim($matches[1]) : 0;

                        $recipient_id = (\preg_match("/X-LHC-RCP\:(.*)/i", $head->{$attributeToUse}, $matches)) ? (int)\trim($matches[1]) : 0;


                        if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {

                            $attributesDirect = [
                                'to' => 'to_data',
                                'reply_to' => 'reply_to_data',
                                'cc' => 'cc_data',
                                'bcc' => 'bcc_data',
                            ];

                            foreach ($attributesDirect as $key => $objAttribute) {
                                if ($head->get($key) !== null) {
                                    $dataItems = [];
                                    foreach ($head->get($key)->toArray() as $dataItem) {
                                        $dataItems[$dataItem->mail] = (string)$dataItem->personal;
                                    }

                                    if (!empty($dataItems)) {
                                        $message->{$objAttribute} = json_encode($dataItems);
                                    }
                                }
                            }

                        } else {
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
                        }

                        if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {

                            if ($mailInfoRaw->hasHTMLBody()) {
                                $message->body = self::cleanupMailBody($mailInfoRaw->getHTMLBody());
                            }

                            $message->alt_body = $mailInfoRaw->getTextBody();

                            // Same object
                            $mail = $mailInfoRaw;

                        } else {
                            // Parse body
                            $mail = $mailboxHandler->getMail($mailInfo->uid, false);

                            if ($mail->textHtml) {
                                $message->body = self::cleanupMailBody(erLhcoreClassMailconvEncoding::toUTF8($mail->textHtml));
                            }

                            if ($mail->textPlain) {
                                $message->alt_body = erLhcoreClassMailconvEncoding::toUTF8($mail->textPlain);
                            }
                        }

                        $message->headers_raw_array = erLhcoreClassMailconvParser::parseDeliveryStatus(preg_replace('/([\w-]+:\r\n)/i','',$head->{$attributeToUse}));

                        $matchingRuleSelected = self::getMatchingRuleByMessage($message, $filteredMatchingRules);

                        if (!($matchingRuleSelected instanceof erLhcoreClassModelMailconvMatchRule)) {
                            $statsImport[] = 'No matching rule - Skipping e-mail - ' . $vars['message_id'] . ' - ' . $mailInfo->uid;
                            continue;
                        }

                        $priorityConversation = $matchingRuleSelected->priority;

                        // Rule without department has higher priority
                        $matchingPriorityRuleSelected = self::getMatchingRuleByMessage($message, $filteredPriorityMatchingRules);
                        if ($matchingPriorityRuleSelected instanceof erLhcoreClassModelMailconvMatchRule && $matchingPriorityRuleSelected->priority > $priorityConversation) {
                            $priorityConversation = $matchingPriorityRuleSelected->priority;
                        }

                        if (
                            (isset($matchingRuleSelected->options_array['skip_message']) && $matchingRuleSelected->options_array['skip_message'] == true) ||
                            ($matchingPriorityRuleSelected instanceof erLhcoreClassModelMailconvMatchRule && isset($matchingPriorityRuleSelected->options_array['skip_message']) && $matchingPriorityRuleSelected->options_array['skip_message'] == true)
                        ) {
                            $statsImport[] = 'Skipping e-mail because of matching rule - ' . $vars['message_id'] . ' - ' . $mailInfo->uid;
                            continue;
                        }

                        $rfc822RawBody = '';
                        if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                            foreach ($mailInfoRaw->getStructure()->find_parts() as $part) {
                                if ($part->subtype == 'delivery-status') {
                                    $message->undelivered = 1;
                                    $message->delivery_status_array = self::parseDeliveryStatus($part->content);
                                    $message->delivery_status = json_encode($message->delivery_status_array);
                                } elseif ($part->subtype == 'rfc822') {
                                    $rfc822RawBody = $message->rfc822_body = trim($part->raw);
                                }
                            }
                        } else {
                            if ($mail->deliveryStatus) {
                                $message->delivery_status_array = self::parseDeliveryStatus($mail->deliveryStatus);
                                $message->delivery_status = json_encode($message->delivery_status_array);
                                $message->undelivered = 1;
                            }

                            if ($mail->RFC822) {
                                $message->rfc822_body = erLhcoreClassMailconvEncoding::toUTF8($mail->RFC822);
                                $rfc822RawBody = $mail->RFC822;
                            }
                        }

                        if ($message->undelivered != 1 && $message->rfc822_body != '') {
                            $rfc822RawBody = $message->rfc822_body = '';
                        }

                        // Message was undelivered
                        // But there is returned message data
                        // So just extract this data from message
                        if ($message->user_id == 0 && $message->undelivered == 1 && !empty($message->rfc822_body)) {
                            $message->user_id = (\preg_match("/X-LHC-ID\:(.*)/i", $message->rfc822_body, $matches)) ? (int)\trim($matches[1]) : 0;
                            $head = \imap_rfc822_parse_headers($rfc822RawBody);

                            // Set recipient_id if it exists
                            $recipient_id = (\preg_match("/X-LHC-RCP\:(.*)/i", $message->rfc822_body, $matches)) ? (int)\trim($matches[1]) : $recipient_id;

                            if (isset($head->to)) {
                                foreach ($head->to as $to) {
                                    $to_parsed = $mailboxHandlerHelper->possiblyGetEmailAndNameFromRecipient($to);
                                    if ($to_parsed) {
                                        list($toEmail, $toName) = $to_parsed;
                                        // Switch message data to the e-mail whom we send it
                                        $message->from_name = mb_substr(erLhcoreClassMailconvEncoding::toUTF8((string)$toName),0,250);
                                        $message->from_address = mb_substr((string)$toEmail,0,250);
                                        $message->reply_to_data = json_encode([$message->from_address => $message->from_name]);
                                    }
                                }
                            }

                            if (isset($head->Subject)) {
                                $message->subject = (string)erLhcoreClassMailconvEncoding::toUTF8($mailboxHandlerHelper->decodeMimeStr($head->Subject));
                            }

                            if (isset($head->in_reply_to) and !empty(\trim($head->in_reply_to))) {
                                $inReplyTo = mb_substr($mailboxHandlerHelper->cleanReferences($head->in_reply_to),0,250);
                                $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filterin' => ['mailbox_id' => $mailbox->relevant_mailbox_id], 'filter' => ['message_id' => $inReplyTo]));
                                if ($previousMessage instanceof erLhcoreClassModelMailconvMessage) {
                                    if ($previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation) {
                                        $followUpConversationId = $previousMessage->conversation->id;
                                        $message->user_id = $previousMessage->conversation->user_id;
                                    }
                                }
                            }

                            if (isset($head->message_id) && !empty(\trim($head->message_id)) && $followUpConversationId == 0) {
                                $inReplyTo = mb_substr($mailboxHandlerHelper->cleanReferences($head->message_id),0,250);
                                $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filterin' => ['mailbox_id' => $mailbox->relevant_mailbox_id],'filter' => ['message_id' => $inReplyTo]));
                                if ($previousMessage instanceof erLhcoreClassModelMailconvMessage) {
                                    if ($previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation) {
                                        $followUpConversationId = $previousMessage->conversation->id;
                                        $message->user_id = $previousMessage->conversation->user_id;
                                    }
                                }
                            }
                        }

                        try {
                            $message->saveThis();
                        } catch (\ezcPersistentQueryException $e) { // Handle incorrect encoding for body/alt_body
                            if (strpos($e->getMessage(),'SQLSTATE[22007]') !== false && strpos($e->getMessage(),'`lhc_mailconv_msg`.`alt_body`') !== false) {
                                $message->alt_body = '';
                                try {
                                    $message->saveThis();
                                } catch (\ezcPersistentQueryException $e) {
                                    $message->body = '';
                                    $message->saveThis();
                                }
                            } elseif (strpos($e->getMessage(),'SQLSTATE[22007]') !== false && strpos($e->getMessage(),'`lhc_mailconv_msg`.`body`') !== false) {
                                $message->body = '';
                                try {
                                    $message->saveThis();
                                } catch (\ezcPersistentQueryException $e) {
                                    $message->alt_body = '';
                                    $message->saveThis();
                                }
                            } else {
                                throw $e;
                            }
                        }

                        $conversations = new erLhcoreClassModelMailconvConversation();
                        $conversations->dep_id = $matchingRuleSelected->dep_id;
                        $conversations->subject = erLhcoreClassMailconvEncoding::toUTF8((string)$message->subject);
                        $conversations->undelivered = $message->undelivered;

                        $conversations->from_name = mb_substr(erLhcoreClassMailconvEncoding::toUTF8((string)$message->from_name),0,250);
                        $conversations->from_address = mb_substr($message->from_address,0,250);

                        $internalInit = false;
                        // set from address to recipient
                        if ($message->from_address == $mailbox->mail) {
                            $internalInit = true;
                            foreach ($message->to_data_array as $toData) {
                                $conversations->from_address = mb_substr((string)$toData['email'],0,250);
                                $conversations->from_name = mb_substr((string)$toData['name'],0,250);
                                break;
                            }
                        } else {
                            $message->is_external = 1;
                        }

                        $conversations->body = erLhcoreClassMailconvEncoding::toUTF8($message->alt_body != '' ? $message->alt_body : strip_tags($message->body));
                        $conversations->last_message_id = $conversations->message_id = $message->id;
                        $conversations->udate = $message->udate;
                        $conversations->date = mb_substr($message->date,0,250);
                        $conversations->mailbox_id = $mailbox->id;
                        $conversations->match_rule_id = $matchingRuleSelected->id;
                        $conversations->priority = $priorityConversation;
                        $conversations->total_messages = 1;
                        $conversations->pnd_time = time();
                        $conversations->user_id = $message->user_id;
                        $conversations->follow_up_id = $followUpConversationId;

                        if ($conversations->user_id == 0 && $followUpUserId > 0) {
                            $conversations->user_id = $followUpUserId;
                            $logImport[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Conversation user id was set by previous conversation user.') . ' [' . $followUpUserId . ']';
                        }

                        if ($conversations->user_id == 0 && $mailbox->user_id > 0) {
                            $conversations->user_id = $mailbox->user_id;
                        }

                        // It was just a send e-mail. We can mark conversations as finished. Until someone replies back to us.
                        if ($internalInit == true) {

                            // Operator send a message as closed
                            $statusMessage = (\preg_match("/X-LHC-ST\:(.*)/i", $head->{$attributeToUse}, $matches)) ? (int)\trim($matches[1]) : 0;

                            $conversations->status = $statusMessage == erLhcoreClassModelMailconvMessage::STATUS_ACTIVE ? erLhcoreClassModelMailconvMessage::STATUS_ACTIVE : erLhcoreClassModelMailconvConversation::STATUS_CLOSED;

                            if ($conversations->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED) {
                                $conversations->cls_time = time();
                            }

                            $conversations->start_type = erLhcoreClassModelMailconvConversation::START_OUT;

                            // It was just a send messages we can set all required attributes as this messages was processed
                            $message->response_type = erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL;
                            $message->status = erLhcoreClassModelMailconvMessage::STATUS_RESPONDED;
                            $message->lr_time = time();
                            $message->accept_time = time();
                            $message->cls_time = time();
                        }

                        if ($conversations->user_id > 0 && erLhcoreClassModelUser::getCount(['filter' => ['id' => $conversations->user_id, 'disabled' => 1]]) == 1) {
                            $conversations->user_id = 0;
                        }

                        $conversations->saveThis();

                        $message->conv_user_id = $conversations->user_id;
                        $message->priority = $priorityConversation;
                        $message->conversation_id = $conversations->id;
                        $message->dep_id = $conversations->dep_id;
                        $message->updateThis(['update' =>  ['dep_id','conversation_id','response_type','status','lr_time','accept_time','cls_time','is_external','conv_user_id']]);

                        // Save initial message
                        if (!empty($logImport)) {
                            $messageLog = new erLhcoreClassModelMailconvMessageInternal();
                            $messageLog->msg = implode("\n",$logImport);
                            $messageLog->user_id = -1;
                            $messageLog->chat_id = $conversations->id;
                            $messageLog->time = time();
                            $messageLog->saveThis();
                        }

                        $messages[] = $message;

                        if ($mail->hasAttachments() == true) {
                            self::saveAttatchements($mail, $message, $mailbox);
                        }

                        // Update attachment status
                        if ($message->has_attachment > $conversations->has_attachment) {
                            $conversations->has_attachment = $message->has_attachment;
                            $conversations->updateThis(['update' => ['has_attachment']]);
                        }

                        if (
                            (isset($matchingRuleSelected->options_array['close_conversation']) && $matchingRuleSelected->options_array['close_conversation'] == true) ||
                            ($matchingPriorityRuleSelected instanceof erLhcoreClassModelMailconvMatchRule && isset($matchingPriorityRuleSelected->options_array['close_conversation']) && $matchingPriorityRuleSelected->options_array['close_conversation'] == true)
                        ) {
                            erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conversations]);
                        }

                        if ($message->undelivered == 0 && $conversations->start_type == erLhcoreClassModelMailconvConversation::START_IN && $conversations->status != erLhcoreClassModelMailconvConversation::STATUS_CLOSED) {
                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_started',array(
                                'mail' => & $message,
                                'conversation' => & $conversations
                            ));
                        } else {
                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_started_passive',array(
                                'mail' => & $message,
                                'conversation' => & $conversations
                            ));
                        }

                        if ($recipient_id > 0) {
                            $recipient = erLhcoreClassModelMailconvMailingCampaignRecipient::fetch($recipient_id);
                            if ($recipient instanceof erLhcoreClassModelMailconvMailingCampaignRecipient) {
                                $recipient->message_id = (int)$message->id;
                                $recipient->conversation_id = (int)$conversations->id;
                                $recipient->updateThis(['update' => ['message_id','conversation_id']]);
                            }
                        }

                        \LiveHelperChat\mailConv\workers\LangWorker::detectLanguage($message);

                    // It's an reply
                    } else {

                        $conversation = null;

                        $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('filterin' => ['mailbox_id' => $mailbox->relevant_mailbox_id],'filter' => ['message_id' => $vars['in_reply_to']]));

                        if ($previousMessage instanceof erLhcoreClassModelMailconvMessage && $previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation) {
                            $conversation = $previousMessage->conversation;
                        } else if (isset($vars['references']) && !empty($vars['references']) && !(isset($workflowOptions['workflow_use_in_reply']) && $workflowOptions['workflow_use_in_reply'] == 1)) { // Handle auto responder logic when it's not imported.
                            $matches = [];
                            preg_match_all('/\<(.*?)\>/',$vars['references'],$matches);
                            $relatedMessagesIds = [];
                            if (isset($matches[0])) {
                                foreach ($matches[0] as $messageId) {
                                    if (trim($messageId) != '' && trim($vars['in_reply_to']) != trim($messageId)) {
                                        $relatedMessagesIds[] = str_replace(' ','',$messageId);
                                    }
                                }

                                if (!empty($relatedMessagesIds)) {
                                    $previousMessage = erLhcoreClassModelMailconvMessage::findOne(array('sort' => '`id` DESC','filterin' => ['mailbox_id' => $mailbox->relevant_mailbox_id, 'message_id' => $relatedMessagesIds]));
                                    if ($previousMessage instanceof erLhcoreClassModelMailconvMessage && $previousMessage->conversation instanceof erLhcoreClassModelMailconvConversation) {
                                        $conversation = $previousMessage->conversation;
                                    }
                                }
                            }
                        }

                        $message = self::importMessage($vars, $mailbox, $mailboxHandler, $conversation, $head, $mailInfoRaw);

                        $rfc822RawBody = '';
                        
                        if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                            foreach ($mailInfoRaw->getStructure()->find_parts() as $part) {
                                if ($part->subtype == 'delivery-status') {

                                    $message->undelivered = 1;
                                    $message->delivery_status_array = self::parseDeliveryStatus($part->content);
                                    $message->delivery_status = json_encode($message->delivery_status_array);
                                    $message->updateThis(['update' => ['undelivered','delivery_status']]);

                                    if ($conversation instanceof erLhcoreClassModelMailconvConversation){
                                        $conversation->undelivered = 1;
                                        $conversation->updateThis(['update' => ['undelivered']]);
                                    }

                                } elseif ($part->subtype == 'rfc822') {
                                    $rfc822RawBody = $message->rfc822_body = trim($part->raw);
                                    $message->updateThis(['update' => ['rfc822_body']]);
                                }
                            }
                        }

                        if ($message->undelivered != 1 && $message->rfc822_body != '') {
                            $rfc822RawBody = $message->rfc822_body = '';
                            $message->updateThis(['update' => ['rfc822_body']]);
                        }

                        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->user_id > 0 && erLhcoreClassModelUser::getCount(['filter' => ['id' => $conversation->user_id, 'disabled' => 1]]) == 1) {
                            $conversation->user_id = 0;
                            $conversation->updateThis(['update' => ['user_id']]);
                        }

                        // Set folder from where message was taken;
                        $message->conv_user_id = $conversation->user_id;
                        $message->mb_folder = $mailboxFolder['path'];
                        $message->updateThis(['update' => ['mb_folder','conv_user_id']]);

                        $messages[] = $message;

                        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->udate < $message->udate) {
                            $conversation->pending_sync = 0;
                            $conversation->last_message_id = $message->id;
                            $conversation->conv_duration = erLhcoreClassChat::getCount(['filter' => ['conversation_id' => $conversation->id]],'lhc_mailconv_msg','SUM(conv_duration)');
                            $conversation->updateThis(['update' => ['last_message_id', 'conv_duration', 'pending_sync']]);
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
                        } elseif ($conversation instanceof erLhcoreClassModelMailconvConversation) {
                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.conversation_reply_passive',array(
                                'mail' => & $message,
                                'conversation' => & $conversation
                            ));
                        }

                        \LiveHelperChat\mailConv\workers\LangWorker::detectLanguage($message);

                        $statsImport[] = date('Y-m-d H:i:s').' | Importing reply - ' . $vars['message_id'] . ' - ' .  $mailInfo->uid;
                   }
                }
            }
        } catch (Exception $e) {

            $statsImport[] = date('Y-m-d H:i:s').' | ' . $e->getMessage() . ' - ' . $e->getTraceAsString() . ' - ' . $e->getFile() . ' - ' . $e->getLine();

            try {
                $db->reconnect();

                \erLhcoreClassLog::write(json_encode($statsImport, JSON_PRETTY_PRINT),
                    \ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'lhc',
                        'category' => 'mail_import_failure',
                        'line' => __LINE__,
                        'file' => __FILE__,
                        'object_id' => $mailbox->id
                    )
                );

            } catch (Exception $e) {
                // Ignore
            }

            $mailbox->failed = 1;
        }

        $db->reconnect();

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

                $priorityConversation = $matchingRuleSelected->priority;

                $matchingPriorityRuleSelected = self::getMatchingRuleByMessage($message, $filteredPriorityMatchingRules);
                if ($matchingPriorityRuleSelected instanceof erLhcoreClassModelMailconvMatchRule && $matchingPriorityRuleSelected->priority > $priorityConversation) {
                    $priorityConversation = $matchingPriorityRuleSelected->priority;
                }

                if (
                    (isset($matchingRuleSelected->options_array['skip_message']) && $matchingRuleSelected->options_array['skip_message'] == true) ||
                    ($matchingPriorityRuleSelected instanceof erLhcoreClassModelMailconvMatchRule && isset($matchingPriorityRuleSelected->options_array['skip_message']) && $matchingPriorityRuleSelected->options_array['skip_message'] == true)
                ) {
                    $statsImport[] = 'Skipping e-mail because of matching rule - ' . $message->message_id . ' - ' . $message->uid;
                    continue;
                }

                $conversations = new erLhcoreClassModelMailconvConversation();
                $conversations->dep_id = $matchingRuleSelected->dep_id;
                $conversations->subject = erLhcoreClassMailconvEncoding::toUTF8((string)$message->subject);
                $conversations->from_name = mb_substr(erLhcoreClassMailconvEncoding::toUTF8((string)$message->from_name),0,250);
                $conversations->from_address = mb_substr($message->from_address,0,250);
                $conversations->body = erLhcoreClassMailconvEncoding::toUTF8($message->alt_body != '' ? $message->alt_body : strip_tags($message->body));
                $conversations->last_message_id = $conversations->message_id = $message->id;
                $conversations->udate = $message->udate;
                $conversations->date = mb_substr($message->date,0,250);
                $conversations->mailbox_id = $mailbox->id;
                $conversations->match_rule_id = $matchingRuleSelected->id;
                $conversations->priority = $priorityConversation;
                $conversations->total_messages = 1;
                $conversations->pnd_time = time();
                $conversations->lang = $message->lang;
                $conversations->undelivered = $message->undelivered;
                $conversations->has_attachment = $message->has_attachment;
                $conversations->saveThis();

                // Assign conversation
                $message->priority = $priorityConversation;
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
            } else {
                // Track mail open status
                if ($message->opened_at == 0 && $message->message_hash != '') {
                    $openedMessage = erLhcoreClassModelMailconvMessageOpen::findOne(['filter' => ['hash' => $message->message_hash]]);
                    if ($openedMessage instanceof erLhcoreClassModelMailconvMessageOpen) {

                        $campaignRecipient = erLhcoreClassModelMailconvMailingCampaignRecipient::findOne(['filter' => ['message_id' => $message->id]]);

                        if ($campaignRecipient instanceof erLhcoreClassModelMailconvMailingCampaignRecipient) {
                            $campaignRecipient->opened_at = $openedMessage->opened_at;
                            $campaignRecipient->updateThis(['update' => ['opened_at']]);
                        }

                        $message->opened_at = $openedMessage->opened_at;
                        $message->updateThis(['update' => ['opened_at']]);
                        if ($message->conversation instanceof erLhcoreClassModelMailconvConversation && $message->conversation->opened_at == 0) {
                            $message->conversation->opened_at = time();
                            $message->conversation->updateThis(['update' => ['opened_at']]);
                        }
                    }
                }
            }
        }


        $db->reconnect();

        $mailbox->last_sync_time = time();
        $log = $mailbox->last_sync_log_array;
        array_unshift ($log, $statsImport);
        $log = array_slice($log,0,5);
        $mailbox->last_sync_log_array = $log;
        $mailbox->last_sync_log = json_encode($mailbox->last_sync_log_array);
        $mailbox->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PENDING;
        $mailbox->updateThis(['update' => ['failed','uuid_status','sync_status','last_sync_log','last_sync_time','last_process_time']]);
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

    public static function saveAttatchements($mail, & $message, $mailbox) {

        $dispositions = [];

        foreach ($mail->getAttachments() as $attachmentRaw) {

            if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {

                $attributesOAuth['disposition'] = (string)$attachmentRaw->getDisposition();
                $attributesOAuth['name'] = (string)$attachmentRaw->getName();
                $attributesOAuth['description'] = (string)$attachmentRaw->getName();
                $attributesOAuth['sizeInBytes'] = (int)$attachmentRaw->getSize();
                $attributesOAuth['contentId'] = (string)$attachmentRaw->getId();
                $attributesOAuth['mime'] = (string)$attachmentRaw->getMimeType();
                $attributesOAuth['subtype'] = (string)$attachmentRaw->getExtension();
                $attributesOAuth['id'] = md5(microtime() . $attachmentRaw->getId() . $attachmentRaw->getName() . $attachmentRaw->getSize());

                $fileBody = $attachmentRaw->getContent();

                if ($attachmentRaw->getContentType() == 'message/rfc822' && $attributesOAuth['name'] == 'undefined' || $attributesOAuth['name'] == '') {

                    $head = \imap_rfc822_parse_headers($fileBody);

                    $attributesOAuth['subtype'] = 'eml';

                    if (isset($head->Subject)) {
                        $mailboxHandlerHelper = new PhpImap\Mailbox(
                            'INBOX', // IMAP server incl. flags and optional mailbox folder
                            $mailbox->username, // Username for the before configured mailbox
                            $mailbox->password, // Password for the before configured username
                            false
                        );
                        $attributesOAuth['name'] = (string)erLhcoreClassMailconvEncoding::toUTF8($mailboxHandlerHelper->decodeMimeStr($head->Subject)) . '.eml';
                    } else {
                        $attributesOAuth['name'] = 'ForwardedMessage.eml';
                    }

                } else {
                    if ($attributesOAuth['subtype'] == '') {
                        $extension = \erLhcoreClassChatWebhookIncoming::getExtensionByMime($attributesOAuth['mime']);
                        if ($extension !== false) {
                            $attributesOAuth['subtype'] = $extension;
                        }
                    }
                }

                $attachment = json_decode(json_encode($attributesOAuth)); // Just convert to object

            } else {
                $attachment = $attachmentRaw;
            }

            if ((int)$attachment->sizeInBytes == 0) {
                continue;
            }

            $mailAttatchement = new erLhcoreClassModelMailconvFile();
            $mailAttatchement->message_id = $message->id;
            $mailAttatchement->attachment_id = $attachment->id;
            $mailAttatchement->content_id = (string)$attachment->contentId;
            $mailAttatchement->disposition = (string)$attachment->disposition;
            $mailAttatchement->size = (int)$attachment->sizeInBytes;
            $mailAttatchement->name = mb_substr((string)$attachment->name,-250);
            $mailAttatchement->description = (string)$attachment->description;
            $mailAttatchement->extension = mb_substr((string)strtolower($attachment->subtype),0,10);
            $mailAttatchement->type = (string)$attachment->mime;
            $mailAttatchement->conversation_id = $message->conversation_id;
            $mailAttatchement->saveThis();

            if (!in_array(strtolower($mailAttatchement->disposition),$dispositions)) {
                $dispositions[] = strtolower($mailAttatchement->disposition);
            }

            if ($mailbox->auth_method != erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                $fileBody = $attachment->getContents();
            }

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
            if (isset($arr[$i+1])) {
                $res[$key] = trim($arr[$i+1]);
            }
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
                        if (!erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$mustCombination),$message->from_name,0)) {
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
                        if (!erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$mustCombination),$message->subject,0)) {
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
            $conversation->date = mb_substr($message->date,0,250);
            $conversation->subject = $message->subject;
            $conversation->undelivered = $message->undelivered;

            // We have to reopen conversation
            if ($conversation->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED && $message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED) {
                $conversation->pnd_time = time();
                $conversation->accept_time = 0;
                $conversation->tslasign = 0;

                $mailbox = erLhcoreClassModelMailconvMailbox::fetch($message->mailbox_id);

                if ($mailbox instanceof erLhcoreClassModelMailconvMailbox && $mailbox->reopen_reset == 1) {
                    $conversation->user_id = 0;
                }

                $conversation->cls_time = 0;        // Reset close time
                $conversation->status = erLhcoreClassModelMailconvConversation::STATUS_PENDING;
            }

            $conversation->saveThis();
        }
    }

    public static function importMessage($mailInfo, $mailbox, $mailboxHandler, $conversation, $head, $mailInfoRaw)
    {
        // @todo migrate to oauth flow
        $message = new erLhcoreClassModelMailconvMessage();
        $message->setState($mailInfo);

        $attributeToUse = 'headersRaw';

        if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {

            $message->from_host = (string)$head->from->first()->host;
            $message->from_name = mb_substr($head->from->first()->personal,0,250);
            $message->from_address = mb_substr($head->from->first()->mail,0,250);

            $message->sender_host = $head->sender->first()->host;
            $message->sender_name =  mb_substr($head->sender->first()->personal,0,250);
            $message->sender_address = $head->sender->first()->mail;

            $attributeToUse = 'raw';
        } else {
            $message->from_host = (string)$head->fromHost;
            $message->from_name = mb_substr(erLhcoreClassMailconvEncoding::toUTF8((string)$head->fromName), 0, 250);
            $message->from_address = mb_substr(erLhcoreClassMailconvEncoding::toUTF8((string)$head->fromAddress), 0, 250);

            $message->sender_host = (string)$head->senderHost;
            $message->sender_name = mb_substr(erLhcoreClassMailconvEncoding::toUTF8((string)$head->senderName),0,250);
            $message->sender_address = $head->senderAddress;
        }

        $message->mailbox_id = $mailbox->id;

        // Find out what operator send this message if any
        $message->user_id = (\preg_match("/X-LHC-ID\:(.*)/i", $head->{$attributeToUse}, $matches)) ? (int)\trim($matches[1]) : 0;

        if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {

            $attributesDirect = [
                'to' => 'to_data',
                'reply_to' => 'reply_to_data',
                'cc' => 'cc_data',
                'bcc' => 'bcc_data',
            ];

            foreach ($attributesDirect as $key => $objAttribute) {
                if ($head->get($key) !== null) {
                    $dataItems = [];
                    foreach ($head->get($key)->toArray() as $dataItem) {
                        $dataItems[$dataItem->mail] = (string)$dataItem->personal;
                    }

                    if (!empty($dataItems)) {
                        $message->{$objAttribute} = json_encode($dataItems);
                    }
                }
            }

            if ($mailInfoRaw->hasHTMLBody()) {
                $message->body = self::cleanupMailBody($mailInfoRaw->getHTMLBody());
            }

            $message->alt_body = $mailInfoRaw->getTextBody();

            $mail = $mailInfoRaw;

        } else {

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
                $message->body = self::cleanupMailBody(erLhcoreClassMailconvEncoding::toUTF8((string)$mail->textHtml));
            }

            if ($mail->textPlain) {
                $message->alt_body = erLhcoreClassMailconvEncoding::toUTF8((string)$mail->textPlain);
            }
        }

        if ($conversation instanceof erLhcoreClassModelMailconvConversation && $conversation->id > 0) {
            $message->conversation_id = $conversation->id;
            $message->dep_id = $conversation->dep_id;
            $message->conv_user_id = $conversation->user_id;
        }

        if ($message->from_address == $mailbox->mail) {
            // It was just a send messages we can set all required attributes as this messages was processed
            $message->response_type = erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL;
            $message->status = erLhcoreClassModelMailconvMessage::STATUS_RESPONDED;
            $message->lr_time = time();
            $message->accept_time = time();
            $message->cls_time = time();
        } else {
            $message->is_external = 1;
        }

        try {
            $message->saveThis();
        } catch (\ezcPersistentQueryException $e) { // Handle incorrect encoding for body/alt_body
            if (strpos($e->getMessage(),'SQLSTATE[22007]') !== false && strpos($e->getMessage(),'`lhc_mailconv_msg`.`alt_body`') !== false) {
                $message->alt_body = '';
                try {
                    $message->saveThis();
                } catch (\ezcPersistentQueryException $e) {
                    $message->body = '';
                    $message->saveThis();
                }
            } elseif (strpos($e->getMessage(),'SQLSTATE[22007]') !== false && strpos($e->getMessage(),'`lhc_mailconv_msg`.`body`') !== false) {
                $message->body = '';
                try {
                    $message->saveThis();
                } catch (\ezcPersistentQueryException $e) {
                    $message->alt_body = '';
                    $message->saveThis();
                }
            } else {
                throw $e;
            }
        }

        if ($mail->hasAttachments() == true) {
              self::saveAttatchements($mail, $message, $mailbox);
        }

        return $message;
    }

    public static function cleanupMailBody($body)
    {
        return preg_replace('/<img src="http(s?):\/\/([A-Za-z0-9\.\-]{6,})\/mailconv\/tpx\/([A-Za-z0-9]{20,})" \/>/is','',$body);
    }

    public static function purgeMessage($message)
    {
        $mailbox = erLhcoreClassModelMailconvMailbox::fetch($message->mailbox_id);

        // Check that we have trash mailbox configured
        if ($mailbox->trash_mailbox == null) {
            return;
        }

        if ($mailbox->delete_mode == erLhcoreClassModelMailconvMailbox::DELETE_ALL) {

            if ($mailbox->auth_method == erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {

                $mailboxHandler = \LiveHelperChat\mailConv\OAuth\OAuth::getClient($mailbox);
                $mailboxFolderOAuth = $mailboxHandler->getFolderByPath($message->mb_folder);

                $messagesCollection = $mailboxFolderOAuth->search()->whereUid($message->uid)->get();

                if ($messagesCollection->total() == 1) {
                    $email = $messagesCollection->shift();
                    $email->delete(true, $mailbox->trash_mailbox);
                }

            } else {
                $mailboxHandler = new PhpImap\Mailbox(
                    $message->mb_folder, // We use message mailbox folder.
                    $mailbox->username, // Username for the before configured mailbox
                    $mailbox->password, // Password for the before configured username
                    false
                );

                $mailboxHandler->moveMail($message->uid, $mailbox->trash_mailbox);
                $mailboxHandler->expungeDeletedMails();

            }
        }
    }
}

?>