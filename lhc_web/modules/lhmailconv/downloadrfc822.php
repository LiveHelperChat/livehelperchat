<?php

$mail = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

if (!($mail instanceof \erLhcoreClassModelMailconvMessage)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
    if (isset($mailData['mail'])) {
        $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetchAndLock($Params['user_parameters']['id']);
    }
}

if ($mail instanceof \erLhcoreClassModelMailconvMessage && ($conv = $mail->conversation) instanceof \erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv)) {
    header('Content-Disposition: attachment; filename="'.$mail->id.'.eml"');
    header('Content-type: text/plain');

    echo $mail->rfc822_body;

    exit;
} else {
    http_response_code(403);
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/errors/adminchatnopermission.tpl.php');
    $Result['content'] = $tpl->fetch();
}

?>