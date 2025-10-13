<?php

/*
 * php cron.php -s site_admin -c cron/mail/debug_mailbox -p <mailbox_id>
 * */

// Lock filter object
$db = ezcDbInstance::get();

// Set all relevant IMAP timeouts to 10 seconds
// 1. Connection timeout (for imap_open)
imap_timeout(IMAP_OPENTIMEOUT, 10);
// 2. Read timeout (for reading data from the server)
imap_timeout(IMAP_READTIMEOUT, 10);
// 3. Write timeout (for sending data to the server)
imap_timeout(IMAP_WRITETIMEOUT, 10);
// Close timeout.
imap_timeout(IMAP_CLOSETIMEOUT, 10);

$mailbox = erLhcoreClassModelMailconvMailbox::fetch($cronjobPathOption->value);

// Retrieve the current OPEN (connection) timeout
$openTimeout = imap_timeout(IMAP_OPENTIMEOUT);

// Retrieve the current READ timeout
$readTimeout = imap_timeout(IMAP_READTIMEOUT);

// Retrieve the current WRITE timeout
$writeTimeout = imap_timeout(IMAP_WRITETIMEOUT);

echo "Open Timeout: " . $openTimeout . " seconds\n";
echo "Read Timeout: " . $readTimeout . " seconds\n";
echo "Write Timeout: " . $writeTimeout . " seconds\n";

try {
    erLhcoreClassMailconvParser::getRawConnection($mailbox);
    echo '✔️ '.erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Connection established to IMAP server.');
    exit;
} catch (Exception $e) {
    echo '❌ ' . htmlspecialchars($e->getMessage());
    exit;
}

exit;

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
