<?php

class erLhcoreClassGenericBotActionText {

    public static function process($chat, $action, $trigger, $params)
    {
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
            $metaMessage['content']['html']['content'] = $action['content']['html'];
        }

        if (isset($action['content']['attr_options']) && !empty($action['content']['attr_options']))
        {
            $metaMessage['content']['attr_options'] = $action['content']['attr_options'];
        }

        $msgData = explode('|||',(isset($action['content']['text']) ? trim($action['content']['text']) : ''));

        $item = $msgData[0];
        if (count($msgData) > 0){
            $item = trim($msgData[mt_rand(0,count($msgData)-1)]);
        }

        $msg->msg = $item;

        if (isset($params['replace_array'])) {
            $msg->msg = str_replace(array_keys($params['replace_array']),array_values($params['replace_array']),$msg->msg);
        }

        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
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