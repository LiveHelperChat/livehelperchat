<?php

/*
 * php cron.php -s site_admin -c cron/mail/delete_mail_item
 *
 * */
$fp = fopen("cache/cron_mail_delete_mail_item.lock", "w+");

// Gain the lock
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    echo "Couldn't get the lock! Another process is already running\n";
    fclose($fp);
    exit;
} else {
    echo "Lock acquired. Starting process!\n";
}

$cfg = erConfigClassLhConfig::getInstance();
$worker = $cfg->getSetting( 'webhooks', 'worker' );

if (\LiveHelperChat\Models\mailConv\Delete\DeleteItem::estimateRows() > 0)
{
    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
        if (erLhcoreClassRedis::instance()->llen('resque:queue:lhc_mailconv_delete') <= 4) {
            erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv_delete', '\LiveHelperChat\mailConv\workers\DeleteWorker', array('is_background' => true));
        }
    } else {
        $deleteWorker = new \LiveHelperChat\mailConv\workers\DeleteWorker();
        $deleteWorker->perform();
    }
}

flock($fp, LOCK_UN); // release the lock
fclose($fp);

?>
