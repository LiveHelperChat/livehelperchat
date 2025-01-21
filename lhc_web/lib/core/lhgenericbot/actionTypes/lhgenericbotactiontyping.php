<?php

class erLhcoreClassGenericBotActionTyping {

    public static function process($chat, $action, $trigger, $params)
    {
        static $triggersProcessed = array();

        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }
        
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (
            (isset($action['content']['duration']) && !empty($action['content']['duration']) && $action['content']['duration'] > 0) ||
            (isset($action['content']['untill_message']) && $action['content']['untill_message'] == true)
        )
        {
            // Message should be send only on start chat event, but we are not in start chat mode
            if (in_array($trigger->id, $triggersProcessed) || isset($action['content']['on_start_chat']) && $action['content']['on_start_chat'] == true && (erLhcoreClassGenericBotWorkflow::$startChat == false && !(isset($params['start_mode']) && $params['start_mode'] == true)))
            {
                return;
            }

            // Send only once
            if (isset($action['content']['on_start_chat']) && $action['content']['on_start_chat'] == true &&
                (
                    erLhcoreClassGenericBotWorkflow::$startChat == true || (isset($params['start_mode']) && $params['start_mode'] == true)
                )
            )
            {
                $triggersProcessed[] = $trigger->id;
            }

            $action['content']['text'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['text'], array('chat' => $chat, 'args' => $params));

            for ($i = 1; $i <= 3; $i++) {
                if (isset($action['content']['delay_expose_text_'.$i]) && !empty($action['content']['delay_expose_text_'.$i])) {
                    $action['content']['delay_expose_text_'.$i] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['delay_expose_text_'.$i], array('chat' => $chat, 'args' => $params));
                }
            }

            $metaMessage['content']['typing'] = $action['content'];

            if (isset($params['auto_responder']) && $params['auto_responder'] === true) {
                $metaMessage['content']['auto_responder'] = true;
            }

            $msg->msg = "";
            $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
            
            if ($msg->meta_msg != '') {
                $msg->meta_msg = erLhcoreClassGenericBotWorkflow::translateMessage($msg->meta_msg, array('chat' => $chat, 'args' => $params));
            }

            // Automatic translations
            if (isset($action['content']['auto_translate']) && $action['content']['auto_translate'] == true && $chat->dep_id > 0) {
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
        }

        return $msg;
    }
}

?>