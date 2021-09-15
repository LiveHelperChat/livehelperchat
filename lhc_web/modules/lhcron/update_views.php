<?php
/**
 * php cron.php -s site_admin -c cron/update_views
 *
 * Run every 1/2 minutes or so. On this cron depends view number of records counter
 *
 * */

foreach (erLhAbstractModelSavedSearch::getList([
    'limit' => false,
    'filter' => ['passive' => 0],
    'filterlt' => ['updated_at' => time() - 2 * 60],  // Only views which was updated more than 2 minutes ago
    'filtergt' => ['requested_at' => time() - 5 * 60] // Only views where operator requested update during last 5 minutes
]) as $search) {
    if (class_exists('erLhcoreClassExtensionLhcphpresque')) {
        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_views_update', 'erLhcoreClassViewResque', array('view_id' => $search->id));
    } else {
        erLhcoreClassViewResque::updateView($search);
    }
}

?>