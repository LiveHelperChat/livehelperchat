<?php

try {

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {
        $signature = $conv->mailbox->signature;

        $signature = str_replace([
            '{operator}',
            '{department}'
        ],[
            $currentUser->getUserData()->name_official,
            $conv->department_name
            ],$signature);

        $replyRecipients = [];
        $replyRecipientsMapped = [];

        foreach ($message->reply_to_data_keyed as $replyEmail => $name) {
            $replyRecipients[$replyEmail] = $name;
        }

        /*
         * We should not fill reply recipient with recipient
         *
         * foreach ($message->to_data_keyed as $replyEmail => $name) {
            $replyRecipients[$replyEmail] = $name;
        }*/

        foreach ($replyRecipients as $mail => $name) {
            if ($mail != $conv->mailbox->mail) {
                $replyRecipientsMapped[] = ['email' => $mail, 'name' => $name];
            }
        }

        $prepend = '<p>' . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','On') . ' ' . date('Y-m-d H:i',$message->udate).', '. ($message->from_name != '' ? $message->from_name : $message->from_address) . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','wrote') . ':</p>';

        if ($Params['user_parameters']['mode'] == 'forward') {
            $replyRecipientsMapped = [['email' => '', 'name' => '']];
            $message->cc_data_array = [];
            $message->bcc_data_array = [];
            $partsIntro = [
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From') . ': ' . ($message->from_name != '' ? '<b>' . $message->from_name .'</b>' : '') . ' <' . $message->from_address .'>',
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Date') . ': ' . date('D',$message->udate) . ', ' . date('d',$message->udate) . ' ' . date('M',$message->udate) . ' ' . date('Y',$message->udate) . ' '. erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','at') . ' ' . date('H:i',$message->udate),
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Subject') . ': ' . $message->subject,
                erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','To') . ': ' . $message->to_data_front,
            ];

            if (!empty($message->cc_data_front)) {
                $partsIntro[] = 'Cc: ' . $message->cc_data_front;
            }

            if (!empty($message->bcc_data_front)) {
                $partsIntro[] = 'Bcc: ' . $message->bcc_data_front;
            }

            $prepend = "---------- " . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Forwarded message') . " ---------<br/>";
            $prepend .= implode("<br/>",$partsIntro);
        }

        if (empty($replyRecipientsMapped)) {
            $replyRecipientsMapped[] = ['email' => $message->from_address, 'name' => $message->from_name];
        }

        echo json_encode([
            'intro' => ($conv->mailbox->signature_under == 1 ? '<div class="gmail_signature">' . $signature . '</div>' : '') . $prepend,
            'signature' => '<div class="gmail_signature">' . $signature . '</div>',
            'signature_under' => ($conv->mailbox->signature_under == 1),
            'recipients' => [
            'to' => $message->to_data_array,
            'reply' => $replyRecipientsMapped,
            'cc' => $message->cc_data_array,
            'bcc' => $message->bcc_data_array]
        ]);
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