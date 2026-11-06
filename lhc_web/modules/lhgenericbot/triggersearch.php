<?php

header('content-type: application/json; charset=utf-8');

$bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

$db = ezcDbInstance::get();

$depId = (isset($_GET['dep_id']) && is_numeric($_GET['dep_id']) && (int)$_GET['dep_id'] > 0) ? (int)$_GET['dep_id'] : null;

$triggersPayload = [];

if ($depId !== null) {

    // 1. Trigger event configuration - "dep_inc" and "dep_exc" E.g {"dep_inc":"1,2","dep_exc":"4,5"}
    foreach (erLhcoreClassModelGenericBotTriggerEvent::getList(['limit' => false, 'filterin' => array('bot_id' => $bot->getBotIds()), 'filterlike' => array('configuration' => 'dep_')]) as $triggerEvent) {

        $configuration = $triggerEvent->configuration_array;

        if (!is_array($configuration)) {
            continue;
        }

        foreach (['dep_inc', 'dep_exc'] as $depKey) {
            if (isset($configuration[$depKey]) && $configuration[$depKey] !== '') {
                $depIds = explode(',', str_replace(' ', '', $configuration[$depKey]));
                if (in_array((string)$depId, $depIds)) {
                    $triggersPayload[] = (int)$triggerEvent->trigger_id;
                    break;
                }
            }
        }
    }

    // 2. Trigger actions - "setdepartment" payload, "setchatattribute" with dep_id payload and conditions comparing department
    $actionsSQL = '`actions` LIKE ' . $db->quote('%setdepartment%') . ' OR `actions` LIKE ' . $db->quote('%dep_id%') . ' OR `actions` LIKE ' . $db->quote('%online_department%');

    foreach (erLhcoreClassModelGenericBotTrigger::getList(['limit' => false, 'filterin' => array('bot_id' => $bot->getBotIds()), 'customfilter' => ['(' . $actionsSQL . ')']]) as $trigger) {

        foreach ($trigger->actions_front as $action) {

            if (!isset($action['content']) || !is_array($action['content'])) {
                continue;
            }

            $content = $action['content'];
            $command = isset($content['command']) ? (string)$content['command'] : '';

            // 2.1 Change department of chat/mail
            if ($command == 'setdepartment' && isset($content['payload']) && is_numeric($content['payload']) && (int)$content['payload'] == $depId) {
                $triggersPayload[] = (int)$trigger->id;
                break;
            }

            // 2.2 Update main chat attribute where dep_id is changed
            if ($command == 'setchatattribute' && isset($content['payload']) && $content['payload'] == 'dep_id' && isset($content['payload_arg']) && is_numeric($content['payload_arg']) && (int)$content['payload_arg'] == $depId) {
                $triggersPayload[] = (int)$trigger->id;
                break;
            }

            if (isset($content['conditions']) && is_array($content['conditions'])) {

                $depConditionFound = false;

                foreach ($content['conditions'] as $condition) {

                    if (!isset($condition['content']['attr'])) {
                        continue;
                    }

                    $attr = (string)$condition['content']['attr'];
                    $val = isset($condition['content']['val']) ? (string)$condition['content']['val'] : '';

                    // 2.3 online_department__1,2 / online_department_hours__1,2 / online_op_department__1,2
                    $attrParts = explode('__', $attr, 2);
                    if (count($attrParts) == 2 && in_array($attrParts[0], ['online_department', 'online_department_hours', 'online_op_department'])) {
                        if (in_array((string)$depId, explode(',', str_replace(' ', '', $attrParts[1])))) {
                            $depConditionFound = true;
                            break;
                        }
                    }

                    // 2.4 Direct comparison with chat department E.g lhc.dep_id == 4 or in_list 1||4||5
                    if ($attr == 'lhc.dep_id' || $attr == 'dep_id') {
                        $depValues = preg_split('/[^0-9]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
                        if (in_array((string)$depId, $depValues)) {
                            $depConditionFound = true;
                            break;
                        }
                    }
                }

                if ($depConditionFound == true) {
                    $triggersPayload[] = (int)$trigger->id;
                    break;
                }
            }
        }
    }

    // 3. Bot commands assigned to the department
    foreach (erLhcoreClassModelGenericBotCommand::getList(['limit' => false, 'filter' => array('dep_id' => $depId), 'filterin' => array('bot_id' => $bot->getBotIds())]) as $command) {
        $triggersPayload[] = (int)$command->trigger_id;
    }

    $triggersPayload = array_values(array_unique(array_filter($triggersPayload)));

    if (empty($triggersPayload)) {
        echo json_encode([]);
        exit;
    }

    $customSQL = '`id` IN (' . implode(', ', $triggersPayload) . ')';

} else {

    $customSQL = '`name` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ') OR `actions` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ')';

    if (isset($_GET['include_translations'])) {
        // Search in translation strings and search by identifiers
        $translations = erLhcoreClassModelGenericBotTrItem::getList(['customfilter' => ['`translation` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ')']]);
        $keywordsTranslations = [];
        foreach ($translations as $translation) {
            $customSQL .= ' OR `actions` LIKE (' . $db->quote('%{' . $translation->identifier . '__%') . ')';
        }
    }

    foreach (erLhcoreClassModelGenericBotTriggerEvent::getList(['filterin' => array('bot_id' => $bot->getBotIds()), 'customfilter' => ['`pattern` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ') OR `pattern_exc` LIKE (' . $db->quote('%' . $_GET['keyword'] . '%') . ')']]) as $triggerEvent) {
        $triggersPayload[] = $triggerEvent->trigger_id;
    }

    if (!empty($triggersPayload)) {
        $customSQL .= ' OR `id` IN (' . implode(', ', $triggersPayload) . ')';
    }
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