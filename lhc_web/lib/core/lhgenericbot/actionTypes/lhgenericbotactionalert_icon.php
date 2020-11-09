<?php

class erLhcoreClassGenericBotActionAlert_icon {

    public static function process($chat, $action, $trigger, $params)
    {
        $alertIcon = $action['content']['alert_icon'];

        if (empty($alertIcon)) {
            $alertIcon = 'new_releases';
        }

        $chatVariables = $chat->chat_variables_array;

        if (!isset($chatVariables['aicons'])) {
            $chatVariables['aicons'] = [];
        }

        $needUpdate = false;

        if ($action['content']['attr_options']['remove_icon'] && $action['content']['attr_options']['remove_icon'] == true) {
            if (isset($chatVariables['aicons'][$alertIcon])) {
                unset($chatVariables['aicons'][$alertIcon]);
                $needUpdate = true;
            }
            if (empty($chatVariables['aicons'])) {
                unset($chatVariables['aicons']);
            }
        } else {
            $alertValue = isset($action['content']['attr_options']['show_alert']) && $action['content']['attr_options']['show_alert'] == true;
            if (!isset($chatVariables['aicons'][$alertIcon]) || $alertValue != $chatVariables['aicons'][$alertIcon]['alert']) {
                $chatVariables['aicons'][$alertIcon] = ['alert' => $alertValue];
                $needUpdate = true;
            }
        }

        if ($needUpdate == true) {
            $chat->chat_variables = json_encode($chatVariables);
            $chat->chat_variables_array = $chatVariables;
            $chat->saveThis();
        }
    }
}

?>