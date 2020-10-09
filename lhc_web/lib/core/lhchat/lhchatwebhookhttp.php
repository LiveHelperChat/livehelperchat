<?php

/**
 * Status -
 * 0 - Pending
 * 1 - Active
 * 2 - Closed
 * 3 - Blocked
 * */

class erLhcoreClassChatWebhookHttp {

    public function processEvent($event, $params) {

        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT `trigger_id` FROM `lh_webhook` WHERE `event` = :event AND `disabled` = 0");
        $stmt->bindValue(':event', $event,PDO::PARAM_STR);
        $stmt->execute();
        $triggers = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($triggers)) {
            foreach ($triggers as $triggerId) {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($triggerId);
                if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {

                    // processTrigger always requires a chat so fake it.
                    if (!isset($params['chat']) || !($params['chat'] instanceof erLhcoreClassModelChat)) {
                        $params['chat'] = new erLhcoreClassModelChat();
                        $params['chat']->id = -1;
                    }

                    erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, false, array('args' => $params));
                }
            }
        }
    }
}

?>