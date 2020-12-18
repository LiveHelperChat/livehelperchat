<?php

/**
 *
 * Calculate all
 * php cron.php -s site_admin -c cron/stats/department
 *
 * Equal to
 * php cron.php -s site_admin -c cron/stats/department -p avg_chat_duration,avg_wait_time
 *
 * Calculate only `avg_chat_duration`
 * php cron.php -s site_admin -c cron/stats/department -p avg_chat_duration
 *
 * Calculate only `avg_wait_time`
 * php cron.php -s site_admin -c cron/stats/department -p avg_wait_time
 *
 * Run every 6h-12h or so
 *
 * */

echo "-=Starting departments stats aggregation=-\n";

$valid = array_filter(explode(',',str_replace(' ','',trim($cronjobPathOption->value))));

if (empty($valid)) {
    $valid = array (
        'avg_chat_duration',
        'avg_wait_time'
    );
}

$statisticOptions = erLhcoreClassModelChatConfig::fetch('statistic_options');
$configuration = (array)$statisticOptions->data;

foreach (erLhcoreClassModelDepartament::getList(array('filter' => array('archive' => 0, 'disabled' => 0))) as $department) {
    $stats = erLhAbstractModelStats::getInstance(erLhAbstractModelStats::STATS_DEP, $department->id);

    $statsArray = $stats->stats_array;

    // At the moment we use average chat duration only
    // But we can easily add another stats we want.

    if (in_array('avg_chat_duration',$valid)) {

        echo "Calculating `avg_chat_duration` for department {$department->id}\n";

        // Average chat duration
        $filter = array('filter' => array('dep_id' => $department->id));

        if (isset($configuration['avg_chat_duration']) && $configuration['avg_chat_duration'] > 0){
            $filter['filtergte']['time'] = time() - $configuration['avg_chat_duration'];
        }

        $durationAverage = (int)erLhcoreClassChatStatistic::getAverageChatduration(30, $filter);
        $statsArray['avg_chat_duration'] = $durationAverage;
    }

    if (in_array('avg_wait_time',$valid)) {

        echo "Calculating `avg_wait_time` for department {$department->id}\n";

        // Average wait time
        $filter = array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT, 'dep_id' => $department->id), 'filterlt' => array('wait_time' => 600), 'filtergt' => array('wait_time' => 0));

        if (isset($configuration['avg_wait_time']) && $configuration['avg_chat_duration'] > 0) {
            $filter['filtergte']['time'] = time() - $configuration['avg_chat_duration'];
        } else {
            $filter['filtergte']['time'] = (time() - (259200 * 50));
        }

        // Store average wait time in seconds
        $statsArray['avg_wait_time'] = (int)erLhcoreClassChat::getCount($filter, 'lh_chat', 'AVG(wait_time)');
    }

    // Updates stats
    $stats->stats_array = $statsArray;
    $stats->stats = json_encode($stats->stats_array);
    $stats->lupdate = time();
    $stats->saveThis();
}

?>