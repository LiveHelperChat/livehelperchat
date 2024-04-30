<?php
/**
 *
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

$copyWorker = new \LiveHelperChat\mailConv\workers\SentCopyWorker();
$copyWorker->args['copy_id'] = 1;
$copyWorker->perform();


?>