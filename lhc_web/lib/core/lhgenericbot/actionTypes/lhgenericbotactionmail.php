<?php

class erLhcoreClassGenericBotActionMail {

    public static function process($chat, $action, $trigger, $params = array())
    {
        if (isset($action['content']['text']) && $action['content']['text'] != '') {

            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";

            if (isset($action['content']['mail_options']['from_email']) && $action['content']['mail_options']['from_email'] != '') {
                $mail->Sender = $mail->From = $action['content']['mail_options']['from_email'];
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
                $mail->AddAddress(erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['mail_options']['recipient'], array('chat' => $chat, 'args' => $params)));
            }

            $mail->Body = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['text'], array('chat' => $chat, 'args' => $params));

            erLhcoreClassChatMail::setupSMTP($mail);

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

            $mail->Send();
            $mail->ClearAddresses();

        }
    }
}

?>