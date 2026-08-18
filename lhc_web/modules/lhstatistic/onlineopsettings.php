<?php

$tpl = erLhcoreClassTemplate::getInstance('lhstatistic/onlineopsettings.tpl.php');

$identifier = 'online_op_columns';
$defaultColumns = array('name', 'status', 'last_assignment', 'capacity', 'department', 'offline_reason', 'offline_since');
$columnTranslations = array(
	'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'Name'),
	'status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'Status'),
	'last_assignment' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'Last assignment'),
	'capacity' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'Live chats / free slots'),
	'department' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'Department'),
	'offline_reason' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'Reason for offline'),
	'offline_since' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets', 'Session duration')
);
$defaultColumnOrder = array_flip($defaultColumns);

$storedColumns = json_decode(erLhcoreClassModelUserSetting::getSetting($identifier, json_encode($defaultColumns)), true);
$selectedColumns = is_array($storedColumns) ? array_values(array_unique(array_intersect($storedColumns, $defaultColumns))) : array();

if (empty($selectedColumns)) {
	$selectedColumns = $defaultColumns;
}

$positions = array();
foreach ($defaultColumns as $index => $columnIdentifier) {
	$position = array_search($columnIdentifier, $selectedColumns, true);
	$positions[$columnIdentifier] = $position === false ? ($index + 1) : ($position + 1);
}

if (ezcInputForm::hasPostData()) {
	$postedColumns = isset($_POST['online_op_columns']) && is_array($_POST['online_op_columns']) ? $_POST['online_op_columns'] : array();
	$postedColumns = array_values(array_unique(array_intersect($postedColumns, $defaultColumns)));
	$postedPositions = isset($_POST['online_op_position']) && is_array($_POST['online_op_position']) ? $_POST['online_op_position'] : array();

	foreach ($defaultColumns as $index => $columnIdentifier) {
		$position = isset($postedPositions[$columnIdentifier]) ? (int)$postedPositions[$columnIdentifier] : ($index + 1);
		$positions[$columnIdentifier] = $position > 0 ? $position : ($index + 1);
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

	erLhcoreClassModelUserSetting::setSetting($identifier, json_encode($selectedColumns));
	$tpl->set('updated', true);
}

$columnsForTemplate = array();
foreach ($defaultColumns as $index => $columnIdentifier) {
	$columnsForTemplate[] = array(
		'identifier' => $columnIdentifier,
		'enabled' => in_array($columnIdentifier, $selectedColumns),
		'position' => $positions[$columnIdentifier],
		'translation' => $columnTranslations[$columnIdentifier]
	);
}

usort($columnsForTemplate, function($columnA, $columnB) use ($defaultColumnOrder) {
	if ($columnA['position'] === $columnB['position']) {
		return $defaultColumnOrder[$columnA['identifier']] <=> $defaultColumnOrder[$columnB['identifier']];
	}

	return $columnA['position'] <=> $columnB['position'];
});

$tpl->set('columnsForTemplate', $columnsForTemplate);

echo $tpl->fetch();
exit();