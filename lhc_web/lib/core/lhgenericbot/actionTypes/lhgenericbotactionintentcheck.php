<?php

class erLhcoreClassGenericBotActionIntentcheck {

    public static function process($chat, $action, $trigger, $params)
    {

        foreach (erLhcoreClassModelGenericBotPendingEvent::getList(array('limit' => 1, 'filter' => array('chat_id' => $chat->id))) as $pendingEvent) {
            $pendingEvent->removeThis();
            return array(
                'status' => 'stop',
                'trigger_id' => $pendingEvent->trigger_id
            );
        }
    }
}

?>