<?php

class erLhcoreClassMailconvValidator {

    public static function validatePersonalMailboxGroup($item) {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'mailbox_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY
            ),
            'user_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY
            ),
            'active' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'mailbox_id' )) {
            $data = [];
            foreach ($form->mailbox_id as $mailboxId) {
                $data[(int)$mailboxId] = $form->user_id[(int)$mailboxId];
            }
            $item->mails_array = $data;
            $item->mails = json_encode($data);
        }

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a name!');
        }

        if ($form->hasValidData( 'active' )) {
            $item->active = $form->active;
        } else {
            $item->active = 0;
        }

        return $Errors;
    }

    public static function validateMatchRule($item) {

        $definition = array(
            'dep_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'conditions' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'active' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'close_conversation' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'mailbox_ids' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY
            ),
            'priority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'priority_rule' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'from_mail' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'from_name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'subject_contains' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'dep_id' )) {
            $item->dep_id = $form->dep_id;
        } else {
            $item->dep_id = 0;
        }

        if ($form->hasValidData( 'from_mail' )) {
            $item->from_mail = $form->from_mail;
        } else {
            $item->from_mail = '';
        }

        if ($form->hasValidData( 'conditions' )) {
            $item->conditions = $form->conditions;
        } else {
            $item->conditions = '';
        }

        if ($form->hasValidData( 'from_name' )) {
            $item->from_name = $form->from_name;
        } else {
            $item->from_name = '';
        }

        if ($form->hasValidData( 'priority' )) {
            $item->priority = $form->priority;
        } else {
            $item->priority = 0;
        }

        if ($form->hasValidData( 'priority_rule' )) {
            $item->priority_rule = $form->priority_rule;
        } else {
            $item->priority_rule = 0;
        }

        if ($form->hasValidData( 'subject_contains' )) {
            $item->subject_contains = $form->subject_contains;
        } else {
            $item->subject_contains = '';
        }

        if ( $form->hasValidData( 'mailbox_ids' )) {
            $item->mailbox_ids = $form->mailbox_ids;
        } else {
            $item->mailbox_ids = [];
        }

        $item->mailbox_id = json_encode($item->mailbox_ids);

        if ($form->hasValidData( 'active' ) && $form->active == true) {
            $item->active = 1;
        } else {
            $item->active = 0;
        }

        $options = $item->options_array;

        if ($form->hasValidData( 'close_conversation' ) && $form->close_conversation == true) {
            $options['close_conversation'] = 1;
        } else {
            $options['close_conversation'] = 0;
        }

        $item->options_array = $options;
        $item->options = json_encode($item->options_array);

        return $Errors;
    }

    public static function validateMailbox($item) {
        $definition = array(
            'mail' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'username' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'password' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'host' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'mail_smtp' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'name_smtp' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'username_smtp' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'password_smtp' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'imap' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'create_a_copy' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'signature' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'port' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'user_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            'import_priority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'active' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'delete_mode' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'signature_under' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'assign_parent_user' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'no_pswd_smtp' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'sync_interval' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'import_since' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'reopen_timeout' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
        );


        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'mail' ))
        {
            $item->mail = $form->mail;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter an e-mail!');
        }

        if ( $form->hasValidData( 'username' )) {
            $item->username = $form->username;
        } else {
            $item->username = '';
        }

        if ( $form->hasValidData( 'signature_under' ) && $form->signature_under == true) {
            $item->signature_under = 1;
        } else {
            $item->signature_under = 0;
        }
        if ($form->hasValidData( 'user_id' )) {
            $item->user_id = $form->user_id;
        } else {
            $item->user_id = 0;
        }

        if ( $form->hasValidData( 'import_priority' )) {
            $item->import_priority = $form->import_priority;
        } else {
            $item->import_priority = 0;
        }

        if ( $form->hasValidData( 'name' )) {
            $item->name = $form->name;
        } else {
            $item->name = '';
        }

        if ($form->hasValidData( 'create_a_copy' ) && $form->create_a_copy == true) {
            $item->create_a_copy = 1;
        } else {
            $item->create_a_copy = 0;
        }

        if ($form->hasValidData( 'no_pswd_smtp' ) && $form->no_pswd_smtp == true) {
            $item->no_pswd_smtp = 1;
        } else {
            $item->no_pswd_smtp = 0;
        }

        if ($form->hasValidData( 'assign_parent_user' ) && $form->assign_parent_user == true) {
            $item->assign_parent_user = 1;
        } else {
            $item->assign_parent_user = 0;
        }

        if ( $form->hasValidData( 'imap' )) {
            $item->imap = $form->imap;
        } else {
            $item->imap = '';
        }

        if ( $form->hasValidData( 'signature' )) {
            $item->signature = $form->signature;
        } else {
            $item->signature = '';
        }

        if ( $form->hasValidData( 'sync_interval' )) {
            $item->sync_interval = $form->sync_interval;
        } else {
            $item->sync_interval = 60;
        }

        if ( $form->hasValidData( 'import_since' )) {
            $item->import_since = $form->import_since;
        } else {
            $item->import_since = 0;
        }
        
        if ( $form->hasValidData( 'reopen_timeout' )) {
            $item->reopen_timeout = $form->reopen_timeout;
        } else {
            $item->reopen_timeout = 0;
        }

        if ( $form->hasValidData( 'password' )) {
            $item->password = $form->password;
        } else {
            $item->password = '';
        }

        if ( $form->hasValidData( 'host' )) {
            $item->host = $form->host;
        } else {
            $item->host = '';
        }

        if ( $form->hasValidData( 'port' )) {
            $item->port = $form->port;
        } else {
            $item->port = '';
        }

        if ( $form->hasValidData( 'mail_smtp' )) {
            $item->mail_smtp = $form->mail_smtp;
        } else {
            $item->mail_smtp = '';
        }

        if ( $form->hasValidData( 'name_smtp' )) {
            $item->name_smtp = $form->name_smtp;
        } else {
            $item->name_smtp = '';
        }

        if ( $form->hasValidData( 'username_smtp' )) {
            $item->username_smtp = $form->username_smtp;
        } else {
            $item->username_smtp = '';
        }

        if ( $form->hasValidData( 'password_smtp' )) {
            $item->password_smtp = $form->password_smtp;
        } else {
            $item->password_smtp = '';
        }

        if ( $form->hasValidData( 'active' ) && $form->active == true) {
            $item->active = 1;
        } else {
            $item->active = 0;
        }

        if ($form->hasValidData( 'delete_mode' ) && $form->delete_mode == true) {
            $item->delete_mode = erLhcoreClassModelMailconvMailbox::DELETE_ALL;
        } else {
            $item->delete_mode = erLhcoreClassModelMailconvMailbox::DELETE_LOCAL;
        }

        return $Errors;
    }
    
    public static function validateResponseTemplate($item) {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'template' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'template_plain' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'DepartmentID' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
            'dep_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'name' ) && $form->name != '')
        {
            $item->name = $form->name;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a name!');
        }

        if ( $form->hasValidData( 'template' )) {
            $item->template = $form->template;
        } else {
            $item->template = '';
        }
        
        if ( $form->hasValidData( 'template_plain' )) {
            $item->template_plain = $form->template_plain;
        } else {
            $item->template_plain = '';
        }

        if ( $form->hasValidData( 'dep_id' )) {
            $item->dep_id = $form->dep_id;
        } else {
            $item->dep_id = 0;
        }

        if (!$form->hasValidData( 'DepartmentID' )) {
            $item->dep_id = 0;
        } else {
            $item->dep_id = -1;
            $item->department_ids = $form->DepartmentID;
        }

        return $Errors;
    }

    public static function setSendParameters($mailbox, $phpmailer)
    {
        $phpmailer->IsSMTP();
        $phpmailer->Host = $mailbox->host;
        $phpmailer->Port = $mailbox->port;

        $phpmailer->From = $mailbox->mail_smtp != '' ?  $mailbox->mail_smtp : $mailbox->mail;
        $phpmailer->FromName = $mailbox->name_smtp != '' ? $mailbox->name_smtp : $mailbox->name;

        if ($mailbox->username_smtp != '') {
            $phpmailer->Username = $mailbox->username_smtp;
            $phpmailer->Password = $mailbox->password_smtp;
            $phpmailer->SMTPAuth = true;
            return;
        }

        if ($mailbox->no_pswd_smtp == 0 && $mailbox->username != '') {
            $phpmailer->Username = $mailbox->username;
            $phpmailer->Password = $mailbox->password;
            $phpmailer->SMTPAuth = true;
        } else {
            $phpmailer->From = '';
        }
    }

    public static function prepareMailContent($content, $mailReply) {

        // Parse links
        $matches = [];

        $string = '/href="' . str_replace('/','\/',erLhcoreClassDesign::baseurl('file/downloadfile')) . '([a-zA-Z0-9-\.-\/\_]+)"/';

        preg_match_all($string,$content,$matches);

        foreach ($matches[1] as $index => $file) {
            $paramsFile = explode('/',trim($file,'/'));
            $fileObj = erLhcoreClassModelChatFile::fetch($paramsFile[0]);
            if ($fileObj instanceof erLhcoreClassModelChatFile && $fileObj->security_hash == $paramsFile[1]) {
                $content = str_replace($matches[0][$index],'href="' . erLhcoreClassXMP::getBaseHost().  $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$fileObj->id}/{$fileObj->security_hash}",$content);
            }
        }

        // Parse images
        $string = '/src="' . str_replace('/','\/',erLhcoreClassDesign::baseurl('file/downloadfile')) . '([a-zA-Z0-9-\.-\/\_]+)"/';

        preg_match_all($string,$content,$matches);

        foreach ($matches[1] as $index => $file) {
            $paramsFile = explode('/',trim($file,'/'));
            $fileObj = erLhcoreClassModelChatFile::fetch($paramsFile[0]);
            if ($fileObj instanceof erLhcoreClassModelChatFile && $fileObj->security_hash == $paramsFile[1]) {
                $cid = 'lhc-file-' . $fileObj->id . '-' . time();
                $mailReply->AddEmbeddedImage($fileObj->file_path_server, $cid, $fileObj->upload_name);
                $content = str_replace($matches[0][$index],'src="' . 'cid:' . $cid .'"', $content);
            }
        }

        return $content;
    }

    public static function sendReply($params, & $response, $mail, $user_id = 0) {

        $response['errors'] = [];

        if (!isset($params['content']) || empty($params['content'])) {
            $response['errors']['content'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Content is required!');
        }

        if (!isset($params['recipients']['reply']) || empty($params['recipients']['reply'])) {
            $response['errors']['reply'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter at-least one recipient!');
        }

        foreach ($params['recipients']['reply'] as $recipient) {
            if (!isset($recipient['email']) || empty($recipient['email'])) {
                $response['errors']['reply'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','To: Please enter a valid recipient e-mail!');
            } else if (!filter_var($recipient['email'], FILTER_VALIDATE_EMAIL)) {
                $response['errors']['reply'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','To: Invalid e-mail recipient!');
            }
        }

        foreach ($params['recipients']['bcc'] as $recipient) {
            if (!isset($recipient['email']) || empty($recipient['email'])) {
                $response['errors']['reply'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Bcc: Please enter a valid recipient e-mail!');
            } else if (!filter_var($recipient['email'], FILTER_VALIDATE_EMAIL)) {
                $response['errors']['reply'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Bcc: Invalid e-mail recipient!');
            }
        }

        foreach ($params['recipients']['cc'] as $recipient) {
            if (!isset($recipient['email']) || empty($recipient['email'])) {
                $response['errors']['reply'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Cc: Please enter a valid recipient e-mail!');
            } else if (!filter_var($recipient['email'], FILTER_VALIDATE_EMAIL)) {
                $response['errors']['reply'] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Cc: Invalid e-mail recipient!');
            }
        }

        if (empty($response['errors'])) {
            try {
                $mailReply = new PHPMailer(true);
                $mailReply->CharSet = "UTF-8";

                // If it's first reply append 'Re: ' to subject.
                if (isset($params['mode']) && $params['mode'] === 'forward') {
                    $mailReply->Subject = 'Fwd: ' . $mail->subject;
                } else {
                    $mailReply->Subject = ($mail->in_reply_to == '' ? 'Re: ' : '') . $mail->subject;
                }

                $params['content'] = self::prepareMailContent($params['content'], $mailReply);

                $mailReply->Body = $params['content'];
                $mailReply->AltBody = strip_tags(str_replace(['<br />','<br/>'],"\n",$params['content']));

                $mailbox = $mail->conversation->mailbox;

                $mailReply->AddReplyTo($mailbox->mail,(string)$mailbox->name);

                self::setSendParameters($mailbox, $mailReply);

                if ($mail->message_id != '') {
                    $mailReply->addCustomHeader('In-Reply-To', $mail->message_id);
                    $mailReply->addCustomHeader('References', $mail->message_id);
                }

                // Add operator who send a message
                // So once we fetch message we will know whom to assign it
                if ($user_id > 0) {
                    $mailReply->addCustomHeader('X-LHC-ID', $user_id);
                }

                // @todo add validation
                foreach ($params['recipients']['reply'] as $recipient) {
                    $mailReply->AddAddress( $recipient['email'],'' );
                }

                foreach ($params['recipients']['cc'] as $recipient) {
                    $mailReply->addCC( $recipient['email'],'' );
                }

                foreach ($params['recipients']['bcc'] as $recipient) {
                    $mailReply->addBCC( $recipient['email'],'' );
                }

                // Assign attatchements
                foreach ($params['attatchements'] as $attatchement) {
                    $fileObj = erLhcoreClassModelChatFile::fetch($attatchement['id']);
                    if ($fileObj instanceof erLhcoreClassModelChatFile) {
                        $mailReply->addAttachment($fileObj->file_path_server, $fileObj->upload_name);
                    }
                }

                $response['send'] = $mailReply->Send();

                // Create a copy if required
                if ($mailbox->create_a_copy == true) {
                    $response['copy'] = self::makeSendCopy($mailReply, $mailbox);
                }

                // Now we can set appropriate attributes for the message itself.
                if ($mail->accept_time == 0) {
                    $mail->accept_time = $mail->ctime;
                }

                $mail->lr_time = time();
                $mail->response_type = erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL;
                $mail->response_time = $mail->lr_time - $mail->accept_time;
                $mail->status = erLhcoreClassModelMailconvMessage::STATUS_RESPONDED;
                $mail->conv_duration = time() - $mail->ctime;
                $mail->user_id = $user_id; // Update user who replied to customer e-mail
                $mail->updateThis();

            } catch (Exception $e) {
                $response['send'] = false;
                $response['errors']['general'] = $e->getMessage();
            }
        } else {
            $response['send'] = false;
        }
    }

    public static function sendEmail($item, & $response, $user_id = 0) {
        try {
            $mailReply = new PHPMailer(true);
            $mailReply->CharSet = "UTF-8";
            $mailReply->Subject = $item->subject;

            self::setSendParameters($item->mailbox, $mailReply);

            if (!empty($item->to_data)) {
                $mailReply->AddReplyTo($item->to_data, (string)$item->reply_to_data);
            } else {
                $mailReply->AddReplyTo($item->mailbox->mail, (string)$item->mailbox->name);
            }

            $mailReply->AddAddress($item->from_address, $item->from_name);

            $item->body = self::prepareMailContent( $item->body, $mailReply);

            $mailReply->Body = $item->body;
            $mailReply->AltBody = strip_tags(str_replace(['<br />','<br/>'],"\n",$item->body));

            if ($user_id > 0) {
                $mailReply->addCustomHeader('X-LHC-ID', $user_id);

                if ($item->status == erLhcoreClassModelMailconvMessage::STATUS_ACTIVE) {
                    $mailReply->addCustomHeader('X-LHC-ST', erLhcoreClassModelMailconvMessage::STATUS_ACTIVE);
                }
            }

            $response['send'] = $mailReply->Send();

            if ($item->mailbox->create_a_copy == true) {
                $response['copy'] = self::makeSendCopy($mailReply, $item->mailbox);
            }

        } catch (Exception $e) {
            $response['send'] = false;
            $response['errors']['general'] = $e->getMessage();
        }

        return $response;
    }

    // Save a copy in send folder
    public static function makeSendCopy($mail, $mailbox) {

        $path = null;

        foreach ($mailbox->mailbox_sync_array as $syncArray) {
            if (isset($syncArray['send_folder']) && $syncArray['send_folder'] === true) {
                $path = $syncArray['path'];
                break;
            }
        }

        if ($path === null) {
            return ['success' => false, 'reason' => 'No send folder defined!'];
        }

        \imap_errors();

        // Create a copy in send folder
        $imapStream = imap_open($path, $mailbox->username, $mailbox->password);
        $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
        imap_close($imapStream);

        if ($result !== true) {
            return ['success' => false, 'reason' => implode("\n",imap_errors())];
        }

        $messageId = sprintf('<%s@%s>', $mail->uniqueid, $mail->serverHostname());

        return ['success' => true, 'message_id' => $messageId];
    }

    public static function validateNewEmail(& $item){

        $definition = array(
            'subject' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'from_address' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'from_name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'to_data' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'reply_to_data' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'body' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'send_status' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            'mailbox_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'subject' ) && $form->subject != '')
        {
            $item->subject = $form->subject;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a subject!');
        }

        if ( $form->hasValidData( 'from_address' ) && $form->from_address != '') {
            $item->from_address = $form->from_address;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter recipient e-mail!');
        }

        if ( $form->hasValidData( 'from_name' )) {
            $item->from_name = $form->from_name;
        } else {
            $item->from_name = '';
        }

        // Reply e-mail
        if ( $form->hasValidData( 'to_data' )) {
            $item->to_data = $form->to_data;
        } else {
            $item->to_data = '';
        }

        // Reply name
        if ( $form->hasValidData( 'reply_to_data' )) {
            $item->reply_to_data = $form->reply_to_data;
        } else {
            $item->reply_to_data = '';
        }

        if ( $form->hasValidData( 'body' )) {
            $item->body = $form->body;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter e-mail body!');
        }

        if ( $form->hasValidData( 'mailbox_id' )) {
            $mailbox = erLhcoreClassModelMailconvMailbox::findOne(['filter' => ['active' => 1, 'mail' => $form->mailbox_id]]);
            if ($mailbox instanceof erLhcoreClassModelMailconvMailbox){
                $item->mailbox_id = $mailbox->id;
                $item->mailbox_front = $form->mailbox_id;
            } else {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please choose a mailbox!');
            }
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please choose a mailbox!');
        }

        if ($form->hasValidData( 'send_status')) {
            $item->status = erLhcoreClassModelMailconvMessage::STATUS_ACTIVE;
        }

        return $Errors;

    }
}

?>