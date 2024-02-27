<?php

$mail = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

if (!($mail instanceof \erLhcoreClassModelMailconvMessage)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
    if (isset($mailData['mail'])) {
        $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetchAndLock($Params['user_parameters']['id']);
    }
}

header('Content-Disposition: attachment; filename="'.$mail->id.'.eml"');
header('Content-type: text/plain');

echo $mail->rfc822_body;

exit;
?>