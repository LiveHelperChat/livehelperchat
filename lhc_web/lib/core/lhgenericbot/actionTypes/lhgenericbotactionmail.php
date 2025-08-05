<?php

class erLhcoreClassGenericBotActionMail {

    public static function process($chat, $action, $trigger, $params = array())
    {
        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }
        
        if (isset($action['content']['text']) && $action['content']['text'] != '') {

            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";

            if (isset($action['content']['mail_options']['from_email']) && $action['content']['mail_options']['from_email'] != '') {
                $mail->Sender = $mail->From =  erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['mail_options']['from_email'], array('chat' => $chat, 'args' => $params));
            }

            if (isset($action['content']['mail_options']['from_name']) && $action['content']['mail_options']['from_name'] != '') {
                $mail->FromName = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['mail_options']['from_name'], array('chat' => $chat, 'args' => $params));
            }

            $mail->Subject = isset($action['content']['mail_options']['subject']) && $action['content']['mail_options']['subject'] != '' ? erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['mail_options']['subject'], array('chat' => $chat, 'args' => $params)) : 'New mail from chat ' . $chat->id;

            // Reply to
            if (isset($action['content']['mail_options']['reply_to']) && $action['content']['mail_options']['reply_to'] != '') {
                $replyTOs = explode(',', erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['mail_options']['reply_to'], array('chat' => $chat, 'args' => $params)));
                foreach ($replyTOs as $replyItem) {
                    $mail->AddReplyTo(trim($replyItem));
                }
            }

            if (isset($action['content']['mail_options']['recipient']) && $action['content']['mail_options']['recipient'] != '') {
                $recipientsMain = explode(',',erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['mail_options']['recipient'], array('chat' => $chat, 'args' => $params)));
                foreach ($recipientsMain as $replyItem) {
                    $mail->AddAddress(trim($replyItem));
                }
            }

            $bodyMessage = ['text' => $action['content']['text']];
            $paramsExecution = erLhcoreClassGenericBotActionRestapi::extractDynamicVariables($bodyMessage, $chat);

            $bodyText = str_replace(
                array_keys($paramsExecution),
                array_values($paramsExecution),
                $action['content']['text']);

            $mail->Body = erLhcoreClassGenericBotWorkflow::translateMessage($bodyText, array('chat' => $chat, 'args' => $params));

            if (isset($action['content']['mail_options']['attach_files']) && $action['content']['mail_options']['attach_files'] === true) {
                // Split message by [file= and loop through elements
                $messageParts = explode('[file=', $mail->Body);
                
                if (count($messageParts) > 1) {
                    // Skip first part as it doesn't contain file reference
                    for ($i = 1; $i < count($messageParts); $i++) {
                        // Create a mock message object and let extractFile method do the parsing
                        $mockMessage = new stdClass();
                        $mockMessage->msg = '[file=' . $messageParts[$i];
                        $mockMessage->meta_msg = '';
                        
                        // Use LiveHelperChat\Helpers\Chat\Message::extractFile to get file data
                        $fileData = \LiveHelperChat\Helpers\Chat\Message::extractFile($mockMessage);
                        
                        if ($fileData !== null && isset($fileData['file']) && $fileData['file'] instanceof erLhcoreClassModelChatFile) {
                            $file = $fileData['file'];
                            // Attach file to mail
                            $mail->AddAttachment($file->file_path_server, $file->upload_name);
                        }
                    }
                }
            }

            if (isset($action['content']['mail_options']['parse_bbcode']) && $action['content']['mail_options']['parse_bbcode'] === true) {
                $mail->Body = erLhcoreClassBBCodePlain::make_clickable($mail->Body, array('sender' => 0, 'clean_event' => true));
            }

            if (isset($action['content']['mail_options']['do_not_import']) && $action['content']['mail_options']['do_not_import'] == true) {
                $mail->addCustomHeader('X-LHC-IGN', 1);
            }

            if (class_exists('erLhcoreClassModelMailconvMessage') && $chat instanceof erLhcoreClassModelMailconvMessage) {
                
                if ($chat->message_id != '') {
                    $mail->addCustomHeader('In-Reply-To', $chat->message_id);
                    $mail->addCustomHeader('References', $chat->message_id);
                }

                erLhcoreClassMailconvValidator::setSendParameters($chat->mailbox, $mail);
            } else {
                erLhcoreClassChatMail::setupSMTP($mail);
            }

            if (isset($action['content']['mail_options']['bcc_recipient']) && $action['content']['mail_options']['bcc_recipient'] != '') {
                $recipientsBCC = explode(',', erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['mail_options']['bcc_recipient'], array('chat' => $chat, 'args' => $params)));
                foreach ($recipientsBCC as $recipientBCC) {
                    $mail->AddBCC(trim($recipientBCC));
                }
            }

            if (isset($action['content']['mail_options']['cc_recipient']) && $action['content']['mail_options']['cc_recipient'] != '') {
                $recipientsBCC = explode(',', erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['mail_options']['cc_recipient'], array('chat' => $chat, 'args' => $params)));
                foreach ($recipientsBCC as $recipientBCC) {
                    $mail->addCC(trim($recipientBCC));
                }
            }

            if (isset($params['file']) && $params['file'] instanceof erLhcoreClassModelChatFile) {
                $mail->AddAttachment($params['file']->file_path_server, 'file.'.$params['file']->extension);
            }

            if (class_exists('erLhcoreClassModelMailconvMessage') && $chat instanceof erLhcoreClassModelMailconvMessage && isset($action['content']['mail_options']['copy_send']) && $action['content']['mail_options']['copy_send'] == true) {
                $mail->MessageID = sprintf('<%s@%s>', $mail->generateId(), $mail->serverHostname());
                $mail->Send();

                erLhcoreClassMailconvValidator::makeSendCopy($mail, $chat->mailbox);
            } else {
                $mail->Send();
            }


            $mail->ClearAddresses();
        }
    }
}

?>