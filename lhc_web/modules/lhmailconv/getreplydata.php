<?php

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {
        $signature = $conv->mailbox->signature;

        $signature = str_replace([
            '{operator}',
            '{department}',
            '{operator_chat_name}'
        ],[
            $currentUser->getUserData()->name_official,
            $conv->department_name,
            $currentUser->getUserData()->name_support
            ],$signature);

        $signature = erLhcoreClassGenericBotWorkflow::translateMessage($signature, array('chat' => $conv));

        $replyRecipients = [];
        $replyRecipientsMapped = [];
        $isSelfReply = false;

        if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
            $message->setSensitive(true);
        }

        foreach ($message->reply_to_data_keyed as $replyEmail => $name) {

            if ($replyEmail ==  $conv->mailbox->mail){
                $isSelfReply = true;
            }

            if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email') && $message->response_type != erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL) {
                if ($name == $replyEmail) {
                    $name = \LiveHelperChat\Helpers\Anonymizer::maskEmail($name);
                }
                $replyRecipients[\LiveHelperChat\Helpers\Anonymizer::maskEmail($replyEmail)] = $name;
            } else {
                $replyRecipients[$replyEmail] = $name;
            }
        }

        /*
         * We should not fill reply recipient with recipient
         *
         * foreach ($message->to_data_keyed as $replyEmail => $name) {
            $replyRecipients[$replyEmail] = $name;
        }*/

        foreach ($replyRecipients as $mail => $name) {
            if ($mail != $conv->mailbox->mail) {
                $replyRecipientsMapped[] = [
                    'email' => $mail,
                    'name' => $name
                ];
            }
        }

        $mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options');
        $mcOptionsData = (array)$mcOptions->data;
        $prepend = '';

        if (!(isset($mcOptionsData['no_quote_mail']) && $mcOptionsData['no_quote_mail'] == 1)) {
            if (!empty($mcOptionsData['reply_to_tmp'])) {
                $prepend = erLhcoreClassGenericBotWorkflow::translateMessage($mcOptionsData['reply_to_tmp'], array('chat' => $message, 'args' => ['chat' => $message, 'msg' => $message]));
            } else {
                $prepend = '<p>' . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','On') . ' ' . date('Y-m-d H:i',$message->udate).', '. ($message->from_name != '' ? $message->from_name : $message->from_address) . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','wrote') . ':</p>';
            }
        }

        if ($Params['user_parameters']['mode'] == 'forward' && $currentUser->hasAccessTo('lhmailconv', 'send_as_forward')) {
            $replyRecipientsMapped = [['email' => '', 'name' => '']];
            $message->cc_data_array = [];
            $message->bcc_data_array = [];

            if (!empty($mcOptionsData['forward_to_tmp'])) {
                $prepend = erLhcoreClassGenericBotWorkflow::translateMessage($mcOptionsData['forward_to_tmp'], array('chat' => $message, 'args' => ['chat' => $message, 'msg' => $message]));
            } else {
                $partsIntro = [
                    erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From') . ': ' . ($message->from_name != '' ? '<b>' . $message->from_name .'</b>' : '') . ' <' . $message->from_address .'>',
                    erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Date') . ': ' . date('D',$message->udate) . ', ' . date('d',$message->udate) . ' ' . date('M',$message->udate) . ' ' . date('Y',$message->udate) . ' '. erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','at') . ' ' . date('H:i',$message->udate),
                    erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Subject') . ': ' . $message->subject,
                    erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','To') . ': ' . $message->to_data_front,
                ];

                $cc_data_front = $message->cc_data_front;
                if (!empty($cc_data_front)) {
                    $partsIntro[] = 'Cc: ' . $message->cc_data_front;
                }

                $bcc_data_front = $message->bcc_data_front;
                if (!empty($bcc_data_front)) {
                    $partsIntro[] = 'Bcc: ' . $message->bcc_data_front;
                }

                $prepend = "---------- " . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Forwarded message') . " ---------<br/>";
                $prepend .= implode("<br/>",$partsIntro);
            }
        }

        if (empty($replyRecipientsMapped)) {
            $replyRecipientsMapped[] = ['email' => $message->from_address, 'name' => $message->from_name];
        }

        $userData = $currentUser->getUserData();

        if ($userData->invisible_mode == 0 && erLhcoreClassChat::hasAccessToWrite($conv)) {

            if (
                ($conv->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING &&
                    $conv->user_id != $userData->id &&
                    !$currentUser->hasAccessTo('lhmailconv','open_all')) &&
                ($conv->user_id != 0 || !$currentUser->hasAccessTo('lhmailconv','open_unassigned_mail'))
            ) {
                throw new Exception('You do not have permission to open all pending mails.');
            }

            $operatorChanged = false;
            $chatAccepted = false;

            if (
                $conv->user_id == 0 &&
                $conv->status != erLhcoreClassModelMailconvConversation::STATUS_CLOSED &&
                $conv->transfer_uid != $currentUser->getUserID()
            ) {
                $currentUser = erLhcoreClassUser::instance();
                $conv->user_id = $currentUser->getUserID();
                $operatorChanged = true;
            }

            // If status is pending change status to active
            if (
                $conv->status == erLhcoreClassModelMailconvConversation::STATUS_PENDING &&
                $conv->transfer_uid != $currentUser->getUserID()
            ) {
                $conv->status = erLhcoreClassModelMailconvConversation::STATUS_ACTIVE;
                $conv->accept_time = time();
                $conv->wait_time = $conv->accept_time - $conv->pnd_time;
                $conv->user_id = $currentUser->getUserID();
                $chatAccepted = true;
            }

            if ($conv->transfer_uid > 0) {
                erLhcoreClassTransfer::handleTransferredChatOpen($conv, $currentUser->getUserID(), erLhcoreClassModelTransfer::SCOPE_MAIL);
            }

            if ($chatAccepted || $operatorChanged) {
                erLhcoreClassMailconvWorkflow::changePersonalMailbox($conv,$conv->user_id);

                // We update directly as it's the only place
                // Mails get's re-indexed after conversation update
                $db = ezcDbInstance::get();
                $stmt = $db->prepare('UPDATE `lhc_mailconv_msg` SET `conv_user_id` = :conv_user_id WHERE `conversation_id` = :conversation_id');
                $stmt->bindValue(':conversation_id',$conv->id,PDO::PARAM_INT);
                $stmt->bindValue(':conv_user_id',$conv->user_id,PDO::PARAM_INT);
                $stmt->execute();

                // Assign unassigned mails to present owner
                $stmt = $db->prepare('UPDATE `lhc_mailconv_msg` SET `user_id` = :user_id WHERE `user_id` = 0 AND `conversation_id` = :conversation_id');
                $stmt->bindValue(':conversation_id',$conv->id,PDO::PARAM_INT);
                $stmt->bindValue(':user_id',$conv->user_id,PDO::PARAM_INT);
                $stmt->execute();

                erLhcoreClassChat::updateActiveChats($conv->user_id);

                if ($conv->department !== false) {
                    erLhcoreClassChat::updateDepartmentStats($conv->department);
                }

                erLhcoreClassMailconvWorkflow::logInteraction($conv->plain_user_name . ' [' . $conv->user_id.'] '.erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','has accepted a mail by clicking reply button.'), $conv->plain_user_name, $conv->id);
            }

            $conv->updateThis();
        }

        echo json_encode([
            'intro' => (!empty($signature) && $conv->mailbox->signature_under == 1 ? '<div class="gmail_signature">' . $signature . '</div>' : '') . $prepend,
            'user_id' => $conv->user_id,
            'is_owner' => (erLhcoreClassUser::instance()->getUserID() == $conv->user_id),
            'is_self_reply' => $isSelfReply,
            'signature' => (!empty($signature) ? '<div class="gmail_signature">' . $signature . '</div>' : ''),
            'signature_under' => ($conv->mailbox->signature_under == 1),
            'recipients' => [
            'to' => $message->to_data_array,
            'reply' => $replyRecipientsMapped,
            'cc' => $message->cc_data_array,
            'bcc' => $message->bcc_data_array]
        ],\JSON_INVALID_UTF8_IGNORE);
        exit;

    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to read conversation.'));
    }

} catch (Exception $e) {
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/errors/adminchatnopermission.tpl.php');
    echo $tpl->fetch();
    exit;
}

?>