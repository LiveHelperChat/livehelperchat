<?php

/**
 * php cron.php -s site_admin -c cron/util/maintain_database
 *
 * Run once a week.
 */
echo "Starting database maintain\n";

echo "Starting optimising `lh_userdep`\n";
$db = ezcDbInstance::get();
$sql = 'OPTIMIZE TABLE `lh_userdep`';
$stmt = $db->prepare($sql);
$stmt->execute();
echo "Finished optimising `lh_userdep`\n";
