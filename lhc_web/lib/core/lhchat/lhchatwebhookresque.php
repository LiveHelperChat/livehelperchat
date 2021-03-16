<?php

class erLhcoreClassChatWebhookResque {

    public function processEvent($event, $params) {
        $db = ezcDbInstance::get();

        try {
            $stmt = $db->prepare("SELECT `id` FROM `lh_webhook` WHERE `event` = :event AND `disabled` = 0");
            $stmt->bindValue(':event', $event,PDO::PARAM_STR);
            $stmt->execute();
            $hooks = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            return;
        }

        if (!empty($hooks)) {
            foreach ($hooks as $hookId) {
                if (isset($params['wh_worker']) && $params['wh_worker'] == 'http') {
                    $worker = new erLhcoreClassChatWebhookHttp();
                    $worker->processEvent($event, $params);
                } else if (class_exists('erLhcoreClassExtensionLhcphpresque')) {
                     erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_rest_webhook', 'erLhcoreClassChatWebhookResque', array('hook_id' => $hookId, 'params' => base64_encode(gzdeflate(serialize($params)))));
                }
            }
        }
    }

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        $hookId = $this->args['hook_id'];

        $webhook = erLhcoreClassModelChatWebhook::fetch($hookId);

        $triggerId = $webhook->trigger_id;

        $params = unserialize(gzinflate(base64_decode($this->args['params'])));

        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($triggerId);

        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {

            // processTrigger always requires a chat so fake it.
            if (!isset($params['chat']) || !($params['chat'] instanceof erLhcoreClassModelChat)) {
                $params['chat'] = new erLhcoreClassModelChat();
                $params['chat']->id = -1;
            }

            if (erLhcoreClassChatWebhookHttp::isValidConditions($webhook, $params['chat']) === true) {
                erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, false, array('args' => $params));
            } elseif ($webhook->trigger_id_alt > 0) {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($webhook->trigger_id_alt);
                if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                    erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, false, array('args' => $params));
                }
            }
        }
    }

}

?>