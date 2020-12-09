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

            if (!isset($chatVariables['aicons'][$alertIcon]) || $alertValue != $chatVariables['aicons'][$alertIcon]['alert']) {

                $paramsIcon = ['alert' => $alertValue];

                if ($alertColor != '') {
                    $paramsIcon['icolor'] = $alertColor;
                }

                if (isset($action['content']['attr_options']['aicon_title']) && $action['content']['attr_options']['aicon_title'] != '') {
                    $paramsIcon['t'] = $action['content']['attr_options']['aicon_title'];
                }

                $chatVariables['aicons'][$alertIcon] = $paramsIcon;
                $needUpdate = true;
            }
        }

        if ($needUpdate == true) {
            $chat->chat_variables = json_encode($chatVariables);
            $chat->chat_variables_array = $chatVariables;
            $chat->saveThis(array('update' => array('chat_variables')));
        }
    }
}

?>