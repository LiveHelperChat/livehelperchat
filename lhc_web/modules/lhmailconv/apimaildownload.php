<?php

$mail = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

if (!($mail instanceof \erLhcoreClassModelMailconvMessage)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
    if (isset($mailData['mail'])) {
        $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetchAndLock($Params['user_parameters']['id']);
    }
}

try {

    header('Content-Disposition: attachment; filename="'.$mail->id.'.eml"');
    header('Content-type: text/plain');

    echo \LiveHelperChat\mailConv\helpers\DownloadHelper::download($mail);

    exit;

} catch (Exception $e) {

    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');

    $tpl->set('errors',[
        erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Message with specified ID could not be found anymore in provided IMAP folder'),
        htmlspecialchars($e->getMessage()),
        erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Message ID').' - '.$mail->uid.' '.$mail->message_id,
        $mail->mb_folder
    ]);

    $Result['content'] = $tpl->fetch();
}

?>