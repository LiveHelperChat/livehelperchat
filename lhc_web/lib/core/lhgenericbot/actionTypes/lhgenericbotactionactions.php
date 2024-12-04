<?php

class erLhcoreClassGenericBotActionActions {

    public static function process($chat, $action, $trigger, $params)
    {
        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }

        if (isset($action['content']['success_message']) && $action['content']['success_message'] != '') {

            $msgData = explode('|||',erLhcoreClassGenericBotWorkflow::translateMessage(trim($action['content']['success_message']), array('chat' => $chat, 'args' => $params)));

            $item = $msgData[0];
            if (count($msgData) > 0){
                $item = trim($msgData[mt_rand(0,count($msgData)-1)]);
            }

            $msg = new erLhcoreClassModelmsg();
            $msg->chat_id = $chat->id;
            if (isset($params['override_nick']) && !empty($params['override_nick'])) {
                $msg->name_support = (string)$params['override_nick'];
            } else {
                $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
            }
            $msg->user_id = isset($params['override_user_id']) && $params['override_user_id'] > 0 ? (int)$params['override_user_id'] : -2;
            $msg->time = time();

            if (erLhcoreClassGenericBotWorkflow::$setBotFlow === true) {
                $msg->time += 1;
            }

            $metaMessage = [];

            if (isset($params['auto_responder']) && $params['auto_responder'] === true) {
                $metaMessage['content']['auto_responder'] = true;
            }

            $msg->msg = $item;
            $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';

            if (isset($params['replace_array'])) {
                foreach ($params['replace_array'] as $keyReplace => $valueReplace) {
                    if (is_object($valueReplace) || is_array($valueReplace)) {
                        $msg->msg = @str_replace($keyReplace,json_encode($valueReplace),$msg->msg);
                    } else {
                        $msg->msg = @str_replace($keyReplace,$valueReplace,$msg->msg);
                    }
                }
            }

            if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                erLhcoreClassChat::getSession()->save($msg);
            }
        }

        // Within next user message we will validate his username or anything else
        if ((isset($action['content']['event_background']) && $action['content']['event_background'] == true) || (isset($action['content']['event_background_inst']) && $action['content']['event_background_inst'])) {

            if (isset($action['content']['event_arg_match']) && isset($params['replace_array'])) {
                foreach ($params['replace_array'] as $keyReplace => $valueReplace) {
                    if (is_object($valueReplace) || is_array($valueReplace)) {
                        $action['content']['event_arg_match'] = @str_replace($keyReplace,json_encode($valueReplace),$action['content']['event_arg_match']);
                    } else {
                        $action['content']['event_arg_match'] = @str_replace($keyReplace,$valueReplace,$action['content']['event_arg_match']);
                    }
                }
            }

            $event = new erLhcoreClassModelGenericBotChatEvent();
            $event->chat_id = $chat->id;
            $event->ctime = time();
            $event->content = json_encode(array('callback_list' => array(
                array(
                    'content' => array(
                        'type' => 'intent',
                        'replace_array' => (isset($params['replace_array']) ? $params['replace_array'] : array()),
                        'validation' => array(
                            'words' => (isset($action['content']['event_validate']) ? $action['content']['event_validate'] : null),
                            'typos' => (isset($action['content']['event_typos']) ? $action['content']['event_typos'] : null),
                            'words_exc' => (isset($action['content']['event_validate_exc']) ? $action['content']['event_validate_exc'] : null),
                            'typos_exc' => (isset($action['content']['event_typos_exc']) ? $action['content']['event_typos_exc'] : null),
                            'words_alt' => (isset($action['content']['event_in_validate']) ? $action['content']['event_in_validate'] : null),
                            'validation_args' => (isset($action['content']['event_arg_match']) ? $action['content']['event_arg_match'] : null),
                            'validation_static_args' => (isset($action['content']['event_arg_static']) ? $action['content']['event_arg_static'] : null),
                        ),
                        'event' => (isset($action['content']['event']) ? $action['content']['event'] : null),
                        'event_args' => array(
                            'invalid' => (isset($action['content']['attr_options']['collection_callback_cancel']) ? $action['content']['attr_options']['collection_callback_cancel'] : null),
                            'valid' => (isset($action['content']['attr_options']['collection_callback_pattern']) ? $action['content']['attr_options']['collection_callback_pattern'] : null),
                            'format' => (isset($action['content']['attr_options']['collection_callback_format']) ? $action['content']['attr_options']['collection_callback_format'] : null),
                            'valid_alt' => (isset($action['content']['attr_options']['collection_callback_alternative']) ? $action['content']['attr_options']['collection_callback_alternative'] : null),
                            'callback_match' => (isset($action['content']['attr_options']['collection_callback_match']) ? $action['content']['attr_options']['collection_callback_match'] : null),
                            'check_default' => (isset($action['content']['attr_options']['check_default']) && $action['content']['attr_options']['check_default'] == true),
                            'validation_args' => (isset($params['validation_args']) ? $params['validation_args'] : array())
                        )
                    )
                )
            )));

            if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                $event->saveThis();
            }

            if (isset($action['content']['event_background_inst']) && $action['content']['event_background_inst']) {
                erLhcoreClassGenericBotWorkflow::processEvent($event, $chat, $params);
                return;
            }
        }

        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
            'render' => (isset($action['content']['event']) ? $action['content']['event'] : null),
            'event' => (isset($event) ? $event : null),
            'render_args' => $params,
            'render_args_event' => array(
                'invalid' => (isset($action['content']['attr_options']['collection_callback_cancel']) ? $action['content']['attr_options']['collection_callback_cancel'] : null),
                'valid' => (isset($action['content']['attr_options']['collection_callback_pattern']) ? $action['content']['attr_options']['collection_callback_pattern'] : null),
                'format' => (isset($action['content']['attr_options']['collection_callback_format']) ? $action['content']['attr_options']['collection_callback_format'] : null),
                'static' => (isset($action['content']['event_arg_static']) ? $action['content']['event_arg_static'] : null),
            ),
            'chat' => & $chat
        ));


        if (isset($handler['trigger'])) {
            return erLhcoreClassGenericBotWorkflow::processTrigger($chat, $handler['trigger'], true, array('args' => $params));
        }

        // We have valid handler, so we have and function also
        if ($handler !== false && isset($handler['render']) && is_callable($handler['render'])) {

        } else if ((!isset($action['content']['event_background']) || $action['content']['event_background'] == false) && isset($handler['valid'])) {

            if ($handler['valid'] == false) {
                $triggerId = isset($action['content']['attr_options']['collection_callback_cancel']) ? $action['content']['attr_options']['collection_callback_cancel'] : 0;
            } else {
                $triggerId = isset($action['content']['attr_options']['collection_callback_pattern']) ? $action['content']['attr_options']['collection_callback_pattern'] : 0;
            }

            if ($triggerId > 0)
            {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($triggerId);

                if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                    if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                        return erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, array('args' => $params));
                    } else {
                        return erLhcoreClassGenericBotWorkflow::processTriggerPreview($chat, $trigger, array('args' => $params));
                    }
                }
            }
        }

        if (isset($msg) && $msg instanceof erLhcoreClassModelmsg) {
            return $msg;
        }
    }
}

?>