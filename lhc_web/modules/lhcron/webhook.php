<?php
/**
 * php cron.php -s site_admin -c cron/webhook
 *
 * Run every 20 seconds
 *
 * */

$fp = fopen("cache/webhook.lock", "w+");

// Gain the lock
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    echo "Couldn't get the lock! Another process is already running";
    fclose($fp);
    return;
} else {
    echo "Lock acquired. Starting process!";
}

erLhcoreClassChatWebhookContinuous::processEvent();
\LiveHelperChat\mailConv\Webhooks\Continous::processEventMail();

flock($fp, LOCK_UN); // release the lock
fclose($fp);