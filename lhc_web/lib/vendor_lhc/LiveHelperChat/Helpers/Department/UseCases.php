<?php

namespace LiveHelperChat\Helpers\Department;

/**
 * Collects places where the department is explicitly chosen in configuration
 * */
class UseCases
{
    /**
     * Returns places where department is explicitly chosen in configuration
     *
     * @param \erLhcoreClassModelDepartament $department
     * @return array
     */
    public static function getUseCases(\erLhcoreClassModelDepartament $department)
    {
        $useCases = array();

        // Auto responders - single link to the auto responders list filtered by this department
        $autoResponderIds = \erLhAbstractModelAutoResponder::getDepartmentRelatedIds(array($department->id));

        if (!empty($autoResponderIds)) {
            self::addUseCasesItem(
                $useCases,
                'auto_responder',
                \erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Auto responders') . ' (' . count($autoResponderIds) . ')',
                'abstract/list/AutoResponder/(dep_id)/' . $department->id
            );
        }

        // Proactive invitations
        $items = \erLhAbstractModelProactiveChatInvitation::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('dep_id' => $department->id)));
        $relatedIds = self::getUseCasesRelatedIds('lh_abstract_proactive_chat_invitation_dep', 'invitation_id', $department->id);
        if (!empty($relatedIds)) {
            $items = array_merge($items, \erLhAbstractModelProactiveChatInvitation::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))));
        }
        foreach ($items as $item) {
            self::addUseCasesItem($useCases, 'proactive_invitation', $item->name, 'abstract/edit/ProactiveChatInvitation/' . $item->id, $item);
        }

        // Canned messages - single link to the canned messages list filtered by this department
        $stmt = \ezcDbInstance::get()->prepare('SELECT COUNT(*) FROM (SELECT `id` FROM `lh_canned_msg` WHERE `department_id` = :dep_id UNION SELECT `lh_canned_msg`.`id` FROM `lh_canned_msg_dep` INNER JOIN `lh_canned_msg` ON `lh_canned_msg`.`id` = `lh_canned_msg_dep`.`canned_id` WHERE `lh_canned_msg_dep`.`dep_id` = :related_dep_id) AS `canned_messages`');
        $stmt->bindValue(':dep_id', (int)$department->id, \PDO::PARAM_INT);
        $stmt->bindValue(':related_dep_id', (int)$department->id, \PDO::PARAM_INT);
        $stmt->execute();
        $cannedMsgCount = (int)$stmt->fetchColumn();

        if ($cannedMsgCount > 0) {
            self::addUseCasesItem(
                $useCases,
                'canned_msg',
                \erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Canned messages') . ' (' . $cannedMsgCount . ')',
                'chat/cannedmsg/(department_id)/' . $department->id
            );
        }

        // Canned message replaceable variables with department conditions
        foreach (\erLhcoreClassModelCannedMsgReplace::getList(array('sort' => 'id ASC', 'limit' => false)) as $item) {
            $isUsed = false;

            foreach ($item->conditions_array as $condition) {
                if (
                    (isset($condition['dep_id']) && (int)$condition['dep_id'] == (int)$department->id) ||
                    (isset($condition['dep_ids']) && is_array($condition['dep_ids']) && in_array((string)$department->id, array_map('strval', $condition['dep_ids']), true))
                ) {
                    $isUsed = true;
                    break;
                }
            }

            if ($isUsed) {
                self::addUseCasesItem($useCases, 'canned_msg_replace', '{' . $item->identifier . '}', 'cannedmsg/editreplace/' . $item->id, $item);
            }
        }

        // Chat variables
        foreach (\erLhAbstractModelChatVariable::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('dep_id' => $department->id))) as $item) {
            self::addUseCasesItem($useCases, 'chat_variable', $item->var_name, 'abstract/edit/ChatVariable/' . $item->id, $item);
        }

        // Chat priorities
        foreach (\erLhAbstractModelChatPriority::getList(array('sort' => 'id ASC', 'limit' => false, 'filterlor' => array('dep_id' => array($department->id), 'dest_dep_id' => array($department->id)))) as $item) {
            self::addUseCasesItem($useCases, 'chat_priority', '[' . $item->id . '] ' . $item->value, 'abstract/edit/ChatPriority/' . $item->id, $item);
        }

        // Content chunks
        $relatedIds = self::getUseCasesRelatedIds('lh_abstract_content_chunk_dep', 'chunk_id', $department->id);
        if (!empty($relatedIds)) {
            foreach (\LiveHelperChat\Models\LHCAbstract\ContentChunk::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))) as $item) {
                self::addUseCasesItem($useCases, 'content_chunk', $item->name, 'abstract/edit/ContentChunk/' . $item->id, $item);
            }
        }

        // Subjects
        $relatedIds = self::getUseCasesRelatedIds('lh_abstract_subject_dep', 'subject_id', $department->id);
        if (!empty($relatedIds)) {
            foreach (\erLhAbstractModelSubject::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))) as $item) {
                self::addUseCasesItem($useCases, 'subject', $item->name, 'abstract/edit/Subject/' . $item->id, $item);
            }
        }

        // Message content protection rules
        $stmt = \ezcDbInstance::get()->prepare("SELECT `id` FROM `lh_abstract_msg_protection` WHERE `has_dep` = 1 AND JSON_CONTAINS(`dep_ids`, :dep_id, '$')");
        $stmt->bindValue(':dep_id', (string)(int)$department->id, \PDO::PARAM_STR);
        $stmt->execute();
        $relatedIds = array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
        if (!empty($relatedIds)) {
            foreach (\LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))) as $item) {
                self::addUseCasesItem($useCases, 'msg_protection', $item->name, 'abstract/edit/ChatMessagesGhosting/' . $item->id, $item);
            }
        }

        // Products
        $items = \erLhAbstractModelProduct::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('departament_id' => $department->id)));
        $relatedIds = self::getUseCasesRelatedIds('lh_abstract_product_departament', 'product_id', $department->id, 'departament_id');
        if (!empty($relatedIds)) {
            $items = array_merge($items, \erLhAbstractModelProduct::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))));
        }
        foreach ($items as $item) {
            self::addUseCasesItem($useCases, 'product', $item->name, 'abstract/edit/Product/' . $item->id, $item);
        }

        // Mailboxes
        foreach (\erLhcoreClassModelMailconvMailbox::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('dep_id' => $department->id))) as $item) {
            self::addUseCasesItem($useCases, 'mailbox', $item->mail, 'mailconv/editmailbox/' . $item->id, $item);
        }

        // Mail matching rules
        foreach (\erLhcoreClassModelMailconvMatchRule::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('dep_id' => $department->id))) as $item) {
            self::addUseCasesItem($useCases, 'mail_match_rule', $item->name != '' ? $item->name : $item->display_name, 'mailconv/editmatchrule/' . $item->id, $item);
        }

        // Mail response templates
        $items = \erLhcoreClassModelMailconvResponseTemplate::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('dep_id' => $department->id)));
        $relatedIds = self::getUseCasesRelatedIds('lhc_mailconv_response_template_dep', 'template_id', $department->id);
        if (!empty($relatedIds)) {
            $items = array_merge($items, \erLhcoreClassModelMailconvResponseTemplate::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))));
        }
        foreach ($items as $item) {
            self::addUseCasesItem($useCases, 'mail_response_template', $item->name, 'mailconv/editresponsetemplate/' . $item->id, $item);
        }

        // Incoming webhooks - single link to the incoming webhooks list filtered by this department
        $stmt = \ezcDbInstance::get()->prepare('SELECT COUNT(*) FROM `lh_incoming_webhook` WHERE `dep_id` = :dep_id');
        $stmt->bindValue(':dep_id', (int)$department->id, \PDO::PARAM_INT);
        $stmt->execute();
        $incomingWebhookCount = (int)$stmt->fetchColumn();

        if ($incomingWebhookCount > 0) {
            self::addUseCasesItem(
                $useCases,
                'incoming_webhook',
                \erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments', 'Incoming webhooks') . ' (' . $incomingWebhookCount . ')',
                'webhooks/incomingwebhooks/(department_ids)/' . $department->id
            );
        }

        // Bot commands
        foreach (\erLhcoreClassModelGenericBotCommand::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('dep_id' => $department->id))) as $item) {
            self::addUseCasesItem($useCases, 'bot_command', $item->name . ' | ' . $item->command, 'genericbot/editcommand/' . $item->id, $item);
        }

        // Bot trigger events available/disabled for this department (stored in JSON configuration)
        foreach (\erLhcoreClassModelGenericBotTriggerEvent::getList(array('sort' => 'id ASC', 'limit' => false, 'filterlike' => array('configuration' => 'dep_'))) as $item) {
            $configuration = $item->configuration_array;

            if (!is_array($configuration)) {
                continue;
            }

            $isUsed = false;

            foreach (array('dep_inc', 'dep_exc') as $depKey) {
                if (isset($configuration[$depKey]) && $configuration[$depKey] !== '') {
                    $depIds = explode(',', str_replace(' ', '', (string)$configuration[$depKey]));
                    if (in_array((string)$department->id, $depIds, true)) {
                        $isUsed = true;
                        break;
                    }
                }
            }

            if ($isUsed) {
                $trigger = \erLhcoreClassModelGenericBotTrigger::fetch($item->trigger_id);

                if ($trigger instanceof \erLhcoreClassModelGenericBotTrigger) {
                    self::addUseCasesItem($useCases, 'bot_trigger', self::getUseCasesBotTriggerName($trigger), 'genericbot/bot/' . $trigger->bot_id . '#!#/trigger-' . $trigger->id);
                }
            }
        }

        // Bot trigger actions that set or check this department (stored in JSON actions)
        $db = \ezcDbInstance::get();

        $botActionsFilter = '`actions` LIKE ' . $db->quote('%setdepartment%') . ' OR `actions` LIKE ' . $db->quote('%dep_id%') . ' OR `actions` LIKE ' . $db->quote('%online_department%') . ' OR `actions` LIKE ' . $db->quote('%online_op_department%');

        foreach (\erLhcoreClassModelGenericBotTrigger::getList(array('sort' => 'id ASC', 'limit' => false, 'customfilter' => array('(' . $botActionsFilter . ')'))) as $trigger) {
            if (self::isDepartmentUsedInBotActions($trigger->actions_front, $department->id)) {
                self::addUseCasesItem($useCases, 'bot_trigger', self::getUseCasesBotTriggerName($trigger), 'genericbot/bot/' . $trigger->bot_id . '#!#/trigger-' . $trigger->id);
            }
        }

        foreach (\LiveHelperChat\Models\Bot\Condition::getList(array('sort' => 'id ASC', 'limit' => false)) as $item) {
            $conditions = json_decode($item->configuration, true);
            if (!is_array($conditions)) {
                continue;
            }
            foreach ($conditions as $condition) {
                if (isset($condition['field']) && self::isDepartmentUsedInCondition($condition['field'], $condition['value'] ?? '', $department->id)) {
                    self::addUseCasesItem($useCases, 'bot_condition', $item->name, 'genericbot/editcondition/' . $item->id, $item);
                    break;
                }
            }
        }

        // Chat start settings
        $items = \erLhcoreClassModelChatStartSettings::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('department_id' => $department->id)));
        $stmt = \ezcDbInstance::get()->prepare("SELECT `id` FROM `lh_chat_start_settings` WHERE CASE WHEN JSON_VALID(`dep_ids`) THEN JSON_CONTAINS(`dep_ids`, :dep_id, '$') ELSE 0 END");
        $stmt->bindValue(':dep_id', (string)(int)$department->id, \PDO::PARAM_STR);
        $stmt->execute();
        $relatedIds = array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
        if (!empty($relatedIds)) {
            $items = array_merge($items, \erLhcoreClassModelChatStartSettings::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))));
        }
        foreach ($items as $item) {
            self::addUseCasesItem($useCases, 'chat_start_settings', $item->name, 'chatsettings/editstartsettings/' . $item->id, $item);
        }

        foreach (\erLhcoreClassModelChatStartSettings::getList(array('sort' => 'id ASC', 'limit' => false)) as $item) {
            if (self::isDepartmentUsedInStartFields($item->data_array, $department->id, $department->alias)) {
                self::addUseCasesItem($useCases, 'chat_start_settings', $item->name, 'chatsettings/editstartsettings/' . $item->id, $item);
            }
        }

        // Chat event tracking
        foreach (\erLhcoreClassModelChatEventTrack::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('department_id' => $department->id))) as $item) {
            self::addUseCasesItem($useCases, 'chat_event_track', $item->name, 'chatsettings/editeventsettings/' . $item->id, $item);
        }

        // Department groups
        $relatedIds = self::getUseCasesRelatedIds('lh_departament_group_member', 'dep_group_id', $department->id);
        if (!empty($relatedIds)) {
            foreach (\erLhcoreClassModelDepartamentGroup::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))) as $item) {
                self::addUseCasesItem($useCases, 'department_group', $item->name, 'department/editgroup/' . $item->id, $item);
            }
        }

        // Department limit groups
        $relatedIds = self::getUseCasesRelatedIds('lh_departament_limit_group_member', 'dep_limit_group_id', $department->id);
        if (!empty($relatedIds)) {
            foreach (\erLhcoreClassModelDepartamentLimitGroup::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))) as $item) {
                self::addUseCasesItem($useCases, 'department_limit_group', $item->name, 'department/editlimitgroup/' . $item->id, $item);
            }
        }

        // Brands
        $relatedIds = self::getUseCasesRelatedIds('lh_brand_member', 'brand_id', $department->id);
        if (!empty($relatedIds)) {
            foreach (\LiveHelperChat\Models\Brand\Brand::getList(array('sort' => 'id ASC', 'limit' => false, 'filterin' => array('id' => $relatedIds))) as $item) {
                self::addUseCasesItem($useCases, 'brand', $item->name, 'department/editbrand/' . $item->id, $item);
            }
        }

        // Departments which transfer chats to this department
        foreach (\erLhcoreClassModelDepartament::getList(array('sort' => 'id ASC', 'limit' => false, 'filter' => array('department_transfer_id' => $department->id))) as $item) {
            if ($item->id == $department->id) {
                continue;
            }
            self::addUseCasesItem($useCases, 'transfer_destination', $item->name, 'department/edit/' . $item->id, $item);
        }

        return array_values($useCases);
    }

    private static function addUseCasesItem(& $useCases, $type, $name, $url, $item = null)
    {
        $key = $type . '|' . $url;

        if (isset($useCases[$key])) {
            return;
        }

        $useCases[$key] = array(
            'type' => $type,
            'name' => (string)$name,
            'url' => \erLhcoreClassDesign::baseurl($url),
            'item' => $item
        );
    }

    private static function getUseCasesRelatedIds($table, $column, $departmentId, $filterColumn = 'dep_id')
    {
        $db = \ezcDbInstance::get();

        $stmt = $db->prepare("SELECT `" . $column . "` FROM `" . $table . "` WHERE `" . $filterColumn . "` = :department_id");
        $stmt->bindValue(':department_id', (int)$departmentId, \PDO::PARAM_INT);
        $stmt->execute();

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    private static function getUseCasesBotTriggerName($trigger)
    {
        if (!($trigger instanceof \erLhcoreClassModelGenericBotTrigger)) {
            return '';
        }

        $bot = \erLhcoreClassModelGenericBotBot::fetch($trigger->bot_id);

        return ($bot instanceof \erLhcoreClassModelGenericBotBot ? $bot->name . ' -> ' : '') . $trigger->name;
    }

    /**
     * Checks does bot trigger actions explicitly set or check department
     * */
    private static function isDepartmentUsedInBotActions($actions, $departmentId)
    {
        if (!is_array($actions)) {
            return false;
        }

        foreach ($actions as $action) {
            if (!isset($action['content']) || !is_array($action['content'])) {
                continue;
            }

            $content = $action['content'];
            $command = isset($content['command']) ? (string)$content['command'] : '';

            // Change department of chat/mail
            if ($command == 'setdepartment' && isset($content['payload']) && is_numeric($content['payload']) && (int)$content['payload'] == (int)$departmentId) {
                return true;
            }

            // Update main chat attribute where dep_id is changed
            if ($command == 'setchatattribute' && isset($content['payload']) && $content['payload'] == 'dep_id' && isset($content['payload_arg']) && is_numeric($content['payload_arg']) && (int)$content['payload_arg'] == (int)$departmentId) {
                return true;
            }

            if (isset($content['conditions']) && is_array($content['conditions'])) {
                foreach ($content['conditions'] as $condition) {
                    if (!isset($condition['content']['attr'])) {
                        continue;
                    }

                    if (self::isDepartmentUsedInCondition($condition['content']['attr'], $condition['content']['val'] ?? '', $departmentId)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private static function isDepartmentUsedInCondition($attr, $value, $departmentId)
    {
        $attrParts = explode('__', (string)$attr, 2);
        if (count($attrParts) == 2 && in_array($attrParts[0], array('online_department', 'online_department_hours', 'online_op_department')) && in_array((string)$departmentId, explode(',', str_replace(' ', '', $attrParts[1])), true)) {
            return true;
        }

        if ($attr == 'lhc.dep_id' || $attr == 'dep_id') {
            $depValues = preg_split('/[^0-9]+/', (string)$value, -1, PREG_SPLIT_NO_EMPTY);
            return in_array((string)$departmentId, $depValues, true);
        }

        return false;
    }

    private static function isDepartmentUsedInStartFields($data, $departmentId, $departmentAlias)
    {
        $fields = isset($data['custom_fields']) ? json_decode($data['custom_fields'], true) : null;
        if (!is_array($fields)) {
            return false;
        }

        foreach ($fields as $field) {
            if (($field['fieldtype'] ?? '') != 'dropdown' || !isset($field['options']) || !is_string($field['options'])) {
                continue;
            }
            foreach (explode("\n", $field['options']) as $option) {
                $decoded = json_decode($option, true);
                if (is_array($decoded)) {
                    $depValue = $decoded['dep_id'] ?? null;
                } else {
                    $parts = explode('=>', $option, 2);
                    $parts = explode('||', trim(end($parts)), 2);
                    $depValue = isset($parts[1]) ? trim($parts[1]) : null;
                }
                if (is_scalar($depValue) && ((is_numeric($depValue) && (int)$depValue == (int)$departmentId) || ($departmentAlias !== '' && (string)$depValue === (string)$departmentAlias))) {
                    return true;
                }
            }
        }

        return false;
    }
}
