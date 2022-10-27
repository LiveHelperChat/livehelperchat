<?php
/**
 * php cron.php -s site_admin -c cron/report
 *
 * Run every minit.
 *
 * */
echo "Starting report sending\n";

\LiveHelperChat\Validators\ReportValidator::sendReports();

echo "Ended report sending\n";

?>