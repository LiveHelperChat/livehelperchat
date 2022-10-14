<?php

$report = \LiveHelperChat\Models\Statistic\SavedReport::fetch($Params['user_parameters']['report_id']);

$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

if ($currentUser->getUserID() == $report->user_id) {
    $report->removeThis();
}

echo "ok";
exit;

?>