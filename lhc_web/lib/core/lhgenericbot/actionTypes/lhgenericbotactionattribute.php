<?php

class erLhcoreClassGenericBotActionAttribute {

    public static function process($chat, $action, $trigger, $params)
    {

        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['attr_options']['name']) && !empty($action['content']['attr_options']['name']))
        {
            $filter = array('filter' => array('chat_id' => $chat->id));

            if ( erLhcoreClassGenericBotWorkflow::$currentEvent instanceof erLhcoreClassModelGenericBotChatEvent) {
                $filter['filternot']['id'] = erLhcoreClassGenericBotWorkflow::$currentEvent->id;
            }

            $event = erLhcoreClassModelGenericBotChatEvent::findOne($filter);

            if ($event instanceof erLhcoreClassModelGenericBotChatEvent) {
                $action['content']['intro_message'] = 'Please complete previous process!';
            } else {

                $actionEvent = $action['content'];
                unset($actionEvent['intro_message']);
                $actionEvent['type'] = 'chat_attr';

                $event = new erLhcoreClassModelGenericBotChatEvent();
                $event->chat_id = $chat->id;
                $event->ctime = time();
                $event->content = json_encode(array('callback_list' => array(array('content' => $actionEvent))));

                if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                    $event->saveThis();
                }
            }

            $msg->msg = (isset($action['content']['intro_message']) ? $action['content']['intro_message'] : '');
            $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
            $msg->chat_id = $chat->id;
            $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
            $msg->user_id = -2;
            $msg->time = time() + 5;

            if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                erLhcoreClassChat::getSession()->save($msg);
            }
        }

        return $msg;
    }
}

?>