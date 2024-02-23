<?php

/**
 * php cron.php -s site_admin -c cron/archive_mail
 *
 * Run every 30 minits or so. On this cron depends mails archive module and files maintenance. It should be run less frequency than workflow script.
 *
 * */
echo "Starting archive workflow\n";

$arOptions = erLhcoreClassModelChatConfig::fetch('mail_archive_options');
$data = (array)$arOptions->data;

if (isset($data['automatic_archiving']) && $data['automatic_archiving'] == 1) {

    $lastArchive = \LiveHelperChat\Models\mailConv\Archive\Range::findOne(array('sort' => 'id DESC', 'filter' => ['type' => 0]));

    if ($data['archive_strategy'] == 1) {
        if (!($lastArchive instanceof \LiveHelperChat\Models\mailConv\Archive\Range)) {
            $lastArchive = new \LiveHelperChat\Models\mailConv\Archive\Range();
            $lastArchive->year_month = date('Ym');
            $lastArchive->range_from = time();
            $lastArchive->range_to = 0;
        } elseif ($lastArchive->year_month != date('Ym')) {
            $lastArchive->range_to = time();
            $lastArchive->saveThis();

            // Create a new archive
            $lastArchive = new \LiveHelperChat\Models\mailConv\Archive\Range();
            $lastArchive->year_month = date('Ym');
            $lastArchive->range_from = time();
            $lastArchive->range_to = 0;
        }

        $lastArchive->older_than = $data['older_than'];
        $lastArchive->saveThis();

        echo "Moving older mails than " . $data['older_than'] . " days\n";

        // Creates tables
        $lastArchive->createArchive();

        for ($i = 1; $i < 50; $i++) {
            // Process
            $status = $lastArchive->process(array($data));
            echo "First archived mail id - [" . $status['fcid']. ']' . ' Last - [' . $status['lcid'] . '] Messages - ' . $status['messages_archived'] . ' Mails - ' . $status['mails_archived'] . "\n";
        }

    } elseif ($data['archive_strategy'] == 2) {

        $lastArchive = \LiveHelperChat\Models\mailConv\Archive\Range::findOne(array('sort' => 'id DESC', 'filter' => ['type' => 0]));
        if (!($lastArchive instanceof \LiveHelperChat\Models\mailConv\Archive\Range)) {
            $lastArchive = new \LiveHelperChat\Models\mailConv\Archive\Range();
            $lastArchive->year_month = date('Ym');
            $lastArchive->range_from = time();
            $lastArchive->range_to = 0;
        } elseif ($lastArchive->mails_in_archive > $data['max_mails']) {

            echo "Creating new archive because mails's number bigger than {$lastArchive->mails_in_archive} > {$data['max_mails']}\n";

            $lastArchive->range_to = time();
            $lastArchive->saveThis();

            $lastArchive = new \LiveHelperChat\Models\mailConv\Archive\Range();
            $lastArchive->year_month = date('Ym');
            $lastArchive->range_from = time();
            $lastArchive->range_to = 0;
        }

        $lastArchive->older_than = $data['older_than'];
        $lastArchive->saveThis();

        echo "Moving older mails than " . $data['older_than'] . " days\n";

        // Creates tables
        $lastArchive->createArchive();

        for ($i = 1; $i < 50; $i++) {
            // Process
            $status = $lastArchive->process(array($data));
            echo "First archived mail id - [" . $status['fcid']. ']' . ' Last - [' . $status['lcid'] . '] Messages - ' . $status['messages_archived'] . ' Mails - ' . $status['mails_archived'] . "\n";
        }
    }

} else {
    echo "Automatic mail archiving is not setup\n";
}

?>