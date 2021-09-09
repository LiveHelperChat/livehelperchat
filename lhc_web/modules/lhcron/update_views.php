<?php
/**
 * php cron.php -s site_admin -c cron/update_views
 *
 * Run every 1/2 minutes or so. On this cron depends views number of records counter
 *
 * */

foreach (erLhAbstractModelSavedSearch::getList([
    'filterlt' => ['updated_at' => time() - 2 * 60],  // Only views which was updated more than 2 minutes ago
    'filtergt' => ['requested_at' => time() - 5 * 60] // Only views where operator requested update during last 5 minutes
]) as $search) {

    $filterSearch = $search->params_array['filter'];

    if ($search->days > 0) {
        $filterSearch['filtergte']['time'] = time() - $search->days * 24 * 3600;
    }

    $totalRecords =  erLhcoreClassModelChat::getCount($filterSearch);

    if ($search->total_records != $totalRecords){
        $search->updated_at = time();
        $search->total_records = $totalRecords;
        $search->updateThis(['update' => ['updated_at','total_records']]);
    }

}

?>