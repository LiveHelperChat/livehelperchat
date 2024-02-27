<?php

// php cron.php -s site_admin -c cron/util/delete_mails_by_mailbox -p <mailbox_id>_<id_greater_than>

echo "You have 10 seconds to stop a script\n";
sleep(10);

$parts = explode('_',$cronjobPathOption->value);

$mailbox = erLhcoreClassModelMailconvMailbox::fetch($parts[0]);

while (true) {
    $maillist = erLhcoreClassModelMailconvConversation::getList(array('filtergte' => array('id' => $parts[1]),'filter' => array('mailbox_id' => $mailbox->id)));

    if (empty($maillist)){
        echo "No more mails were found!\n";
        break;
    }

    foreach ($maillist as $mail) {
        echo "Removing - ",$mail->id,"\n";
        $mail->removeThis();
    }
}



?>