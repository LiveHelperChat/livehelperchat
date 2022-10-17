<?php

$report = new LiveHelperChat\Models\Statistic\SavedReport();

$report->params_array = ['input_form' => [
    'timefrom_hours' => $_POST['timefrom_hours'],
    'timefrom_minutes' => $_POST['timefrom_minutes'],
    'timefrom_seconds' => $_POST['timefrom_seconds'],
    'timeto_hours' => $_POST['timeto_hours'],
    'timeto_minutes' => $_POST['timeto_minutes'],
    'timeto_seconds' => $_POST['timeto_seconds'],
]];
$report->date_type = $_POST['date_type'];
$report->days = $_POST['days'];
$report->days_end = $_POST['days_end'];

$paramsFormatted = $report->getParamsURL();

$outputString = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel', 'From') . ' ' .$paramsFormatted['input_form']['timefrom']
    . ' ' . str_pad((int)$paramsFormatted['input_form']['timefrom_hours'],2, '0', STR_PAD_LEFT) .' h.'
    . ' ' . str_pad((int)$paramsFormatted['input_form']['timefrom_minutes'],2, '0', STR_PAD_LEFT) .' m.'
    . ' ' . str_pad((int)$paramsFormatted['input_form']['timefrom_seconds'],2, '0', STR_PAD_LEFT) .' s.';
if ($paramsFormatted['input_form']['timeto'] != ''){
    $outputString .= ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel', 'Till') . ' ' . $paramsFormatted['input_form']['timeto']
    . ' ' . str_pad((int)$paramsFormatted['input_form']['timeto_hours'],2, '0', STR_PAD_LEFT) .' h.'
    . ' ' . str_pad((int)$paramsFormatted['input_form']['timeto_minutes'],2, '0', STR_PAD_LEFT) .' m.'
    . ' ' . str_pad((int)$paramsFormatted['input_form']['timeto_seconds'],2, '0', STR_PAD_LEFT) .' s.' ;
} else {
    $outputString .= ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel', 'Till now');
}

echo $outputString;

exit;
?>