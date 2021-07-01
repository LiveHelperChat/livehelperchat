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
            $cfg = erConfigClassLhConfig::getInstance();
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
                        'object_id' => $event->id
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
                    }

                    $params['start_mode'] = isset($contentArray[0]['content']['start_mode']) && $contentArray[0]['content']['start_mode'] == true;

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.rest_api_before_request', array(
                        'restapi' => & $restAPI,
                        'chat' => $chat
                    ));

                    $response = erLhcoreClassGenericBotActionRestapi::makeRequest($restAPI->configuration_array['host'], $method, array('action' => $action, 'rest_api_method_params' => $action['content']['rest_api_method_params'], 'chat' => $chat, 'params' => $params));

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

                            self::processTrigger($chat, $action['content']['rest_api_method_output'][$response['id']], $argsDefault);

                            if (class_exists('erLhcoreClassNodeJSRedis')) {
                                erLhcoreClassNodeJSRedis::instance()->publish('chat_' . $chat->id, 'o:' . json_encode(array('op' => 'cmsg')));
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
                                )));

                        if (isset($params['msg'])) {
                            $argsDefault['args']['msg'] = $params['msg'];
                        } else {
                            $argsDefault['args']['msg_text'] = $contentArray[0]['content']['msg_text'];
                        }

                        if (isset($params['start_mode']) && $params['start_mode'] == true) {
                            $argsDefault['args']['start_mode'] = true;
                        }

                        self::processTrigger($chat, $action['content']['rest_api_method_output']['default_trigger'], $argsDefault);

                        if (class_exists('erLhcoreClassNodeJSRedis')) {
                            erLhcoreClassNodeJSRedis::instance()->publish('chat_' . $chat->id, 'o:' . json_encode(array('op' => 'cmsg')));
                        }

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
