<?php

class erLhcoreClassGenericBotActionPauseBot {

    public static function process($chat, $action, $trigger, $params)
    {
        static $triggersProcessed = array();

        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }

        // We do not want to have that feature for automated hosting as it might take all php-fpm workers...
        if (isset($action['content']['duration']) && !empty($action['content']['duration']) && (int)$action['content']['duration'] > 0 && !class_exists('erLhcoreClassInstance'))
        {
            sleep(min((int)$action["content"]["duration"],10));
        }
        
        return null;
    }
}

?>