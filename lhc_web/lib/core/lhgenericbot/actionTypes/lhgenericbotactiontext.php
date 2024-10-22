<?php

class erLhcoreClassGenericBotActionText {

    public static function process($chat, $action, $trigger, $params)
    {
        static $triggersProcessed = array();

        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }

        // Message should be send only on start chat event, but we are not in start chat mode
        if (
            in_array($trigger->id, $triggersProcessed) ||
            (isset($action['content']['attr_options']['on_start_chat']) && $action['content']['attr_options']['on_start_chat'] == true &&
                (
                    erLhcoreClassGenericBotWorkflow::$startChat == false && !(isset($params['start_mode']) && $params['start_mode'] == true)
                )
            )
        )
        {
            return;
        }

        // Send only once
        if (isset($action['content']['attr_options']['on_start_chat']) && $action['content']['attr_options']['on_start_chat'] == true &&
            (
                erLhcoreClassGenericBotWorkflow::$startChat == true || (isset($params['start_mode']) && $params['start_mode'] == true)
            )
        )
        {
            $triggersProcessed[] = $trigger->id;
        }

        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['html']) && !empty($action['content']['html']))
        {
            $metaMessage['content']['html']['content'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['html'], array('chat' => $chat, 'args' => $params));

            if (isset($params['replace_array'])) {
                $metaMessage['content']['html']['content'] = str_replace(array_keys($params['replace_array']),array_values($params['replace_array']),$metaMessage['content']['html']['content']);
            }
        }

        if (isset($action['content']['reactions']) && !empty($action['content']['reactions'])) {
            $metaMessage['content']['reactions']['content'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['reactions'], array('chat' => $chat, 'args' => $params));
        }

        if (isset($action['content']['quick_replies']) && !empty($action['content']['quick_replies']))
        {

            $quickReplies = array();

            foreach ($action['content']['quick_replies'] as $quickReply) {

                $validButton = true;

                if (isset($quickReply['content']['render_precheck_function']) && $quickReply['content']['render_precheck_function'] != '') {

                    $validationResult = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                        'render' => $quickReply['content']['render_precheck_function'],
                        'render_args' => (isset($quickReply['content']['render_args']) ? $quickReply['content']['render_args'] : []),
                        'chat' => & $chat,
                        'trigger' => $trigger,
                    ));

                    if ($validationResult !== false && isset($validationResult['content']['valid']) && $validationResult['content']['valid'] === false)
                    {
                        if (isset($validationResult['content']['hide_button']) && $validationResult['content']['hide_button'] == true) {
                            $validButton = false;
                        }

                        if (isset($validationResult['content']['disable_button']) && $validationResult['content']['disable_button'] == true) {
                            $quickReply['content']['disabled'] = true;
                        }
                    }
                }

                if ($validButton == true) {
                    if (!isset($quickReply['content']['bot_condition']) || $quickReply['content']['bot_condition'] == "") {
                        $quickReplies[] = $quickReply;
                    } else {
                        $buttonRules = explode(",",$quickReply['content']['bot_condition']);
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
                            $quickReplies[] = $quickReply;
                        }
                    }
                }

            }

            if (!empty($quickReplies)){
                $metaMessage['content']['quick_replies'] = $quickReplies;
            }
        }

        if (isset($action['content']['callback_list']) && !empty($action['content']['callback_list']))
        {
            $filter = array('filter' => array('chat_id' => $chat->id));

            if ( erLhcoreClassGenericBotWorkflow::$currentEvent instanceof erLhcoreClassModelGenericBotChatEvent) {
                $filter['filternot']['id'] = erLhcoreClassGenericBotWorkflow::$currentEvent->id;
            }

            $softEvent = false;
            $hasEvent = false;

            foreach (erLhcoreClassModelGenericBotChatEvent::getList($filter) as $eventFilter) {
                $hasEvent = true;
                if (isset($eventFilter->content_array['soft_event']) && $eventFilter->content_array['soft_event'] === true) {
                    $softEvent = true;
                    $eventFilter->removeThis();
                }
            }

            if ($hasEvent && $softEvent === false) {
                $action['content']['text'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please complete previous process!');
            } else {
                $event = new erLhcoreClassModelGenericBotChatEvent();
                $event->chat_id = $chat->id;
                $event->ctime = time();
                $event->content = json_encode(array('callback_list' => $action['content']['callback_list']));

                if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                    $event->saveThis();
                }
            }
        }

        if (isset($action['content']['attr_options']) && !empty($action['content']['attr_options']))
        {
            $metaMessage['content']['attr_options'] = $action['content']['attr_options'];
        }

        $action['content']['text'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['text'], array('chat' => $chat, 'args' => $params));

        $msgData = explode('|||',(isset($action['content']['text']) ? trim($action['content']['text']) : ''));

        $item = $msgData[0];
        if (count($msgData) > 0){
            $item = trim($msgData[mt_rand(0,count($msgData)-1)]);
        }

        $msg->msg = $item;

        if (isset($params['error_code'])) {
            $bot = erLhcoreClassModelGenericBotBot::fetch($trigger->bot_id);
            if ($bot instanceof erLhcoreClassModelGenericBotBot) {
                $configurationArray = $bot->configuration_array;
                if (isset($configurationArray['exc_group_id']) && !empty($configurationArray['exc_group_id'])){
                    $exceptionMessage = erLhcoreClassModelGenericBotExceptionMessage::findOne(array('limit' => 1, 'sort' => 'priority ASC', 'filter' => array('active' => 1,'code' => $params['error_code']), 'filterin' => array('exception_group_id' => $configurationArray['exc_group_id'])));
                    if ($exceptionMessage instanceof erLhcoreClassModelGenericBotExceptionMessage && $exceptionMessage->message != '') {
                        $params['replace_array']['{error}'] = erLhcoreClassGenericBotWorkflow::translateMessage($exceptionMessage->message, array('chat' => $chat, 'args' => $params));
                    }
                }
            }
        }

        if (isset($params['replace_array'])) {
            foreach ($params['replace_array'] as $keyReplace => $valueReplace) {
                if (is_object($valueReplace) || is_array($valueReplace)) {
                    if (isset($action['content']['attr_options']['json_replace']) && $action['content']['attr_options']['json_replace'] === true)
                    {
                        $msg->msg = @str_replace($keyReplace,json_encode($valueReplace),$msg->msg);
                    } else {
                        $msg->msg = @str_replace($keyReplace,'[' . $keyReplace . ' - OBJECT OR ARRAY]',$msg->msg);
                    }
                } else {
                    $msg->msg = @str_replace($keyReplace,$valueReplace,$msg->msg);
                }
            }
        }


        if (isset($params['auto_responder']) && $params['auto_responder'] === true) {
            $metaMessage['content']['auto_responder'] = true;
        }

        $msg->msg = erLhcoreClassGenericBotWorkflow::translateMessage($msg->msg, array('chat' => $chat, 'args' => $params));
        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : (isset($params['meta_msg']) && !empty($params['meta_msg']) ? json_encode($params['meta_msg']) : '');

        if (!empty($msg->meta_msg)){
            $msg->meta_msg = erLhcoreClassGenericBotWorkflow::translateMessage($msg->meta_msg, array('chat' => $chat, 'args' => $params));
        }

        // Automatic translations
        if (isset($action['content']['attr_options']['auto_translate']) && $action['content']['attr_options']['auto_translate'] == true && $chat->dep_id > 0) {
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

        // Perhaps this message should be saved as a system message
        if (isset($action['content']['attr_options']['as_system']) && $action['content']['attr_options']['as_system'] == true)
        {
            $msg->user_id = -1;
        }

        if (isset($action['content']['attr_options']['as_visitor']) && $action['content']['attr_options']['as_visitor'] == true)
        {
            $msg->user_id = 0;
            $msg->name_support = '';
        }

        if (isset($action['content']['attr_options']['as_log_msg']) && $action['content']['attr_options']['as_log_msg'] == true)
        {
            $params['do_not_save'] = true;
            erLhcoreClassLHCBotWorker::logIfRequiredPlain($chat, 'text_msg', $msg->msg);
        }

        // Support for commands
        if (strpos($msg->msg, '!') === 0) {
            $bot = erLhcoreClassModelGenericBotBot::fetch($trigger->bot_id);
            if ($bot instanceof erLhcoreClassModelGenericBotBot) {
                $bot->id = -2;
                $statusCommand = erLhcoreClassChatCommand::processCommand(array('user' => $bot, 'msg' => $msg->msg, 'chat' => & $chat));

                if ($statusCommand['processed'] === true) {
                    $msg->user_id = -1;
                    $rawMessage = !isset($statusCommand['raw_message']) ? $msg->msg : $statusCommand['raw_message'];
                    $msg->msg = trim('[b]'.$bot->name_support.'[/b]: '.$rawMessage .' '. ($statusCommand['process_status'] != '' ? '|| '.$statusCommand['process_status'] : ''));
                }
            }
        }

        if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
            erLhcoreClassChat::getSession()->save($msg);
        }

        return $msg;
    }
}

?>