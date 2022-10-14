<?php

$report = \LiveHelperChat\Models\Statistic\SavedReport::fetch((int)$Params['user_parameters']['report_id']);

header('Location: '.$report->generateURL());
exit;

?>