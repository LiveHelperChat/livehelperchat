<?php

/**
 * php cron.php -s site_admin -c cron/stats/department
 *
 * Run every 12h or so
 *
 * */

echo "Starting departments stats aggregation\n";

foreach (erLhcoreClassModelDepartament::getList(array('filter' => array('archive' => 0, 'disabled' => 0))) as $department) {
    $stats = erLhAbstractModelStats::getInstance(erLhAbstractModelStats::STATS_DEP, $department->id);

    $statsArray = $stats->stats_array;

    // At the moment we use average chat duration only
    // But we can easily add another stats we want.
    $durationAverage = (int)erLhcoreClassChatStatistic::getAverageChatduration(30,array('filter' => array('dep_id' => $department->id)));
    $statsArray['avg_chat_duration'] = $durationAverage;

    // Updates stats
    $stats->stats_array = $statsArray;
    $stats->stats = json_encode($stats->stats_array);
    $stats->lupdate = time();
    $stats->saveThis();
}

?>