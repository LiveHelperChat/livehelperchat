<?php

class erLhcoreClassGenericBotActionText {

    public static function process($chat, $action, $trigger, $params)
    {
        static $triggersProcessed = array();

        // Message should be send only on start chat event, but we are not in start chat mode
        if (in_array($trigger->id, $triggersProcessed) || isset($action['content']['attr_options']['on_start_chat']) && $action['content']['attr_options']['on_start_chat'] == true && erLhcoreClassGenericBotWorkflow::$startChat == false)
        {
            return;
        }

        // Send only once
        if (isset($action['content']['attr_options']['on_start_chat']) && $action['content']['attr_options']['on_start_chat'] == true && erLhcoreClassGenericBotWorkflow::$startChat == true)
        {
            $triggersProcessed[] = $trigger->id;
        }

        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['quick_replies']) && !empty($action['content']['quick_replies']))
        {

            $quickReplies = array();

            foreach ($action['content']['quick_replies'] as $quickReply) {

                $validButton = true;

                if (isset($quickReply['content']['render_precheck_function']) && $quickReply['content']['render_precheck_function'] != '') {

                    $validationResult = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                        'render' => $quickReply['content']['render_precheck_function'],
                        'render_args' => $quickReply['content']['render_args'],
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
                    $quickReplies[] = $quickReply;
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

            $event = erLhcoreClassModelGenericBotChatEvent::findOne($filter);

            if ($event instanceof erLhcoreClassModelGenericBotChatEvent) {
                $action['content']['text'] = 'Please complete previous process!';
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

        if (isset($action['content']['html']) && !empty($action['content']['html']))
        {
            $metaMessage['content']['html']['content'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['html'], array('chat' => $chat));
        }

        if (isset($action['content']['attr_options']) && !empty($action['content']['attr_options']))
        {
            $metaMessage['content']['attr_options'] = $action['content']['attr_options'];
        }

        $action['content']['text'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['text'], array('chat' => $chat));

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
                        $params['replace_array']['{error}'] = erLhcoreClassGenericBotWorkflow::translateMessage($exceptionMessage->message, array('chat' => $chat));
                    }
                }
            }
        }

        if (isset($params['replace_array'])) {
            $msg->msg = str_replace(array_keys($params['replace_array']),array_values($params['replace_array']),$msg->msg);
        }

        $msg->msg = erLhcoreClassGenericBotWorkflow::translateMessage($msg->msg, array('chat' => $chat));
        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : (isset($params['meta_msg']) && !empty($params['meta_msg']) ? json_encode($params['meta_msg']) : '');

        if (!empty($msg->meta_msg)){
            $msg->meta_msg = erLhcoreClassGenericBotWorkflow::translateMessage($msg->meta_msg, array('chat' => $chat));
        }

        $msg->chat_id = $chat->id;
        $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
        $msg->user_id = -2;
        $msg->time = time() + 5;

        if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
            erLhcoreClassChat::getSession()->save($msg);
        }

        return $msg;
    }
}

?>