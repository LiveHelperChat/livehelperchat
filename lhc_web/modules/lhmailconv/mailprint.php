<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/mailprint.tpl.php');

$mail = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

if (!($mail instanceof \erLhcoreClassModelMailconvMessage)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
    if (isset($mailData['mail'])) {
        $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetchAndLock($Params['user_parameters']['id']);
    }
}

if ($mail instanceof \erLhcoreClassModelMailconvMessage && ($conv = $mail->conversation) instanceof \erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv)) {
    if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
        $mail->setSensitive(true);
    }

    if (!erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email')) {
        if ($mail->response_type !== erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL) {
            if ($mail->from_address == $mail->from_name) {
                $mail->from_name = \LiveHelperChat\Helpers\Anonymizer::maskEmail($mail->from_name);
            }
            $mail->from_address = \LiveHelperChat\Helpers\Anonymizer::maskEmail($mail->from_address);
        }
    }

    if ((int)erLhcoreClassModelChatConfig::fetch('guardrails_enabled')->current_value == 1 && !erLhcoreClassUser::instance()->hasAccessTo('lhmailconv', 'see_sensitive_information')) {
        \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskBodyFront($mail);
    }

    $tpl->set('chat',$mail);
} else {
    http_response_code(403);
    $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>