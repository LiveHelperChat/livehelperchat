<?php 

// php cron.php -s site_admin -c cron/util/update_database

$jsonObject = json_decode(erLhcoreClassModelChatOnlineUser::executeRequest('https://raw.githubusercontent.com/LiveHelperChat/livehelperchat/master/lhc_web/doc/update_db/structure.json'),true);
$localFile = false;

$jsonObject = null;

if (!is_array($jsonObject)) {
    $localFile = true;
    echo "We could not connect to github to fetch database schema. Trying to update database from local file.\n";
    $jsonObject = json_decode(file_get_contents('doc/update_db/structure.json'), true);
}

if (is_array($jsonObject)) {

    $errorMessages = erLhcoreClassUpdate::doTablesUpdate($jsonObject);

    if ($localFile == true) {
        echo "Using local database schema to update database\n";
    } else {
        echo "Using remote database schema to update database\n";
    }

    if (empty($errorMessages)) {
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();
        echo "UPDATE DONE\n";
    } else {
        echo "ERROR:\n".implode("\n", $errorMessages);
    }

} else {
    echo "Failed to fetch database structure...\n";
}

?>