<?php

/**
 * php cron.php -s site_admin -c cron/archive
 *
 * Run every 10 minits or so. On this cron depends automatic chat transfer and unaswered chats callback.
 *
 * */
echo "Starting archive workflow\n";

$arOptions = erLhcoreClassModelChatConfig::fetch('archive_options');
$data = (array)$arOptions->data;

if (isset($data['automatic_archiving']) && $data['automatic_archiving'] == 1) {

    $lastArchive = erLhcoreClassModelChatArchiveRange::findOne(array('sort' => 'id DESC'));

    if ($data['archive_strategy'] == 1) {
        if (!($lastArchive instanceof erLhcoreClassModelChatArchiveRange)) {
            $lastArchive = new erLhcoreClassModelChatArchiveRange();
            $lastArchive->year_month = date('Ym');
            $lastArchive->range_from = time();
            $lastArchive->range_to = 0;
        } elseif ($lastArchive->year_month != date('Ym')) {
            $lastArchive->range_to = time();
            $lastArchive->saveThis();

            // Create a new archive
            $lastArchive = new erLhcoreClassModelChatArchiveRange();
            $lastArchive->year_month = date('Ym');
            $lastArchive->range_from = time();
            $lastArchive->range_to = 0;
        }

        $lastArchive->older_than = $data['older_than'];
        $lastArchive->saveThis();

        echo "Moving older chats than " . $data['older_than'] . " days\n";

        // Creates tables
        $lastArchive->createArchive();

        for ($i = 1; $i < 50; $i++) {
            // Process
            $status = $lastArchive->process(array($data));
            echo "First archived chat id - [" . $status['fcid']. ']' . ' Last - [' . $status['lcid'] . '] Messages - ' . $status['messages_archived'] . ' Chats - ' . $status['chats_archived'] . "\n";
        }

    } elseif ($data['archive_strategy'] == 2) {

        $lastArchive = erLhcoreClassModelChatArchiveRange::findOne(array('sort' => 'id DESC'));
        if (!($lastArchive instanceof erLhcoreClassModelChatArchiveRange)) {
            $lastArchive = new erLhcoreClassModelChatArchiveRange();
            $lastArchive->year_month = date('Ym');
            $lastArchive->range_from = time();
            $lastArchive->range_to = 0;
        } elseif ($lastArchive->chats_in_archive > $data['max_chats']) {

            echo "Creating new archive because chat's number bigger than {$lastArchive->chats_in_archive} > {$data['max_chats']}\n";

            $lastArchive->range_to = time();
            $lastArchive->saveThis();

            $lastArchive = new erLhcoreClassModelChatArchiveRange();
            $lastArchive->year_month = date('Ym');
            $lastArchive->range_from = time();
            $lastArchive->range_to = 0;
        }

        $lastArchive->older_than = $data['older_than'];
        $lastArchive->saveThis();

        echo "Moving older chats than " . $data['older_than'] . " days\n";

        // Creates tables
        $lastArchive->createArchive();

        for ($i = 1; $i < 50; $i++) {
            // Process
            $status = $lastArchive->process(array($data));
            echo "First archived chat id - [" . $status['fcid']. ']' . ' Last - [' . $status['lcid'] . '] Messages - ' . $status['messages_archived'] . ' Chats - ' . $status['chats_archived'] . "\n";
        }
    }

} else {
    echo "Automatic archiving is not setup\n";
}

?>