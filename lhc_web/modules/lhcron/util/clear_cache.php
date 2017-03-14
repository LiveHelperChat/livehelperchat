<?php 

// php cron.php -s site_admin -c cron/util/clear_cache

$CacheManager = erConfigClassLhCacheConfig::getInstance();
$CacheManager->expireCache();
echo "Finished clearing cache\n";

?>