<?php

class erLhcoreClassGenericBotActionButtons {

    public static function process($chat, $action, $trigger, $params)
    {
        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }
        
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['buttons']) && !empty($action['content']['buttons'])) {

            $buttonsValid = [];
            foreach ($action['content']['buttons'] as $button) {
                if (!isset($button['content']['bot_condition']) || $button['content']['bot_condition'] == "") {
                    $buttonsValid[] = $button;
                } else {
                    $buttonRules = explode(",",$button['content']['bot_condition']);
                    $allRulesValid = true;
                    foreach ($buttonRules as $buttonRule) {
                        $conditionsToValidate = \LiveHelperChat\Models\Bot\Condition::getList(['filter' => ['identifier' => trim($buttonRule)]]);
                        foreach ($conditionsToValidate as $conditionToValidate) {
                            if (!$conditionToValidate->isValid(['chat' => $chat, 'replace_array' => (isset($params['replace_array']) ? $params['replace_array'] : [])])) {
                                $allRulesValid = false;
                            }
                        }
                    }
                    if ($allRulesValid === true) {
                        $buttonsValid[] = $button;
                    }
                }
            }

            if (!empty($buttonsValid)) {
                $metaMessage['content']['buttons_generic'] = $buttonsValid;
            }
        }

        if (isset($action['content']['buttons_options']['hide_text_area']) && $action['content']['buttons_options']['hide_text_area'] == true) {
             $metaMessage['content']['attr_options']['hide_text_area'] = true;
        }

        if (isset($action['content']['buttons_options']['btn_title']) && $action['content']['buttons_options']['btn_title'] != '') {
             $metaMessage['content']['attr_options']['btn_title'] = $action['content']['buttons_options']['btn_title'];
        }

        $msgText = isset($action['content']['buttons_options']['message']) && !empty($action['content']['buttons_options']['message']) ? $action['content']['buttons_options']['message'] : '';

        if ($msgText != '') {
            $msgText = erLhcoreClassGenericBotWorkflow::translateMessage($msgText, array('chat' => $chat, 'args' => $params));
        }
        
        if (isset($params['auto_responder']) && $params['auto_responder'] === true) {
            $metaMessage['content']['auto_responder'] = true;
        }

        $msg->msg = $msgText;

        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';

        if ($msg->meta_msg != '') {
            $msg->meta_msg = erLhcoreClassGenericBotWorkflow::translateMessage($msg->meta_msg, array('chat' => $chat, 'args' => $params));
        }

        // Automatic translations
        if (isset($action['content']['buttons_options']['auto_translate']) && $action['content']['buttons_options']['auto_translate'] == true && $chat->dep_id > 0) {
            $department = erLhcoreClassModelDepartament::fetch($chat->dep_id,true);
            if ($department instanceof erLhcoreClassModelDepartament) {
                $configurationDep = $department->bot_configuration_array;
                if (isset($configurationDep['bot_tr_id']) && $configurationDep['bot_tr_id'] > 0) {
                    $translationGroup = erLhcoreClassModelGenericBotTrGroup::fetch($configurationDep['bot_tr_id']);
                    if ($translationGroup instanceof erLhcoreClassModelGenericBotTrGroup && $translationGroup->use_translation_service == 1 && $translationGroup->bot_lang != '') {
                        erLhcoreClassTranslate::translateBotMessage($chat, $msg, $translationGroup);
                    }
                }
            }
        }

        $msg->chat_id = $chat->id;
        if (isset($params['override_nick']) && !empty($params['override_nick'])) {
            $msg->name_support = (string)$params['override_nick'];
        } else {
            $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
        }
        $msg->user_id = isset($params['override_user_id']) && $params['override_user_id'] > 0 ? (int)$params['override_user_id'] : -2;

        $msg->time = time();

        if (erLhcoreClassGenericBotWorkflow::$setBotFlow === false) {
            $msg->time += 1;
        }

        if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
            erLhcoreClassChat::getSession()->save($msg);
        }

        return $msg;
    }
}

?>