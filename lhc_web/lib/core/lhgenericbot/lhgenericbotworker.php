<?php
/**
 * Generic bot worker
 *
 * */
class erLhcoreClassLHCBotWorker
{
    public function __construct()
    {

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

        $eventId = $this->args['event_id'];

        $event = erLhcoreClassModelGenericBotChatEvent::fetch($eventId);

        // Event does not exists
        // This can happen if insert operator takes long time
        // We retry after 5 seconds
        if (!($event instanceof erLhcoreClassModelGenericBotChatEvent)) {

            sleep(5);

            $db->reconnect();
            $event = erLhcoreClassModelGenericBotChatEvent::fetch($eventId, false);

            if (!($event instanceof erLhcoreClassModelGenericBotChatEvent)) {
                erLhcoreClassLog::write('',
                    ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'Bot',
                        'category' => 'missing_event',
                        'line' => __LINE__,
                        'file' => __FILE__,
                        'object_id' => $this->args['event_id']
                    )
                );

                return;
            }
        }

        $chat = erLhcoreClassModelChat::fetch($event->chat_id);

        try {
            if ($this->args['action'] == 'rest_api') {

                $contentArray = $event->content_array['callback_list'];

                $action = $contentArray[0]['content']['action'];

                $restAPI = erLhcoreClassModelGenericBotRestAPI::fetch($action['content']['rest_api']);

                if ($restAPI instanceof erLhcoreClassModelGenericBotRestAPI) {

                    $method = false;

                    foreach ($restAPI->configuration_array['parameters'] as $parameter) {
                        if ($action['content']['rest_api_method'] == $parameter['id']) {
                            $method = $parameter;
                        }
                    }

                    $params = array();

                    $msgId = $contentArray[0]['content']['msg_id'];

                    if ($msgId > 0) {
                        $params['msg'] = erLhcoreClassModelmsg::fetch($msgId);
                    } else {
                        $params['msg_text'] = $contentArray[0]['content']['msg_text'];
                        $msgId = $chat->last_msg_id;
                    }

                    $msgId = max($msgId, $chat->last_msg_id);

                    $params['start_mode'] = isset($contentArray[0]['content']['start_mode']) && $contentArray[0]['content']['start_mode'] == true;

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.rest_api_before_request', array(
                        'restapi' => & $restAPI,
                        'chat' => $chat
                    ));

                    $params['chat'] = $chat;

                    if (isset($contentArray[0]['content']['replace_array']) && !empty($contentArray[0]['content']['replace_array'])) {
                        $params['replace_array'] = $contentArray[0]['content']['replace_array'];
                        foreach ($params['replace_array'] as $key => $value) {
                            $params['replace_array'][str_replace(['{','}'],'',$key)] = $value;
                            if (str_starts_with($key, '{arg_') && !isset($params[str_replace(['{','}'],'',$key)])) {
                                $params[str_replace(['{','}'],'',$key)] = $value;
                            }
                        }
                    }

                    if (
                        isset($method['polling_n_times']) && (int)$method['polling_n_times'] >= 1 && $method['polling_n_times'] <= 10 &&
                        isset($method['polling_n_delay']) && (int)$method['polling_n_delay'] >= 1 && $method['polling_n_delay'] <= 10
                    ) {
                        for ($i = 0; $i < (int)$method['polling_n_times']; $i++) {
                            sleep($method['polling_n_delay']);
                            $response = erLhcoreClassGenericBotActionRestapi::makeRequest($restAPI->configuration_array['host'], $method, array('rest_api' => $restAPI, 'action' => $action, 'rest_api_method_params' => $action['content']['rest_api_method_params'], 'chat' => $chat, 'params' => $params));
                            // Request succeeded we can exit a loop
                            if (isset($response['conditions_met']) && $response['conditions_met'] == true) {
                                break;
                            }
                        }
                    } else {
                        $response = erLhcoreClassGenericBotActionRestapi::makeRequest($restAPI->configuration_array['host'], $method, array('rest_api' => $restAPI, 'action' => $action, 'rest_api_method_params' => $action['content']['rest_api_method_params'], 'chat' => $chat, 'params' => $params));
                    }

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.rest_api_after_request', array(
                        'restapi' => & $restAPI,
                        'chat' => $chat,
                        'params' => $params,
                        'method' => & $method,
                        'response' => & $response
                    ));

                    // Store remote message
                    if (
                        isset($method['remote_message_id']) &&
                        $method['remote_message_id'] != '' &&
                        isset($response['content_raw'])
                    ) {
                        $contentRawParsed = json_decode($response['content_raw'], true);
                        $remoteMessageId = erLhcoreClassGenericBotActionRestapi::extractAttribute($contentRawParsed, $method['remote_message_id']);
                        if ($remoteMessageId['found'] === true && isset($params['msg']) && is_object($params['msg'])) {
                            $db = ezcDbInstance::get();
                            try {
                                $db->beginTransaction();

                                $params['msg']->syncAndLock();

                                $meta_msg_array = $params['msg']->meta_msg_array;
                                $meta_msg_array['iwh_msg_id'] = $remoteMessageId['value'];
                                $params['msg']->meta_msg_array = $meta_msg_array;
                                $params['msg']->meta_msg = json_encode($meta_msg_array);
                                $params['msg']->del_st = erLhcoreClassModelmsg::STATUS_PENDING;

                                if ($params['msg']->id > 0) {
                                    $params['msg']->updateThis(['update' => ['meta_msg','del_st']]);
                                }

                                $db->commit();
                            } catch (Exception $e) {
                                $db->rollback();
                            }
                        }
                    }

                    $event->removeThis();

                    // We have found exact matching response type
                    // Let's check has user checked any trigger to execute.
                    if (isset($response['id'])) {
                        if (isset($action['content']['rest_api_method_output'][$response['id']]) && is_numeric($action['content']['rest_api_method_output'][$response['id']])) {
                            $argsDefault = array('args' => array(
                                'meta_msg' => $response['meta'],
                                'replace_array' => array(
                                    '{content_1}' => $response['content'],
                                    '{content_2}' => $response['content_2'],
                                    '{content_3}' => $response['content_3'],
                                    '{content_4}' => $response['content_4'],
                                    '{content_5}' => $response['content_5'],
                                    '{content_6}' => $response['content_6'],
                                    '{content_1_json}' => json_encode($response['content']),
                                    '{content_2_json}' => json_encode($response['content_2']),
                                    '{content_3_json}' => json_encode($response['content_3']),
                                    '{content_4_json}' => json_encode($response['content_4']),
                                    '{content_5_json}' => json_encode($response['content_5']),
                                    '{content_6_json}' => json_encode($response['content_6']),
                                    '{http_code}' => $response['http_code'],
                                    '{http_error}' => $response['http_error'],
                                    '{content_raw}' => $response['content_raw'],
                                    '{http_data}' => $response['http_data']
                                )));

                            if (isset($params['msg'])) {
                                $argsDefault['args']['msg'] = $params['msg'];
                            } else {
                                $argsDefault['args']['msg_text'] = $contentArray[0]['content']['msg_text'];
                            }
                            
                            if (isset($params['start_mode']) && $params['start_mode'] == true) {
                                $argsDefault['args']['start_mode'] = true;
                            }

                            if (isset($contentArray[0]['content']['replace_array']) && !empty($contentArray[0]['content']['replace_array'])) {
                                $argsDefault['args']['replace_array'] = array_merge($contentArray[0]['content']['replace_array'], $argsDefault['args']['replace_array']);
                            }

                            self::processTrigger($chat, $action['content']['rest_api_method_output'][$response['id']], $argsDefault);

                            if (class_exists('erLhcoreClassNodeJSRedis')) {
                                erLhcoreClassNodeJSRedis::instance()->publish('chat_' . $chat->id, 'o:' . json_encode(array('op' => 'cmsg')));
                            }

                            $msgLast = erLhcoreClassModelmsg::fetch($msgId);

                            if ($msgLast instanceof erLhcoreClassModelmsg) {
                                erLhcoreClassChatWebhookIncoming::sendBotResponse($chat, $msgLast, ['init' => true]);
                            }

                            return;

                        } else {
                            // Do nothing as user did not chose any trigger to execute
                        }
                    } elseif (isset($action['content']['rest_api_method_output']['default_trigger']) && is_numeric($action['content']['rest_api_method_output']['default_trigger'])) {

                        $argsDefault = array(
                            'args' => array(
                                'meta_msg' => $response['meta'],
                                'replace_array' => array(
                                    '{content_1}' => $response['content'],
                                    '{content_2}' => $response['content_2'],
                                    '{content_3}' => $response['content_3'],
                                    '{content_4}' => $response['content_4'],
                                    '{content_5}' => $response['content_5'],
                                    '{content_6}' => $response['content_6'],
                                    '{content_1_json}' => json_encode($response['content']),
                                    '{content_2_json}' => json_encode($response['content_2']),
                                    '{content_3_json}' => json_encode($response['content_3']),
                                    '{content_4_json}' => json_encode($response['content_4']),
                                    '{content_5_json}' => json_encode($response['content_5']),
                                    '{content_6_json}' => json_encode($response['content_6']),
                                    '{http_code}' => $response['http_code'],
                                    '{http_error}' => $response['http_error'],
                                    '{content_raw}' => $response['content_raw'],
                                    '{http_data}' => $response['http_data']
                                ),
                            ),
                            'trigger_id' => $action['content']['rest_api_method_output']['default_trigger'],
                            'trigger_action_id' => (isset($action['content']['rest_api_method_output']['default_trigger_action_id']) ? $action['content']['rest_api_method_output']['default_trigger_action_id'] : null)
                        );

                        if (isset($params['msg'])) {
                            $argsDefault['args']['msg'] = $params['msg'];
                        } else {
                            $argsDefault['args']['msg_text'] = $contentArray[0]['content']['msg_text'];
                        }

                        if (isset($contentArray[0]['content']['replace_array']) && !empty($contentArray[0]['content']['replace_array'])) {
                            $argsDefault['args']['replace_array'] = array_merge($contentArray[0]['content']['replace_array'], $argsDefault['args']['replace_array']);
                        }

                        if (isset($params['start_mode']) && $params['start_mode'] == true) {
                            $argsDefault['args']['start_mode'] = true;
                        }

                        // Alternative trigger, most of the time just for logging purposes
                        if (isset($action['content']['rest_api_method_output']['default_trigger_alt']) && is_numeric($action['content']['rest_api_method_output']['default_trigger_alt'])) {
                            self::processTrigger($chat, $action['content']['rest_api_method_output']['default_trigger_alt'], $argsDefault);
                        }

                        self::processTrigger($chat, $action['content']['rest_api_method_output']['default_trigger'], $argsDefault);

                        if (class_exists('erLhcoreClassNodeJSRedis')) {
                            erLhcoreClassNodeJSRedis::instance()->publish('chat_' . $chat->id, 'o:' . json_encode(array('op' => 'cmsg')));
                        }

                        $msgLast = erLhcoreClassModelmsg::fetch($msgId);

                        if ($msgLast instanceof erLhcoreClassModelmsg) {
                            erLhcoreClassChatWebhookIncoming::sendBotResponse($chat, $msgLast, ['init' => true]);
                        }

                        $chatVariables = $chat->chat_variables_array;

                        return;
                    }

                    if ($response['content'] != '' || (isset($response['meta']) && !empty($response['meta']))){
                        $msg = new erLhcoreClassModelmsg();
                        $msg->chat_id = $chat->id;
                        $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
                        $msg->user_id = -2;
                        $msg->time = time() + 1;
                        $msg->meta_msg = (isset($response['meta']) && !empty($response['meta'])) ? json_encode($response['meta']) : '';
                        $msg->msg = $response['content'];
                        $msg->saveThis();

                        $chat->last_msg_id = $msg->id;
                        $chat->updateThis(array('update' => array('last_msg_id' )));

                        $msgLast = erLhcoreClassModelmsg::fetch($msgId);

                        if ($msgLast instanceof erLhcoreClassModelmsg) {
                            erLhcoreClassChatWebhookIncoming::sendBotResponse($chat, $msgLast, ['init' => true]);
                        }

                        if (class_exists('erLhcoreClassNodeJSRedis')) {
                            erLhcoreClassNodeJSRedis::instance()->publish('chat_' . $chat->id, 'o:' . json_encode(array('op' => 'cmsg')));
                        }
                    }
                }
            }

        } catch (Exception $e) {
            self::logIfRequiredPlain($chat, 'rest_api', $e->getMessage());
        }

        return false;
    }

    private function processTrigger(& $chat, $triggerId, $paramsTrigger = array())
    {
        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($triggerId);
        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
            erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, $paramsTrigger);
        }
    }

    public static function logIfRequiredPlain($chat, $category = 'rest_api', $message = '') {
        erLhcoreClassLog::write(
            $message,
            ezcLog::SUCCESS_AUDIT,
            array(
                'source' => 'Bot',
                'category' => $category,
                'line' => __LINE__,
                'file' => __FILE__,
                'object_id' => $chat->id
            )
        );
    }
}
