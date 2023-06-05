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
                     $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
                     erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_rest_webhook', 'erLhcoreClassChatWebhookResque', array('inst_id' => $inst_id, 'hook_id' => $hookId, 'params' => base64_encode(gzdeflate(serialize($params)))));
                }
            }
        }
    }

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if (isset($this->args['inst_id']) && $this->args['inst_id'] > 0) {
            $cfg = erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $this->args['inst_id']);
        }

        // Helper tasks
        if (isset($this->args['event_type']) && $this->args['event_type'] == 'merge_vid') {
            erLhcoreClassChatHelper::mergeVid(['vid' => $this->args['old_vid'], 'new' => $this->args['new_vid']], true);
            return;
        }

        $hookId = $this->args['hook_id'];

        $webhook = erLhcoreClassModelChatWebhook::fetch($hookId);

        $triggerId = $webhook->trigger_id;

        $params = unserialize(gzinflate(base64_decode($this->args['params'])));

        // Webhook delay support
        if (isset($params['msg']) && isset($params['msg']->meta_msg)) {
            $paramsMetaMessage = json_decode($params['msg']->meta_msg, true);
            if (isset($paramsMetaMessage['content']['attr_options']['wh_delay']) &&
                is_numeric($paramsMetaMessage['content']['attr_options']['wh_delay']) &&
                (int)$paramsMetaMessage['content']['attr_options']['wh_delay'] > 0 &&
                (int)$paramsMetaMessage['content']['attr_options']['wh_delay'] <= 30
            ) {
                sleep((int)$paramsMetaMessage['content']['attr_options']['wh_delay']);
            }
        }

        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($triggerId);

        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {

            // processTrigger always requires a chat so fake it.
            if (!isset($params['chat']) || !($params['chat'] instanceof erLhcoreClassModelChat)) {
                $params['chat'] = new erLhcoreClassModelChat();
                $params['chat']->id = -1;
            } else {
                $params['chat'] = erLhcoreClassModelChat::fetch($params['chat']->id);
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