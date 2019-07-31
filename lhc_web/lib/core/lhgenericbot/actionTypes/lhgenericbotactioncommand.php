<?php

class erLhcoreClassGenericBotActionCommand {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($params['do_not_save']) && $params['do_not_save'] == true) {
            return;
        }

        if ($action['content']['command'] == 'stopchat') {

            $isOnline = (isset($action['content']['payload_ignore_status']) && $action['content']['payload_ignore_status'] == true) || erLhcoreClassChat::isOnline($chat->dep_id,false, array('exclude_bot' => true));

            if ($isOnline == false && isset($action['content']['payload']) && is_numeric($action['content']['payload'])) {

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                    'action' => $action,
                    'chat' => & $chat,
                    'is_online' => false
                ));

                if ($handler !== false) {
                    $trigger = $handler['trigger'];
                } else {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload']);
                }

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }

            } else if ($isOnline == true) {

                $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list
                $chat->pnd_time = time();
                // We do not have to set this
                // Because it triggers auto responder of not replying
                // $chat->last_op_msg_time = time();
                $chat->saveThis();

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                    'action' => $action,
                    'chat' => & $chat,
                    'is_online' => true
                ));

                if ($handler !== false) {
                    $trigger = $handler['trigger'];
                } else {
                    if (isset($action['content']['payload_online']) && is_numeric($action['content']['payload_online'])) {
                        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload_online']);
                    } else {
                        $trigger = null;
                    }
                }

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }
            }

        } elseif ($action['content']['command'] == 'transfertobot') {
            $chat->status = erLhcoreClassModelChat::STATUS_BOT_CHAT;
            $chat->last_op_msg_time = time();
            $chat->saveThis();

            if (isset($action['content']['payload']) && is_numeric($action['content']['payload'])) {

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                    'action' => $action,
                    'chat' => & $chat,
                ));

                if ($handler !== false) {
                    $trigger = $handler['trigger'];
                } else {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload']);
                }

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }
            }
        } elseif ($action['content']['command'] == 'closechat') {

            $chat->pnd_time = time();
            $chat->last_op_msg_time = time();

            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                'action' => $action,
                'chat' => & $chat,
            ));

            if ($handler === false) {
                erLhcoreClassChatHelper::closeChat(array(
                    'chat' => & $chat,
                    'bot' => true
                ));
            }

        } elseif ($action['content']['command'] == 'chatvariable') {

                $variablesArray = (array)$chat->chat_variables_array;

                $variablesAppend = json_decode($action['content']['payload'],true);

                if (is_array($variablesAppend)) {
                    foreach ($variablesAppend as $key => $value) {
                        $variablesArray[$key] = $value;
                    }
                }

                $chat->chat_variables = json_encode($variablesArray);
                $chat->chat_variables_array = $variablesArray;
                $chat->saveThis();

        } elseif ($action['content']['command'] == 'setchatattribute') {
                $chat->{$action['content']['payload']} = $action['content']['payload_arg'];
                $chat->saveThis();
        } elseif ($action['content']['command'] == 'dispatchevent') {

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_dispatch_event', array(
                    'action' => $action,
                    'chat' => & $chat,
                ));

                $chat->saveThis();

        }
    }
}

?>