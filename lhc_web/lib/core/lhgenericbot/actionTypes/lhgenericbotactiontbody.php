<?php

class erLhcoreClassGenericBotActionTbody {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['payload']) && !empty($action['content']['payload']))
        {
            $payload = $action['content']['payload'];

            if (isset($params['replace_array']) && !empty($params['replace_array'])) {
                // Sort keys by length in descending order to avoid partial matches
                $keys = array_keys($params['replace_array']);
                usort($keys, function($a, $b) {
                    return strlen($b) - strlen($a);
                });

                // Create a copy of the message for replacements
                $messageToProcess = $action['content']['payload'];
                $replacedSegments = [];
                $nextPlaceholderId = 0;

                // First pass: handle complex replacements (objects, arrays) with placeholders
                foreach ($keys as $keyReplace) {
                    $valueReplace = $params['replace_array'][$keyReplace];

                    if (is_object($valueReplace) || is_array($valueReplace) || (isset($action['content']['attr_options']['json_replace_all']) && $action['content']['attr_options']['json_replace_all'] === true)) {
                        if (
                            (isset($action['content']['attr_options']['json_replace']) && $action['content']['attr_options']['json_replace'] === true) ||
                            (isset($action['content']['attr_options']['json_replace_all']) && $action['content']['attr_options']['json_replace_all'] === true)
                        ) {
                            if (str_contains($messageToProcess, 'raw_'.$keyReplace) !== false) {
                                $replacement = str_replace('\\\/','\/',erLhcoreClassGenericBotActionRestapi::trimOnce(json_encode($valueReplace)));
                                $messageToProcess = @str_replace('raw_'.$keyReplace, "[[PLACEHOLDER_{$nextPlaceholderId}]]", $messageToProcess);
                                $replacedSegments["[[PLACEHOLDER_{$nextPlaceholderId}]]"] = isset($action['content']['attr_options']['no_reparse']) && $action['content']['attr_options']['no_reparse'] === true ? $replacement : str_replace('{','{ ', $replacement);
                                $nextPlaceholderId++;
                            }
                            if (str_contains($messageToProcess, 'rawjson_'.$keyReplace) !== false) {
                                $replacement = str_replace('\\\/','\/',erLhcoreClassGenericBotActionRestapi::trimOnce(json_encode(json_encode($valueReplace))));
                                $messageToProcess = @str_replace('rawjson_'.$keyReplace, "[[PLACEHOLDER_{$nextPlaceholderId}]]", $messageToProcess);
                                $replacedSegments["[[PLACEHOLDER_{$nextPlaceholderId}]]"] = isset($action['content']['attr_options']['no_reparse']) && $action['content']['attr_options']['no_reparse'] === true ? $replacement : str_replace('{','{ ', $replacement);
                                $nextPlaceholderId++;
                            }
                            if (str_contains($messageToProcess, 'json_'.$keyReplace) !== false) {
                                $replacement = json_encode(json_encode($valueReplace));
                                $messageToProcess = @str_replace('json_'.$keyReplace, "[[PLACEHOLDER_{$nextPlaceholderId}]]", $messageToProcess);
                                $replacedSegments["[[PLACEHOLDER_{$nextPlaceholderId}]]"] = isset($action['content']['attr_options']['no_reparse']) && $action['content']['attr_options']['no_reparse'] === true ? $replacement : str_replace('{','{ ', $replacement);
                                $nextPlaceholderId++;
                            }
                            if (str_contains($messageToProcess, 'direct_'.$keyReplace) !== false) {
                                $replacement = $valueReplace;
                                $messageToProcess = @str_replace('direct_'.$keyReplace, "[[PLACEHOLDER_{$nextPlaceholderId}]]", $messageToProcess);
                                $replacedSegments["[[PLACEHOLDER_{$nextPlaceholderId}]]"] = isset($action['content']['attr_options']['no_reparse']) && $action['content']['attr_options']['no_reparse'] === true ? $replacement : str_replace('{','{ ', $replacement);
                                $nextPlaceholderId++;
                            }
                            if (!str_contains($messageToProcess, 'raw_'.$keyReplace) &&
                                !str_contains($messageToProcess, 'rawjson_'.$keyReplace) &&
                                !str_contains($messageToProcess, 'json_'.$keyReplace) &&
                                !str_contains($messageToProcess, 'direct_'.$keyReplace)) {
                                $replacement = json_encode($valueReplace);
                                $messageToProcess = @str_replace($keyReplace, "[[PLACEHOLDER_{$nextPlaceholderId}]]", $messageToProcess);
                                $replacedSegments["[[PLACEHOLDER_{$nextPlaceholderId}]]"] = isset($action['content']['attr_options']['no_reparse']) && $action['content']['attr_options']['no_reparse'] === true ? $replacement : str_replace('{','{ ', $replacement);
                                $nextPlaceholderId++;
                            }
                        } else {
                            $replacement = '[' . $keyReplace . ' - OBJECT OR ARRAY]';
                            $messageToProcess = @str_replace($keyReplace, "[[PLACEHOLDER_{$nextPlaceholderId}]]", $messageToProcess);
                            $replacedSegments["[[PLACEHOLDER_{$nextPlaceholderId}]]"] = $replacement;
                            $nextPlaceholderId++;
                        }
                    } else {
                        // Simple scalar replacement
                        $messageToProcess = @str_replace($keyReplace, "[[PLACEHOLDER_{$nextPlaceholderId}]]", $messageToProcess);
                        $replacedSegments["[[PLACEHOLDER_{$nextPlaceholderId}]]"] = $valueReplace;
                        $nextPlaceholderId++;
                    }
                }

                // Replace all placeholders with actual content
                foreach ($replacedSegments as $placeholder => $value) {
                    $messageToProcess = str_replace($placeholder, (is_null($value) ? '' : $value), $messageToProcess);
                }

                $payload = $messageToProcess;
            }


            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_actionbody', array(
                'payload' => & $payload,
                'chat' => $chat
            ));

            $triggerBody = json_decode($payload, true);

            if ($triggerBody !== null) {

                $triggerRest = new erLhcoreClassModelGenericBotTrigger();
                $triggerRest->actions = $payload;
                $triggerRest->actions_front = $triggerBody;
                $triggerRest->bot_id = $trigger->bot_id;

                $args = array();

                if (isset($params['msg'])) {
                    $args['args']['msg'] = $params['msg'];
                } elseif (isset($params['msg_text'])) {
                    $args['args']['msg_text'] = $params['msg_text'];
                }

                if (isset($params['replace_array'])) {
                    $args['args']['replace_array'] = $params['replace_array'];
                }

                $args['args']['current_trigger'] = $trigger;

                if (!isset($params['first_trigger'])) {
                    $args['args']['first_trigger'] = $args['args']['current_trigger'];
                } else {
                    $args['args']['first_trigger'] = $params['first_trigger'];
                }

                if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                   return erLhcoreClassGenericBotWorkflow::processTrigger($chat, $triggerRest, true, $args);
                }
            }
        }
    }
}

?>