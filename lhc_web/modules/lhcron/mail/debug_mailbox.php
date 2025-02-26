<?php

/*
 * php cron.php -s site_admin -c cron/mail/debug_mailbox -p <mailbox_id>
 * */

// Lock filter object
$db = ezcDbInstance::get();

$mailbox = erLhcoreClassModelMailconvMailbox::fetch($cronjobPathOption->value);

// Reset attributes for sync always to work
$mailbox->last_process_time = 0;
$mailbox->sync_started = 0;
$mailbox->last_sync_time = 0;
$mailbox->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PENDING;
$uuidStatusArray = $mailbox->uuid_status_array;
foreach ($uuidStatusArray as $key => $uuidStatus) {
    $uuidStatusArray[$key] = 0;
}
$mailbox->uuid_status = json_encode($uuidStatusArray);
$mailbox->updateThis(array('update' => array('sync_started','last_sync_time','sync_status','last_process_time','uuid_status')));

echo "STARTING_SYNC\n";

erLhcoreClassMailconvParser::syncMailbox($mailbox,[
    'debug_sync' => true,
    'live' => true
]);

echo "FINISHED_SYNC\n";



?>
