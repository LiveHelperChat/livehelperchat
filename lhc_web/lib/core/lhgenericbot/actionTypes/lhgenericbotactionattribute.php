<?php

class erLhcoreClassGenericBotActionAttribute {

    public static function process($chat, $action, $trigger, $params)
    {

        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }
        
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['attr_options']['identifier']) && !empty($action['content']['attr_options']['identifier']))
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
                $action['content']['intro_message'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please complete previous process!');
            } else {

                $actionEvent = $action['content'];
                unset($actionEvent['intro_message']);
                $actionEvent['type'] = 'chat_attr';

                $event = new erLhcoreClassModelGenericBotChatEvent();
                $event->chat_id = $chat->id;
                $event->ctime = time();
                $event->content = json_encode(array('soft_event' => (isset($action['content']['soft_event']) && $action['content']['soft_event'] == true), 'callback_list' => array(array('content' => $actionEvent))));

                if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                    $event->saveThis();
                }
            }

            $msgData = explode('|||',(isset($action['content']['intro_message']) ? trim($action['content']['intro_message']) : ''));

            $msgText = $msgData[0];

            if (count($msgData) > 0) {
                $msgText = trim($msgData[mt_rand(0,count($msgData)-1)]);
            }

            if ($msgText != '') {
                $msgText = erLhcoreClassGenericBotWorkflow::translateMessage($msgText, array('chat' => $chat, 'args' => $params));
            }

            $msg->msg = $msgText;
            
            if (isset($params['auto_responder']) && $params['auto_responder'] === true) {
                $metaMessage['content']['auto_responder'] = true;
            }

            $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';

            if ($msg->meta_msg != '') {
                $msg->meta_msg = erLhcoreClassGenericBotWorkflow::translateMessage($msg->meta_msg, array('chat' => $chat, 'args' => $params));
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