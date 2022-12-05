<?php

namespace LiveHelperChat\Helpers;

class Reaction
{
    public static function operatorReaction($chat, $message, $paramsPayload) {

        $db = \ezcDbInstance::get();
        $db->reconnect();

        $db->beginTransaction();

        $message->syncAndLock();

        $metaMessage = $message->meta_msg_array;

        $currentPart = isset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]) ? $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] : null;

        $action = 'remove';
        $identifier = '';
        $valueAction = '';

        // Same reaction icon was clicked unselect if it was selected
        if ($currentPart === $paramsPayload['payload']) {
            unset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]);
            if (empty($metaMessage['content']['reactions']['current'])) {
                unset($metaMessage['content']);
            }
            $identifier = $paramsPayload['payload-id'];
        } else {

            if (
                (
                    (isset($chat->chat_variables_array['theme_id']) && ($theme = \erLhAbstractModelWidgetTheme::fetch($chat->chat_variables_array['theme_id']))) ||
                    ($chat->theme_id > 0 && ($theme = \erLhAbstractModelWidgetTheme::fetch($chat->theme_id)))
                )
                && $theme instanceof \erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['custom_tb_reactions']) && $theme->bot_configuration_array['custom_tb_reactions'] != '') {
                $validIdentifiers = [];
                $action = 'remove';
                $identifier = '';
                $valueAction = '';
                $oneReactionPerMessage = isset($theme->bot_configuration_array['one_reaction_per_msg']) && $theme->bot_configuration_array['one_reaction_per_msg'] == true;

                if (isset($theme->bot_configuration_array['enable_react_for_vi']) && $theme->bot_configuration_array['enable_react_for_vi'] == true) {
                    if (isset($theme->bot_configuration_array['custom_tb_reactions'])) {
                        $partsReaction = explode("=", $theme->bot_configuration_array['custom_tb_reactions']);
                        foreach ($partsReaction as $reaction) {
                            $iconParams = explode("|", $reaction);
                            if (!isset($iconParams[2]) || !isset($iconParams[1])) {
                                $iconParams[2] = strtoupper(preg_replace("/^[0]+/", "", bin2hex(mb_convert_encoding($iconParams[0], 'UTF-32', 'UTF-8'))));
                                $iconParams[1] = 1;
                            }
                            $validIdentifiers[$iconParams[2]][] = $iconParams[1];
                        }
                    }
                }

                if (isset($theme->bot_configuration_array['custom_mw_reactions'])) {
                    $partsReaction = explode("=", $theme->bot_configuration_array['custom_mw_reactions']);
                    foreach ($partsReaction as $reaction) {
                        $iconParams = explode("|", $reaction);
                        if (!isset($iconParams[2]) || !isset($iconParams[1])) {
                            $iconParams[2] = strtoupper(preg_replace("/^[0]+/", "", bin2hex(mb_convert_encoding($iconParams[0], 'UTF-32', 'UTF-8'))));
                            $iconParams[1] = 1;
                        }
                        $validIdentifiers[$iconParams[2]][] = $iconParams[1];
                    }
                }

                if (isset($validIdentifiers[$paramsPayload['payload-id']])) {
                    $currentPart = isset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]) ? $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] : null;
                    if ($currentPart === $paramsPayload['payload']) {
                        unset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]);
                        if (empty($metaMessage['content']['reactions']['current'])) {
                            unset($metaMessage['content']['reactions']['current']);
                        }
                        $identifier = $paramsPayload['payload-id'];
                        if ($oneReactionPerMessage) {
                            unset($metaMessage['content']['reactions']['current']);
                        }
                    } else {
                        if ($oneReactionPerMessage) {
                            unset($metaMessage['content']['reactions']['current']);
                        }
                        if (key_exists($paramsPayload['payload-id'], $validIdentifiers) && in_array($paramsPayload['payload'], $validIdentifiers[$paramsPayload['payload-id']])) {
                            $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] = (string)$paramsPayload['payload'];
                            $action = 'add';
                            $valueAction = (string)$paramsPayload['payload'];
                        }
                    }
                }
            } else {
                // Operator can react only thumb only, extensions might add more support if required
                $validIdentifiers['thumb'] = [0,1];
                if (key_exists($paramsPayload['payload-id'],$validIdentifiers) && in_array($paramsPayload['payload'],$validIdentifiers[$paramsPayload['payload-id']])) {
                    $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] = (string)$paramsPayload['payload'];
                    $action = 'add';
                    $valueAction = (string)$paramsPayload['payload'];
                }
            }
        }

        $message->meta_msg_array = $metaMessage;
        $message->meta_msg = json_encode($message->meta_msg_array);
        $message->updateThis(['update' => ['meta_msg']]);

        $db->commit();

        // This we need for frontend visitor to update UI
        $chat->operation = "lhinst.updateMessageRow({$message->id});\n";
        $chat->updateThis(['update' => ['operation']]);

        // Dispatch reaction action for extensions
        \erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.reaction_operator', array(
            'reaction_identifier' => $identifier,
            'reaction_value' => $valueAction,
            'action' => $action,
            'msg' => & $message,
            'chat' => & $chat
        ));

    }
}

?>