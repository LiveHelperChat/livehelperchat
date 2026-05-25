<?php
$tpl = erLhcoreClassTemplate::getInstance('lhstatistic/performancesettings.tpl.php');

if ($Params['user_parameters']['scope'] === 'op') {
    $identifier = 'statistic_performance_op';
    $defaultColumns = array('ton', 'toff', 'ca', 'frt', 'aart', 'tup', 'tdown');
    $columnTranslations = array(
        'toff' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Total Online Time'),
        'ton' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Total Offline Time'),
        'ca' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Chats answered'),
        'frt' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','First response time (Agent)'),
        'aart' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Average response time (Agent)'),
        'tup' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs Up'),
        'tdown' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs Down')
    );
    $tpl->set('scope','op');
} else {
    $identifier = 'statistic_performance';
    $defaultColumns = array('cr', 'ca', 'wt', 'frt', 'aart', 'tup', 'tdown');
    $columnTranslations = array(
        'cr' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Chats received'),
        'ca' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Chats answered'),
        'wt' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Wait time'),
        'frt' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','First response time'),
        'aart' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Average response time'),
        'tup' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs up'),
        'tdown' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Thumbs down')
    );
}

$defaultColumnOrder = array_flip($defaultColumns);

$validUpdateIntervals = \LiveHelperChat\Models\Statistic\PerformanceWidgets::VALID_UPDATE_INTERVALS;
$storedConfig = (array)erLhcoreClassModelChatConfig::fetch($identifier)->data;

$wrapHeaders = isset($storedConfig['wrap_headers']) ? (bool)$storedConfig['wrap_headers'] : false;

$selectedColumns = array_values(array_intersect(
    isset($storedConfig['columns']) && is_array($storedConfig['columns']) ? $storedConfig['columns'] : $defaultColumns,
    $defaultColumns
));

if (empty($selectedColumns)) {
    $selectedColumns = $defaultColumns;
}

$positions = isset($storedConfig['positions']) && is_array($storedConfig['positions']) ? $storedConfig['positions'] : array();
$updateInterval = isset($storedConfig['update_interval']) && in_array((int)$storedConfig['update_interval'], $validUpdateIntervals) ? (int)$storedConfig['update_interval'] : 600;

if (ezcInputForm::hasPostData()) {

    $postedColumns = isset($_POST['dep_performance_columns']) && is_array($_POST['dep_performance_columns']) ? $_POST['dep_performance_columns'] : array();
    $postedColumns = array_values(array_intersect($postedColumns, $defaultColumns));

    $postedPositions = isset($_POST['dep_performance_position']) && is_array($_POST['dep_performance_position']) ? $_POST['dep_performance_position'] : array();

    $postedInterval = isset($_POST['dep_performance_update_interval']) ? (int)$_POST['dep_performance_update_interval'] : 600;
    if (!in_array($postedInterval, \LiveHelperChat\Models\Statistic\PerformanceWidgets::VALID_UPDATE_INTERVALS)) {
        $postedInterval = 600;
    }

    $positions = array();
    foreach ($defaultColumns as $index => $columnIdentifier) {
        $positionValue = isset($postedPositions[$columnIdentifier]) ? (int)$postedPositions[$columnIdentifier] : ($index + 1);
        if ($positionValue <= 0) {
            $positionValue = $index + 1;
        }
        $positions[$columnIdentifier] = $positionValue;
    }

    if (!empty($postedColumns)) {
        usort($postedColumns, function($columnA, $columnB) use ($positions, $defaultColumnOrder) {
            if ($positions[$columnA] === $positions[$columnB]) {
                return $defaultColumnOrder[$columnA] <=> $defaultColumnOrder[$columnB];
            }

            return $positions[$columnA] <=> $positions[$columnB];
        });
        $selectedColumns = $postedColumns;
    } else {
        $selectedColumns = $defaultColumns;
    }

    $updateInterval = $postedInterval;

    $postedWrap = isset($_POST['dep_performance_wrap_headers']) && $_POST['dep_performance_wrap_headers'] == '1' ? true : false;

    $configRecord = erLhcoreClassModelChatConfig::fetch($identifier);
    $configRecord->identifier = $identifier;
    $configRecord->value = serialize(array(
        'columns'         => $selectedColumns,
        'positions'       => $positions,
        'update_interval' => $updateInterval,
        'wrap_headers'    => $postedWrap,
    ));
    $configRecord->type = 0;
    $configRecord->hidden = 1;
    $configRecord->explain = 'ignore';
    $configRecord->saveThis();

    $tpl->set('updated', true);
}

$columnsForTemplate = array();
foreach ($defaultColumns as $index => $columnIdentifier) {
    $columnsForTemplate[] = array(
        'identifier' => $columnIdentifier,
        'enabled' => in_array($columnIdentifier, $selectedColumns),
        'position' => isset($positions[$columnIdentifier]) && is_numeric($positions[$columnIdentifier]) ? (int)$positions[$columnIdentifier] : ($index + 1),
        'translation' => $columnTranslations[$columnIdentifier]
    );
}

usort($columnsForTemplate, function($columnA, $columnB) use ($defaultColumnOrder) {
    if ($columnA['position'] === $columnB['position']) {
        return $defaultColumnOrder[$columnA['identifier']] <=> $defaultColumnOrder[$columnB['identifier']];
    }

    return $columnA['position'] <=> $columnB['position'];
});

$tpl->setArray(array(
    'columnsForTemplate'  => $columnsForTemplate,
    'updateInterval'      => $updateInterval,
    'validUpdateIntervals' => $validUpdateIntervals,
    'wrapHeaders'         => $wrapHeaders,
));

echo $tpl->fetch();
exit();

?>