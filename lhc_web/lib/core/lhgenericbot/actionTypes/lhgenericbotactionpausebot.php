<?php

class erLhcoreClassGenericBotActionPauseBot {

    public static function process($chat, $action, $trigger, $params)
    {
        static $triggersProcessed = array();

        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }
        
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (
            (isset($action['content']['duration']) && !empty($action['content']['duration']) && $action['content']['duration'] > 0)
        )
        {
            sleep($action["content"]["duration"]);
        }
        return $msg;
    }
}

?>