<?php

class erLhcoreClassGenericBotActionPredefined {

    public static function process($chat, $action)
    {
        if (isset($action['content']['payload']) && is_numeric($action['content']['payload'])) {

            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_predefined', array(
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

?>