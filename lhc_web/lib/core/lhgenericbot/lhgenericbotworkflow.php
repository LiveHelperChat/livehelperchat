<?php

class erLhcoreClassGenericBotWorkflow {

    public static function findEvent($text, $type = 0)
    {
        $event = erLhcoreClassModelGenericBotTriggerEvent::findOne(array('filter' => array('type' => 0),'filterlikeright' => array('pattern' => $text)));
        return $event;
    }

    public static function userMessageAdded(& $chat, $msg) {
        $event = self::findEvent($msg->msg);

        if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
            self::processTrigger($chat, $event->trigger);
        }
    }

    public static function processTrigger($chat, $trigger)
    {
        foreach ($trigger->actions_front as $action) {
            call_user_func_array("erLhcoreClassGenericBotAction" . ucfirst($action['type']).'::process',array($chat, $action));
        }
    }
}

?>