<?php

class erLhcoreClassGenericBotActionAlert_icon {

    public static function process($chat, $action, $trigger, $params)
    {

        $alertIcon = 'new_releases';

        if (isset($action['content']['alert_icon']) && !empty($action['content']['alert_icon'])) {
            $alertIcon = $action['content']['alert_icon'];
        }

        $chatVariables = $chat->chat_variables_array;

        if (!isset($chatVariables['aicons'])) {
            $chatVariables['aicons'] = [];
        }

        $needUpdate = false;

        if (isset($action['content']['attr_options']['remove_icon']) && $action['content']['attr_options']['remove_icon'] == true) {
            if (isset($chatVariables['aicons'][$alertIcon])) {
                unset($chatVariables['aicons'][$alertIcon]);
                $needUpdate = true;
            }
            if (empty($chatVariables['aicons'])) {
                unset($chatVariables['aicons']);
            }
        } else {
            $alertValue = isset($action['content']['attr_options']['show_alert']) && $action['content']['attr_options']['show_alert'] == true;
            $alertColor = isset($action['content']['attr_options']['aicon_color']) ? $action['content']['attr_options']['aicon_color'] : '';
            $nickColor = isset($action['content']['attr_options']['nick_color']) ? $action['content']['attr_options']['nick_color'] : '';
            $nickBold = isset($action['content']['attr_options']['bold_nick']) ? $action['content']['attr_options']['bold_nick'] : false;

            if (
                !isset($chatVariables['aicons'][$alertIcon]) ||
                $alertValue != $chatVariables['aicons'][$alertIcon]['alert'] ||

                (isset($chatVariables['aicons'][$alertIcon]['icolor']) && $alertColor != $chatVariables['aicons'][$alertIcon]['icolor']) ||
                (!isset($chatVariables['aicons'][$alertIcon]['icolor']) && $alertColor != '') ||

                (isset($chatVariables['aicons'][$alertIcon]['nc']) && $nickColor != $chatVariables['aicons'][$alertIcon]['nc']) ||
                (!isset($chatVariables['aicons'][$alertIcon]['nc']) && $nickColor != '') ||

                (isset($chatVariables['aicons'][$alertIcon]['b']) && $nickBold == false) ||
                (!isset($chatVariables['aicons'][$alertIcon]['b']) && $nickBold == true)

            ) {

                $paramsIcon = ['alert' => $alertValue];

                if ($alertColor != '') {
                    $paramsIcon['icolor'] = $alertColor;
                }

                if (isset($action['content']['attr_options']['aicon_title']) && $action['content']['attr_options']['aicon_title'] != '') {
                    $paramsIcon['t'] = $action['content']['attr_options']['aicon_title'];
                }

                if ($nickBold === true) {
                    $paramsIcon['b'] = 1;
                }

                if ($nickColor != '') {
                    $paramsIcon['nc'] = $nickColor;
                }

                $chatVariables['aicons'][$alertIcon] = $paramsIcon;
                $needUpdate = true;
            }
        }

        if ($needUpdate == true) {
            $chat->chat_variables = json_encode($chatVariables);
            $chat->chat_variables_array = $chatVariables;

            $db = ezcDbInstance::get();

            try {

                $db->beginTransaction();

                // Lock record
                erLhcoreClassModelChat::fetchAndLock($chat->id);

                $chat->saveThis(array('update' => array('chat_variables')));

                $db->commit();

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.bot.alert_icon',array('chat' => & $chat));

            } catch (Exception $e) {
                $db->rollback();
            }
        }
    }
}

?>