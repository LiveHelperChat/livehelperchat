<?php

header('content-type: application/json; charset=utf-8');

$bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

$db = ezcDbInstance::get();

$customSQL = '`name` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ') OR `actions` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ')';

if (isset($_GET['include_translations'])) {
    // Search in translation strings and search by identifiers
    $translations = erLhcoreClassModelGenericBotTrItem::getList(['customfilter' => ['`translation` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ')']]);
    $keywordsTranslations = [];
    foreach ($translations as $translation) {
        $customSQL .= ' OR `actions` LIKE (' . $db->quote('%{' . $translation->identifier . '__%') . ')';
    }
}

$triggersPayload = [];
foreach (erLhcoreClassModelGenericBotTriggerEvent::getList(['filterin' => array('bot_id' => $bot->getBotIds()), 'customfilter' => ['`pattern` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ') OR `pattern_exc` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ')']]) as $triggerEvent) {
    $triggersPayload[] = $triggerEvent->trigger_id;
}

if (!empty($triggersPayload)) {
    $customSQL .= ' OR `id` IN (' . implode(', ', $triggersPayload) . ')';
}

$triggers = array_values(erLhcoreClassModelGenericBotTrigger::getList(array('sort' => '`group_id` ASC, `pos` ASC, `id` ASC', 'ignore_fields' => ['actions'], 'customfilter' => ['(' . $customSQL . ')'], 'filterin' => array('bot_id' => $bot->getBotIds()))));

foreach ($triggers as & $trigger) {
    $trigger->group_name = (string)erLhcoreClassModelGenericBotGroup::fetch($trigger->group_id)->name;
}

echo json_encode(
    $triggers
);

exit;
?>