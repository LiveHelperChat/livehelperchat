<?php

// php cron.php -s site_admin -c cron/update/update_445

echo "Starting update for 4.45v\n";
$db = ezcDbInstance::get();
$sql = "UPDATE `lhc_mailconv_conversation` SET `from_address_clean` = LOWER(CONCAT(replace(regexp_replace(`from_address`, '(@+)(.*)', ''),'.',''),'@',regexp_replace(`from_address`, '(.*)(@+)', '')));";
$stmt = $db->prepare($sql);
$stmt->execute();
echo "Finished update for 4.45v\n";

?>