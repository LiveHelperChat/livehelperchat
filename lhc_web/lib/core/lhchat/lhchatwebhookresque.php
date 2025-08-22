<?php
#[\AllowDynamicProperties]
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
            $cfg = \erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database'));

            $instance = \erLhcoreClassModelInstance::fetch($this->args['inst_id']);
            \erLhcoreClassInstance::$instanceChat = $instance;

            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $this->args['inst_id']);
        }

        // Helper tasks
        if (isset($this->args['event_type']) && $this->args['event_type'] == 'merge_vid') {
            try {
                erLhcoreClassChatHelper::mergeVid(['vid' => $this->args['old_vid'], 'new' => $this->args['new_vid']], true);
            } catch (Exception $e) {
                erLhcoreClassLog::write(
                    json_encode([
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTrace(),
                        'raw' => (string)$e,
                    ],JSON_PRETTY_PRINT)
                    ,
                    ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'lhc',
                        'category' => 'resque_exception',
                        'line' => __LINE__,
                        'file' => __FILE__,
                        'object_id' => 0
                    )
                );
            }
            return;
        }

        $hookId = $this->args['hook_id'];

        $webhook = erLhcoreClassModelChatWebhook::fetch($hookId);

        if (!is_object($webhook)) {
            return;
        }

        if ((int)$webhook->delay > 0 && (int)$webhook->delay <= 60) {
            sleep((int)$webhook->delay);
        }

        $triggerId = $webhook->trigger_id;

        $params = unserialize(gzinflate(base64_decode($this->args['params'])));

        // Webhook delay support
        if (isset($params['msg']) && isset($params['msg']->meta_msg)) {
            $paramsMetaMessage = json_decode($params['msg']->meta_msg, true);
            $params['msg']->meta_msg_array = $paramsMetaMessage;
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
            if (isset($params['mail']) && $params['mail'] instanceof erLhcoreClassModelMailconvMessage) {
                $params['mail'] = $params['chat'] = erLhcoreClassModelMailconvMessage::fetch($params['mail']->id);
                
                if (isset($params['conversation']) && $params['conversation'] instanceof erLhcoreClassModelMailconvConversation) {
                    $params['conversation'] = erLhcoreClassModelMailconvConversation::fetch($params['conversation']->id);
                }
                
            } else if (!isset($params['chat']) || !($params['chat'] instanceof erLhcoreClassModelChat)) {
                $params['chat'] = new erLhcoreClassModelChat();
                $params['chat']->id = -1;
            } else {
                $params['chat'] = erLhcoreClassModelChat::fetch($params['chat']->id);

                // Chat by ID not found
                if (!is_object($params['chat'])) {
                    return;
                }
            }


            $setLastMessage = false;

            if (isset($params['chat']) && $params['chat'] instanceof erLhcoreClassModelChat && isset($params['msg'])) {
                $params['chat']->last_message = $params['msg'];
            }

            $paramsExecution = [];

            if (isset($params['chat']) && $params['chat'] instanceof erLhcoreClassModelChat && $params['chat']->id > 0) {
                $setLastMessage = true;
                $paramsExecution = ['msg_last_id' => $params['chat']->last_msg_id];
            }

            $params['override_gbot_id'] = $trigger->bot_id;

            erLhcoreClassGenericBotWorkflow::$auditCategory = 'bot_webhook';

            if (erLhcoreClassChatWebhookHttp::isValidConditions($webhook, $params['chat']) === true) {
                $lastMessage = erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, false, array('set_last_msg_id' => $setLastMessage, 'args' => $params));
            } elseif ($webhook->trigger_id_alt > 0) {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($webhook->trigger_id_alt);
                if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
                    $lastMessage = erLhcoreClassGenericBotWorkflow::processTrigger($params['chat'], $trigger, false, array('set_last_msg_id' => $setLastMessage, 'args' => $params));
                }
            }

            // For NodeJS to inform operators about new message
            if ($setLastMessage === true && isset($lastMessage) && $lastMessage instanceof erLhcoreClassModelmsg && $lastMessage->id > 0) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_passive', array(
                    'chat' => & $params['chat'],
                    'msg' => $lastMessage,
                    'source' => 'webhook_worker'
                ));
            }

            if ($setLastMessage === true && (!isset($params['no_auto_events']) || $params['no_auto_events'] === false)) {
                erLhcoreClassChatWebhookContinuous::dispatchEvents($params['chat'], $paramsExecution);
            }
        }
    }

}

?>