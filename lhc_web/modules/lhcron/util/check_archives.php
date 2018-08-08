<?php

/**
 * php cron.php -s site_admin -c cron/util/check_archives
 *
 * Run every 30 minits or so. On this cron depends chats archive module and files maintenance. It should be run less frequency than workflow script.
 *
 * */
echo "Starting archive check workflow\n";

foreach (erLhcoreClassModelChatArchiveRange::getList() as $archiveRange) {

    echo "Checking archive - ",$archiveRange->id,"\n";
    // Dispatch event if chat is archived
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.check_archive', array('archive' => $archiveRange));
}

?>