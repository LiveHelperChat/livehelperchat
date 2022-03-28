<?php

/*
 * php cron.php -s site_admin -c cron/mail/monitor_mailbox
 *
 * Monitor is there any failing mailbox
 * */
$mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options_general');
$data = (array)$mcOptions->data;

if (isset($data['report_email']) && $data['report_email'] != '') {

    $failedMailbox = [];
    foreach (erLhcoreClassModelMailconvMailbox::getList(['filter' => ['active' => 1, 'failed' => 1]]) as $item) {
        $failedMailbox[] = $item->mail . ' | ID - ' . $item->id . ' | ' . $item->last_sync_log;
    }

    if (!empty($failedMailbox)) {
        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->FromName = 'Live Helper Chat Mailbox';
        $mail->Subject = 'Live Helper Chat Mailbox Import Failure';
        $mail->Body = "Last failed mailbox - \n" . implode("\n",$failedMailbox);

        $emailRecipient = explode(',',$data['report_email']);

        foreach ($emailRecipient as $receiver) {
            $mail->AddAddress( trim($receiver) );
        }

        erLhcoreClassChatMail::setupSMTP($mail);
        $mail->Send();
    }
}

?>
