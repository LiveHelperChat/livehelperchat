<?php

class erLhcoreClassGenericBotActionText {

    public static function process($chat, $action)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['quick_replies']) && !empty($action['content']['quick_replies']))
        {
            $metaMessage['content']['quick_replies'] = $action['content']['quick_replies'];
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
                $event->saveThis();
            }
        }

        $msg->msg = trim($action['content']['text']);
        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
        $msg->chat_id = $chat->id;
        $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
        $msg->user_id = -2;
        $msg->time = time() + 5;

        erLhcoreClassChat::getSession()->save($msg);

        return $msg;
    }
}

?>