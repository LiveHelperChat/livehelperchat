<?php

class erLhcoreClassGenericBotActionCommand {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($params['do_not_save']) && $params['do_not_save'] == true) {
            return;
        }
        
        if ($action['content']['command'] == 'stopchat') {

            $isOnline = erLhcoreClassChat::isOnline($chat->dep_id,false, array('exclude_bot' => true));

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

            } else if ($isOnline == true && isset($action['content']['payload_online']) && is_numeric($action['content']['payload_online'])) {

                $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list
                $chat->pnd_time = time();
                $chat->saveThis();

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                    'action' => $action,
                    'chat' => & $chat,
                    'is_online' => true
                ));

                if ($handler !== false) {
                    $trigger = $handler['trigger'];
                } else {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload_online']);
                }

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }
            }

        } elseif ($action['content']['command'] == 'transfertobot') {
            $chat->status = erLhcoreClassModelChat::STATUS_BOT_CHAT;
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
        }

    }
}

?>