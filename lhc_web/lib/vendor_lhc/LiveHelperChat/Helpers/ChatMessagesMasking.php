<?php

namespace LiveHelperChat\Helpers;

/**
 * Simulates the real masking flow used by the widget (fetchmessages.php -> ChatMessagesGhosting::maskVisitorMessages())
 * and returns step by step diagnostics explaining why a message would or would not be masked for a given chat.
 */
class ChatMessagesMasking
{
    private static $maskRulesByType = [];

    /**
     * @param \erLhcoreClassModelChat|false $chat Chat to take department, assigned operator and its permissions from
     * @param string $testMessage Message text, treated as a visitor (user_id = 0) message
     * @param int $type Masking rule type, defaults to visitor -> operator (MSG_TYPE_VISITOR_TO_OPERATOR)
     * @return array
     */
    public static function testMaskingForChat($chat, $testMessage, $type = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_VISITOR_TO_OPERATOR)
    {
        $translator = \erTranslationClassLhTranslation::getInstance();
        $translate = function ($string) use ($translator) {
            return $translator->getTranslation('abstract/message_protection', $string);
        };

        $diagnostics = array(
            'masked' => false,
            'output' => (string)$testMessage,
            'steps' => array(),
        );

        $addStep = function ($label, $ok, $detail) use (&$diagnostics) {
            $diagnostics['steps'][] = array(
                'label' => $label,
                'ok' => $ok,
                'detail' => $detail,
            );
        };

        // Step 1: chat found
        if (!($chat instanceof \erLhcoreClassModelChat)) {
            $addStep($translate('Chat found'), false, $translate('Chat with the provided ID was not found'));
            return $diagnostics;
        }

        $addStep($translate('Chat found'), true, $translate('Chat') . ' #' . $chat->id . ' | ' . $translate('Department') . ' #' . $chat->dep_id . ' | ' . $translate('Assigned operator') . ' #' . $chat->user_id . ' | ' . $translate('Locale') . ': ' . ($chat->chat_locale != '' ? $chat->chat_locale : '-'));

        // Step 2: guardrails enabled (same check as in fetchmessages.php)
        $guardrailsConfig = \erLhcoreClassModelChatConfig::fetch('guardrails_enabled');
        $guardrailsEnabled = ($guardrailsConfig instanceof \erLhcoreClassModelChatConfig) && (int)$guardrailsConfig->current_value == 1;
        $addStep($translate('Guardrails enabled'), $guardrailsEnabled, $guardrailsEnabled ? $translate('Masking is active in settings') : $translate('Masking is disabled in settings, messages are never masked'));

        if ($guardrailsEnabled === false) {
            return $diagnostics;
        }

        // Step 3: visitor message present (same check as in fetchmessages.php)
        $addStep($translate('Visitor message present'), true, $translate('Test message is treated as a visitor message (user_id 0), masking is executed'));

        // Step 4: chat assigned to operator (fetchmessages.php only masks chats with an assigned operator)
        $assigned = (int)$chat->user_id > 0;
        $addStep($translate('Chat assigned to operator'), $assigned, $assigned ? $translate('Operator') . ' #' . (int)$chat->user_id : $translate('Chat has no assigned operator, masking is skipped'));

        if ($assigned === false) {
            return $diagnostics;
        }

        // Step 5: find masking rule for chat department (same lookup as ChatMessagesGhosting::maskVisitorMessages())
        $dep_id = (int)$chat->dep_id;
        $cacheKey = $type . '_' . $dep_id;

        if (!isset(self::$maskRulesByType[$cacheKey])) {
            $maskRule = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::findOne(array(
                'filter' => array('rule_type' => $type, 'enabled' => 1),
                'customfilter' => array(
                    "((has_dep = 1 AND dep_ids != '' AND dep_ids != '[]' AND JSON_CONTAINS(dep_ids, '" . $dep_id . "', '$')) OR has_dep = 0)"
                ),
                'sort' => '`has_dep` DESC, `id` ASC'
            ));

            self::$maskRulesByType[$cacheKey] = $maskRule !== false ? $maskRule : null;
        }

        $maskRule = self::$maskRulesByType[$cacheKey];

        if ($maskRule === null) {
            $addStep($translate('Masking rule found'), false, $translate('No enabled masking rule matches type') . ' ' . $type . ' ' . $translate('for department') . ' #' . $dep_id);
            return $diagnostics;
        }

        $addStep($translate('Masking rule found'), true, $translate('Rule') . ': ' . $maskRule->name . ' (#' . $maskRule->id . ', ' . $translate('type') . ' ' . $maskRule->rule_type . ', ' . $translate('department rule') . ': ' . ($maskRule->has_dep == 1 ? $translate('yes') : $translate('no')) . ')');

        // Locale based translation of the rule warning (same as maskVisitorMessages())
        if ($chat->chat_locale != '') {
            $maskRule = clone $maskRule;
            $maskRule->translateByChat($chat->chat_locale);
        }

        // Step 6: operator permission (see_sensitive_information)
        $shouldMask = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::shouldMask((int)$chat->user_id);
        $addStep($translate('Operator cannot see sensitive information'), $shouldMask, $shouldMask ? $translate('Operator lacks the lhchat/see_sensitive_information permission, masking is applied') : $translate('Operator has the lhchat/see_sensitive_information permission, message is not masked'));

        if ($shouldMask === false) {
            return $diagnostics;
        }

        // Step 7: apply masking to the test message
        $masked = $maskRule->getMasked((string)$testMessage);
        $diagnostics['masked'] = $masked !== (string)$testMessage;
        $diagnostics['output'] = $masked;
        $addStep($translate('Masking applied'), $diagnostics['masked'], $diagnostics['masked'] ? $translate('Message content matched a pattern and was masked') : $translate('Message content did not match any pattern in the rule, nothing was masked'));

        return $diagnostics;
    }
}
