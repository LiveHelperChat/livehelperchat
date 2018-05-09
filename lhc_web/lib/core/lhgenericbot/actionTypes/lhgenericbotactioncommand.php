<?php

class erLhcoreClassGenericBotActionCommand {

    public static function process($chat, $action)
    {
        if ($action['content']['command'] == 'stopchat') {

            $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
            $chat->saveThis();

            $isOnline = erLhcoreClassChat::isOnline($chat->dep_id);

            if ($isOnline == false && isset($action['content']['payload']) && is_numeric($action['content']['payload'])) {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload']);

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }

            } else if ($isOnline == true && isset($action['content']['payload_online']) && is_numeric($action['content']['payload_online'])) {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload_online']);

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }
            }

        } elseif ($action['content']['command'] == 'transfertobot') {
            $chat->status = erLhcoreClassModelChat::STATUS_BOT_CHAT;
            $chat->saveThis();

            if (isset($action['content']['payload']) && is_numeric($action['content']['payload'])) {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload']);

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }
            }
        }

    }
}

?>