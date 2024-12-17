<?php

class erLhcoreClassGenericBotActionRestapi
{

    public static function process($chat, $action, $trigger, $params)
    {

        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }
        
        if (isset($action['content']['rest_api']) && is_numeric($action['content']['rest_api']) && isset($action['content']['rest_api_method']) && !empty($action['content']['rest_api_method'])) {

            if (isset($params['rest_api_object'])) {
                $restAPI = $params['rest_api_object'];
            } else {
                $restAPI = erLhcoreClassModelGenericBotRestAPI::fetch($action['content']['rest_api']);
            }

            if ($restAPI instanceof erLhcoreClassModelGenericBotRestAPI) {

                // Within next user message we will process this event
                if (isset($action['content']['attr_options']['on_next_msg']) && $action['content']['attr_options']['on_next_msg'] == true) {

                    unset($action['content']['attr_options']['on_next_msg']);
                    $event = new erLhcoreClassModelGenericBotChatEvent();
                    $event->chat_id = $chat->id;
                    $event->ctime = time();
                    $event->content = json_encode(array('callback_list' => array(
                        array(
                            'content' => array(
                                'type' => 'rest_api_next_msg',
                                'replace_array' => (isset($params['replace_array']) ? $params['replace_array'] : array()),
                                'content' => $action['content']
                            )
                        )
                    )));

                    if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                        $event->saveThis();
                    }

                    return;
                }

                $method = false;
                foreach ($restAPI->configuration_array['parameters'] as $parameter) {
                    if ($action['content']['rest_api_method'] == $parameter['id']) {
                        $method = $parameter;
                    }
                }

                // Callback should be executed as background task
                if (
                    (
                        (isset($action['content']['attr_options']['background_process']) && $action['content']['attr_options']['background_process'] == true)
                            ||
                        (isset($action['content']['attr_options']['background_process_delegate']) && $action['content']['attr_options']['background_process_delegate'] == true && erLhcoreClassSystem::instance()->backgroundMode === false)
                            ||
                        (erLhcoreClassSystem::instance()->backgroundMode === false && class_exists('erLhcoreClassInstance')) // Always delegate automated hosting request to background worker if we are not in background already
                    )
                    && class_exists('erLhcoreClassExtensionLhcphpresque')
                ) {

                    $event = new erLhcoreClassModelGenericBotChatEvent();
                    $event->chat_id = $chat->id;
                    $event->ctime = time();
                    $event->content = json_encode(array('callback_list' => array(
                        array(
                            'content' => array(
                                'type' => 'rest_api',
                                'start_mode' => erLhcoreClassGenericBotWorkflow::$startChat,
                                'replace_array' => (isset($params['replace_array']) ? $params['replace_array'] : array()),
                                'action' => $action,
                                'msg_id' => (isset($params['msg']) && is_object($params['msg']) ? $params['msg']->id : 0),
                                'msg_text' => (isset($params['msg_text']) ? $params['msg_text'] : ''),
                                'event' => (isset($action['content']['event']) ? $action['content']['event'] : null),
                            )
                        )
                    )));

                    // Save only if user has resque extension
                    if ((!isset($params['do_not_save']) || $params['do_not_save'] == false)) {
                        $event->saveThis();
                        $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
                        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_rest_api_queue', 'erLhcoreClassLHCBotWorker', array('inst_id' => $inst_id, 'action' => 'rest_api', 'event_id' => $event->id));
                        return ;
                    }
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.rest_api_before_request', array(
                    'restapi' => & $restAPI,
                    'chat' => $chat,
                    'params' => $params,
                    'method' => & $method
                ));

                if (!empty($action['content']['attr_options']['custom_args_1'])) {
                    if (isset($params['replace_array'])) {
                        foreach ($params['replace_array'] as $keyReplace => $valueReplace) {
                            if (is_object($valueReplace) || is_array($valueReplace)) {
                                $action['content']['attr_options']['custom_args_1'] = @str_replace($keyReplace,json_encode($valueReplace),$action['content']['attr_options']['custom_args_1']);
                            } else {
                                $action['content']['attr_options']['custom_args_1'] = @str_replace($keyReplace,$valueReplace,$action['content']['attr_options']['custom_args_1']);
                            }
                        }
                    }
                    $action['content']['attr_options']['custom_args_1'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['attr_options']['custom_args_1'], array('chat' => $chat, 'args' => $params));
                }

                if (
                    isset($method['polling_n_times']) && (int)$method['polling_n_times'] >= 1 && $method['polling_n_times'] <= 10 &&
                    isset($method['polling_n_delay']) && (int)$method['polling_n_delay'] >= 1 && $method['polling_n_delay'] <= 10
                ) {
                    for ($i = 0; $i < (int)$method['polling_n_times']; $i++) {
                        sleep($method['polling_n_delay']);
                        $response = self::makeRequest($restAPI->configuration_array['host'], $method, array('rest_api' => $restAPI, 'action' => $action, 'rest_api_method_params' => $action['content']['rest_api_method_params'], 'chat' => $chat, 'params' => $params));
                        // Request succeeded we can exit a loop
                        if (isset($response['conditions_met']) && $response['conditions_met'] == true) {
                            break;
                        }
                    }
                } else {
                    $response = self::makeRequest($restAPI->configuration_array['host'], $method, array('rest_api' => $restAPI, 'action' => $action, 'rest_api_method_params' => $action['content']['rest_api_method_params'], 'chat' => $chat, 'params' => $params));
                }

                // Log if polling conditions fails
                if (isset($method['polling_n_times']) && (int)$method['polling_n_times'] >= 1 && $method['polling_n_times'] <= 10 && isset($response['conditions_met']) && $response['conditions_met'] !== true) {
                    if (isset($restAPI->configuration_array['log_audit']) && $restAPI->configuration_array['log_audit']) {
                        erLhcoreClassLog::write(
                            json_encode([
                                'name' => '(polling conditions failed) [' . $restAPI->name . '] ' . (isset($method['name']) ? $method['name'] : 'unknwon_name'),
                                'http_code' => (isset($response['http_code']) ? $response['http_code'] : 'unknown'),
                                'method' => (isset($method['method']) ? $method['method'] : 'unknwon'),
                                'request_type' => (isset($method['body_request_type']) ? $method['body_request_type'] : ''),
                                'params_request' => $response['params_request'],
                                'return_content' => $response['content_raw'],
                                'http_error' => $response['http_error'],
                                'msg_id' => (isset($params['msg']) && is_object($params['msg'])) ? $params['msg']->id : 0,
                                'msg_text' => '-',
                            ], JSON_PRETTY_PRINT),
                            ezcLog::SUCCESS_AUDIT,
                            array(
                                'source' => 'Bot',
                                'category' => 'rest_api',
                                'line' => __LINE__,
                                'file' => __FILE__,
                                'object_id' => (isset($chat) && is_object($chat) ? $chat->id : 0)
                            )
                        );
                    }
                    if (isset($restAPI->configuration_array['log_system']) && $restAPI->configuration_array['log_system'] && isset($chat) && is_object($chat)) {
                        $msgLog = new erLhcoreClassModelmsg();
                        $msgLog->user_id = -1;
                        $msgLog->chat_id = $chat->id;
                        $msgLog->time = time();
                        $msgLog->meta_msg = json_encode(['content' => ['html' => [
                            'debug' => true,
                            'content' => json_encode([
                                'name' => '[' . $restAPI->name . '] ' . (isset($method['name']) ? $method['name'] : 'unknwon_name'),
                                'http_code' => (isset($response['http_code']) ? $response['http_code'] : 'unknown'),
                                'method' => (isset($method['method']) ? $method['method'] : 'unknwon'),
                                'request_type' => (isset($method['body_request_type']) ? $method['body_request_type'] : ''),
                                'params_request' => $response['params_request'],
                                'return_content' => $response['content_raw'],
                                'http_error' => $response['http_error'],
                                'msg_id' =>  (isset($params['msg']) && is_object($params['msg'])) ?  $params['msg']->id : 0,
                                'msg_text' => '-',
                            ],JSON_PRETTY_PRINT)]]]);
                        $msgLog->msg = '(polling conditions failed) [' . $restAPI->name . '] ' . '[i]'.(isset($method['name']) ? $method['name'] : 'unknown_name').'[/i]';
                        $msgLog->saveThis();
                    }
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
                    $remoteMessageId = self::extractAttribute($contentRawParsed, $method['remote_message_id']);
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


                // We have found exact matching response type
                // Let's check has user checked any trigger to execute.
                if (isset($response['id'])) {
                    if (isset($action['content']['rest_api_method_output'][$response['id']]) && is_numeric($action['content']['rest_api_method_output'][$response['id']])) {

                        $argsDefault = array(
                            'status' => 'continue_all',
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
                            'meta_msg' => $response['meta'],
                            'trigger_id' => $action['content']['rest_api_method_output'][$response['id']]
                        );

                        if (isset($params['msg'])) {
                            $argsDefault['msg'] = $params['msg'];
                        } elseif (isset($params['msg_text'])) {
                            $argsDefault['msg_text'] = $params['msg_text'];
                        }

                        if (isset($params['replace_array']) && !empty($params['replace_array'])) {
                            $argsDefault['replace_array'] = array_merge($params['replace_array'], $argsDefault['replace_array']);
                        }

                        return $argsDefault;

                    } else {
                        // Do nothing as user did not choose any trigger to execute
                    }
                } elseif (isset($action['content']['rest_api_method_output']['default_trigger']) && is_numeric($action['content']['rest_api_method_output']['default_trigger'])) {

                    $argsDefault = array(
                        'status' => 'continue_all',
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
                        'meta_msg' => $response['meta'],
                        'trigger_id' => $action['content']['rest_api_method_output']['default_trigger'],
                        'trigger_action_id' => (isset($action['content']['rest_api_method_output']['default_trigger_action_id']) && !empty($action['content']['rest_api_method_output']['default_trigger_action_id']) ? $action['content']['rest_api_method_output']['default_trigger_action_id'] : null)
                    );

                    if (isset($params['msg'])) {
                        $argsDefault['msg'] = $params['msg'];
                    } elseif (isset($params['msg_text'])) {
                        $argsDefault['msg_text'] = $params['msg_text'];
                    }
                    
                    if (isset($params['replace_array']) && !empty($params['replace_array'])) {
                        $argsDefault['replace_array'] = array_merge($params['replace_array'], $argsDefault['replace_array']);
                    }

                    // Alternative trigger, most of the time just for logging purposes
                    if (isset($action['content']['rest_api_method_output']['default_trigger_alt']) && is_numeric($action['content']['rest_api_method_output']['default_trigger_alt'])) {
                        $triggerDefaultAlt = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['rest_api_method_output']['default_trigger_alt']);
                        if ($triggerDefaultAlt instanceof erLhcoreClassModelGenericBotTrigger) {
                            erLhcoreClassGenericBotWorkflow::processTrigger($chat, $triggerDefaultAlt, false, array('args' => $argsDefault));
                        }
                    }

                    return $argsDefault;
                }

                if ($response['content'] != '' || (isset($response['meta']) && !empty($response['meta']))){

                    if (isset($action['content']['attr_options']['no_body']) && $action['content']['attr_options']['no_body'] == true) {
                        return;
                    }

                    $msg = new erLhcoreClassModelmsg();
                    $msg->chat_id = $chat->id;
                    $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);

                    if (isset($action['content']['attr_options']['as_system']) && $action['content']['attr_options']['as_system'] == true) {
                        $msg->user_id = -1; // Save as system message
                    } else {
                        $msg->user_id = -2; // Save as bot message
                    }

                    $msg->time = time();

                    if (erLhcoreClassGenericBotWorkflow::$setBotFlow === false) {
                        $msg->time += 1;
                    }

                    foreach (['buttons','custom','progress'] as $contentType) {
                        if (isset($response['meta']['content'][$contentType])){
                            unset($response['meta']['content'][$contentType]);
                        }
                    }

                    $msg->meta_msg = (isset($response['meta']) && !empty($response['meta'])) ? json_encode($response['meta']) : '';
                    $msg->msg = $response['content'];

                    if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                        if ($msg->chat_id > 0) {
                            $msg->saveThis();
                        }
                    }

                    return  $msg;
                }
            }
        }
    }

    public static function trimOnce($string)
    {
        // Do not modify if it's not a string
        if (!is_string($string)) {
            return $string;
        }

        if ($string[0] == '"') $string = substr($string,1);
        if ($string[strlen($string)-1] == '"') $string = substr($string,0,strlen($string)-1);
        return $string;
    }

    public static function isValidMessage($string, $language = 'en') {

        if (trim($string) == '') {
            return true;
        }

        $temp = preg_split('/(\s+)/', trim($string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // More than one word means it's valid string in general
        if (count($temp) > 1) {
            return true;
        }

        $word = [];

        preg_match_all('/^\w+/is', $temp[0], $word);

        if (isset($word[0][0]) && function_exists('pspell_new')) {

            if (is_numeric($word[0][0])) {
                return false;
            }

            $pspell_link = pspell_new($language);

            if ($pspell_link !== false) {
                return pspell_check($pspell_link,$word[0][0]);
            }
        }

        return true;
    }

    public static function makeRequest($host, $methodSettings, $paramsCustomer)
    {
        $msg_text = '';

        if (isset($paramsCustomer['params']['msg'])) {
            if (is_object($paramsCustomer['params']['msg'])) {
                $msg_text = $paramsCustomer['params']['msg']->msg;
            } else {
                $msg_text = '';
            }
        } elseif (isset($paramsCustomer['params']['msg_text'])) {
            $msg_text = $paramsCustomer['params']['msg_text'];
        }

        // Allow extensions to preparse send message
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_parse_send', array('msg' => & $msg_text));

        $msg_text_cleaned_files = $msg_text_cleaned = $msg_text;

        if (isset($methodSettings['body_raw_file']) && $methodSettings['body_raw_file'] != '' && strpos($methodSettings['body_raw_file'],'{reply_to}') !== false) {
            $msg_text_cleaned_files = trim(preg_replace('#\[quote="?([0-9]+)"?\](.*?)\[/quote\]#ms','',$msg_text_cleaned));
        }
        
        // We have to extract attached files and send them separately
        $matches = array();
        preg_match_all('/\[file="?(.*?)"?\]/', $msg_text_cleaned_files, $matches);

        $media = array();
        $files = array();

        foreach ($matches[1] as $index => $body) {
            $parts = explode('_', $body);
            $fileID = $parts[0];
            $hash = $parts[1];
            try {
                $file = erLhcoreClassModelChatFile::fetch($fileID);
                if (is_object($file) && $hash == $file->security_hash) {

                    $url = erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$file->id}/{$hash}";;

                    $media[] = array(
                        'id' => $file->id,
                        'size' => $file->size,
                        'upload_name' => $file->upload_name,
                        'type' => $file->type,
                        'extension' => $file->extension,
                        'hash' => $hash,
                        'url' => $url
                    );

                    $msg_text_cleaned = str_replace($matches[0][$index],'',$msg_text_cleaned);

                    $files[] = $file;
                }

            } catch (Exception $e) {

            }
        }

        if (empty($media)) {
            $matches = array();

            preg_match_all('/\[img\](.*?)\[\/img\]/ms', $msg_text, $matches);

            foreach ($matches[1] as $index => $img) {
                $in = trim($img);

                $url = erLhcoreClassBBCode::esc_url($in);

                if ( empty($url) )
                    continue;

                $file = new erLhcoreClassModelChatFile();
                $file->remote_file = true;
                $file->remote_url = $url;

                $parts = explode('.',strtolower($file->remote_url));
                $extension = array_pop($parts);

                $extensionMimetype = array(
                    'jpeg' => 'image/jpeg',
                    'jpg' => 'image/jpg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                );

                $file->upload_name = (isset($extensionMimetype[$extension]) ? 'image.' . $extension : 'image.png');
                $file->extension = isset($extensionMimetype[$extension]) ? $extension : 'png';
                $file->type = (isset($extensionMimetype[$extension]) ? $extensionMimetype[$extension] : 'image/jpeg');

                $media[] = array(
                    'id' => null,
                    'size' => null,
                    'upload_name' =>  $file->upload_name,
                    'type' => $file->type,
                    'extension' => $file->extension,
                    'hash' => '',
                    'url' => $file->remote_url
                );

                $files[] = $file;

                $msg_text_cleaned = str_replace($matches[0][$index],'',$msg_text_cleaned);
            }
        }

        // Cleanup if file bodu has reply to api defined
        if (isset($methodSettings['body_raw_file']) && $methodSettings['body_raw_file'] != '' && count($files) == 1 && strpos($methodSettings['body_raw_file'],'{reply_to}') !== false) {
             $msg_text_cleaned = trim(preg_replace('#\[quote="?([0-9]+)"?\](.*?)\[/quote\]#ms','',$msg_text_cleaned));
        }

        // Allow extensions to preparse send message
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_parse_send_clean', array('msg' => & $msg_text_cleaned));

        $file_body = null;
        $file_url = null;
        $file_name = null;
        $file_size = null;
        $file_mime = null;

        $file_api = false;

        // Switch to file API if it's only one file send
        if (isset($methodSettings['body_raw_file']) && $methodSettings['body_raw_file'] != '' && count($files) == 1 && trim($msg_text_cleaned) == '') {
            foreach ($files as $mediaFile) {

                $file_api = false;
                $apiUsed = '';

                if (isset($methodSettings['suburl_file']) && !empty($methodSettings['suburl_file'])) {

                    $matchesExtension = [];
                    preg_match_all('/\{api_by_ext__(.*?)\}(.*?)\{\/api_by_ext\}/ms', $methodSettings['body_raw_file'],$matchesExtension);
                    $fileTypeByApi = [];
                    if (isset($matchesExtension[1]) && !empty($matchesExtension[1])) {
                        foreach ($matchesExtension[1] as $fileType) {
                            $fileTypeByApi = array_merge($fileTypeByApi,explode('_',$fileType));
                        }
                    }

                    if (in_array($mediaFile->extension,$fileTypeByApi)) {
                        $fileBodyRawFile = preg_replace('/\{file_api\}(.*?)\{\/file_api\}/ms','',$methodSettings['body_raw_file']);
                        $fileBodyRawFile = preg_replace('/\{video_api\}(.*?)\{\/video_api\}/ms','',$fileBodyRawFile);
                        $fileBodyRawFile = preg_replace('/\{image_api\}(.*?)\{\/image_api\}/ms','',$fileBodyRawFile);

                        $methodSettings['suburl'] = $methodSettings['suburl_file'];
                        $methodSettings['suburl'] = preg_replace('/\{file_api\}(.*?)\{\/file_api\}/ms','',$methodSettings['suburl']);
                        $methodSettings['suburl'] = preg_replace('/\{video_api\}(.*?)\{\/video_api\}/ms','',$methodSettings['suburl']);
                        $methodSettings['suburl'] = preg_replace('/\{image_api\}(.*?)\{\/image_api\}/ms','',$methodSettings['suburl']);

                        foreach ($matchesExtension[1] as $indexExtension => $fileType) {
                            $fileTypeByApi = explode('_',$fileType);
                            if (in_array($mediaFile->extension,$fileTypeByApi)) {
                                $fileBodyRawFile = str_replace($matchesExtension[0][$indexExtension],$matchesExtension[2][$indexExtension], $fileBodyRawFile);
                                $apiUsed = $fileType;
                            } else {
                                $fileBodyRawFile = str_replace($matchesExtension[0][$indexExtension],'', $fileBodyRawFile);
                            }
                        }

                        $matchesExtension = [];
                        preg_match_all('/\{api_by_ext__(.*?)\}(.*?)\{\/api_by_ext\}/ms', $methodSettings['suburl'],$matchesExtension);
                        foreach ($matchesExtension[1] as $indexExtension => $fileType) {
                            $fileTypeByApi = explode('_',$fileType);
                            if (in_array($mediaFile->extension,$fileTypeByApi)) {
                                $methodSettings['suburl'] = str_replace($matchesExtension[0][$indexExtension],$matchesExtension[2][$indexExtension], $methodSettings['suburl']);
                            } else {
                                $methodSettings['suburl'] = str_replace($matchesExtension[0][$indexExtension],'', $methodSettings['suburl']);
                            }
                        }

                        $methodSettings['body_raw_file'] = $fileBodyRawFile;
                        $file_api = true;

                    } elseif (in_array($mediaFile->type,['image/jpeg','image/png','image/gif'])) {
                        $fileBodyRawFile = preg_replace('/\{file_api\}(.*?)\{\/file_api\}/ms','',$methodSettings['body_raw_file']);
                        $fileBodyRawFile = preg_replace('/\{video_api\}(.*?)\{\/video_api\}/ms','',$fileBodyRawFile);
                        $fileBodyRawFile = preg_replace('/\{api_by_ext__(.*?)\}(.*?)\{\/api_by_ext\}/ms', '',$fileBodyRawFile);
                        $fileBodyRawFile = trim(str_replace(['{image_api}','{/image_api}'],'', $fileBodyRawFile));
                        $apiUsed = 'image_api';
                        if (!empty($fileBodyRawFile)) {
                            $methodSettings['suburl'] = $methodSettings['suburl_file'];
                            $methodSettings['suburl'] = preg_replace('/\{file_api\}(.*?)\{\/file_api\}/ms','',$methodSettings['suburl']);
                            $methodSettings['suburl'] = preg_replace('/\{video_api\}(.*?)\{\/video_api\}/ms','',$methodSettings['suburl']);
                            $methodSettings['suburl'] = preg_replace('/\{api_by_ext__(.*?)\}(.*?)\{\/api_by_ext\}/ms', '',$methodSettings['suburl']);
                            $methodSettings['suburl'] = str_replace(['{image_api}','{/image_api}'],'', $methodSettings['suburl']);
                            $methodSettings['body_raw_file'] = $fileBodyRawFile;
                            $file_api = true;
                        }
                    } elseif (in_array($mediaFile->type,['video/mp4'])) {
                        $fileBodyRawFile = preg_replace('/\{file_api\}(.*?)\{\/file_api\}/ms','',$methodSettings['body_raw_file']);
                        $fileBodyRawFile = preg_replace('/\{image_api\}(.*?)\{\/image_api\}/ms','',$fileBodyRawFile);
                        $fileBodyRawFile = preg_replace('/\{api_by_ext__(.*?)\}(.*?)\{\/api_by_ext\}/ms','',$fileBodyRawFile);
                        $fileBodyRawFile = trim(str_replace(['{video_api}','{/video_api}'],'', $fileBodyRawFile));
                        $apiUsed = 'video_api';
                        if (!empty($fileBodyRawFile)) {
                            $methodSettings['suburl'] = $methodSettings['suburl_file'];
                            $methodSettings['suburl'] = preg_replace('/\{file_api\}(.*?)\{\/file_api\}/ms','',$methodSettings['suburl']);
                            $methodSettings['suburl'] = preg_replace('/\{image_api\}(.*?)\{\/image_api\}/ms','',$methodSettings['suburl']);
                            $methodSettings['suburl'] = preg_replace('/\{api_by_ext__(.*?)\}(.*?)\{\/api_by_ext\}/ms','',$methodSettings['suburl']);
                            $methodSettings['suburl'] = str_replace(['{video_api}','{/video_api}'],'', $methodSettings['suburl']);
                            $methodSettings['body_raw_file'] = $fileBodyRawFile;
                            $file_api = true;
                        }
                    } else {
                        $fileBodyRawFile = preg_replace('/\{image_api\}(.*?)\{\/image_api\}/ms','',$methodSettings['body_raw_file']);
                        $fileBodyRawFile = preg_replace('/\{video_api\}(.*?)\{\/video_api\}/ms','',$fileBodyRawFile);
                        $fileBodyRawFile = preg_replace('/\{api_by_ext__(.*?)\}(.*?)\{\/api_by_ext\}/ms','',$fileBodyRawFile);
                        $fileBodyRawFile = trim(str_replace(['{file_api}','{/file_api}'],'', $fileBodyRawFile));
                        $apiUsed = 'file_api';
                        if (!empty($fileBodyRawFile)) {
                            $methodSettings['suburl'] = $methodSettings['suburl_file'];
                            $methodSettings['suburl'] = preg_replace('/\{image_api\}(.*?)\{\/image_api\}/ms','',$methodSettings['suburl']);
                            $methodSettings['suburl'] = preg_replace('/\{video_api\}(.*?)\{\/video_api\}/ms','',$methodSettings['suburl']);
                            $methodSettings['suburl'] = preg_replace('/\{api_by_ext__(.*?)\}(.*?)\{\/api_by_ext\}/ms','',$methodSettings['suburl']);
                            $methodSettings['suburl'] = str_replace(['{file_api}','{/file_api}'],'', $methodSettings['suburl']);
                            $methodSettings['body_raw_file'] = $fileBodyRawFile;
                            $file_api = true;
                        }
                    }
                    if (isset($methodSettings['suburl_file_convert']) && !empty($methodSettings['suburl_file_convert'])) {
                        $keysRequired = explode(',',str_replace(' ','',$methodSettings['suburl_file_convert']));
                        if ($apiUsed != '' && in_array($apiUsed,$keysRequired)) {
                            $methodSettings['body_request_type'] = 'form-data';
                            $fileMethodOverride = true;
                        }
                    }
                }

                $file_name = $mediaFile->upload_name;

                if ($mediaFile->remote_file !== true) {

                    if ((isset($methodSettings['body_raw_file']) && strpos($methodSettings['body_raw_file'],'{{file_body}}') !== false) || (isset($methodSettings['body_raw_file']) && strpos($methodSettings['body_raw_file'],'{{body_raw}}') !== false)) {
                        $file_body = 'data:'.$mediaFile->type.';base64,'.base64_encode(file_get_contents($mediaFile->file_path_server));
                    } else {
                        $file_body = '';
                    }

                    $file_size = $mediaFile->size;
                    $file_mime = $mediaFile->type;
                    $file_url = erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$mediaFile->id}/{$mediaFile->security_hash}";
                } else {
                    $file_size = $file->size;
                    $file_mime = $file->type;
                    $file_body = '';
                    if (strpos($file->remote_url,'http://') !== false || strpos($file->remote_url,'https://') !== false) {
                        $file_url = $file->remote_url;
                    } else {
                        $file_url = erLhcoreClassSystem::getHost() . $file->remote_url;
                    }
                }

                if (isset($fileMethodOverride) && $fileMethodOverride === true) {
                    $file_url = 'file_id_'.$mediaFile->id;
                }
             }
        }

        if (isset($paramsCustomer['params']['msg']) &&
            is_object($paramsCustomer['params']['msg']) &&
            isset($paramsCustomer['params']['msg']->meta_msg_array['content']['quick_replies']) &&
            !empty($paramsCustomer['params']['msg']->meta_msg_array['content']['quick_replies'])) {
            $methodSettings['body_raw'] = preg_replace('/\{plain_api\}(.*?)\{\/plain_api\}/ms','',$methodSettings['body_raw']);
            $methodSettings['body_raw'] = preg_replace('/\{buttons_generic\}(.*?)\{\/buttons_generic\}/ms','',$methodSettings['body_raw']);
            $methodSettings['body_raw'] = trim(str_replace(['{interactive_api}','{/interactive_api}'],'', $methodSettings['body_raw']));

            $matchCycles = [];
            preg_match_all('/{button_template}(.*?){\/button_template}/is',$methodSettings['body_raw'],$matchCycles);
            $buttonsArray = [];

            foreach ($paramsCustomer['params']['msg']->meta_msg_array['content']['quick_replies'] as $quickReplyButton) {
                if ($quickReplyButton['type'] == 'trigger' || $quickReplyButton['type'] == 'button') {
                    if (isset($matchCycles[1][0])) {

                        if (isset($quickReplyButton['content']['override_rest_api_button']) && $quickReplyButton['content']['override_rest_api_button'] === true) {
                            $buttonContent = $quickReplyButton['content']['rest_api_button'];
                        } else {
                            $buttonContent = preg_replace('/\{is_url\}(.*?)\{\/is_url\}/ms', '', $matchCycles[1][0]);
                            $buttonContent = trim(str_replace(['{is_button}', '{/is_button}', '{{rest_api_button}}'], ['', '', (isset($quickReplyButton['content']['rest_api_button']) ? $quickReplyButton['content']['rest_api_button'] : '')], $buttonContent));
                        }

                        $buttonsArray[] = str_replace(['{{button_payload}}','{{button_title}}'],[
                            json_encode(($quickReplyButton['type'] == 'button' ?  'bpayload__' : 'trigger__') . $quickReplyButton['content']['payload']. '__' . md5($quickReplyButton['content']['name']) .'__'.$paramsCustomer['params']['msg']->id),
                            json_encode($quickReplyButton['content']['name'])
                        ],$buttonContent);
                    }
                } elseif ($quickReplyButton['type'] == 'url') {
                    if (isset($matchCycles[1][0])) {

                        if (isset($quickReplyButton['content']['override_rest_api_button']) && $quickReplyButton['content']['override_rest_api_button'] === true) {
                            $buttonContent = $quickReplyButton['content']['rest_api_button'];
                        } else {
                            $buttonContent = preg_replace('/\{is_button\}(.*?)\{\/is_button\}/ms','',$matchCycles[1][0]);
                            $buttonContent = trim(str_replace(['{is_url}','{/is_url}','{{rest_api_button}}'],['','',(isset($quickReplyButton['content']['rest_api_button']) ? $quickReplyButton['content']['rest_api_button'] : '')], $buttonContent));
                        }

                        $buttonsArray[] = str_replace(['{{button_payload}}','{{button_title}}'],[
                            json_encode($quickReplyButton['content']['payload']),
                            json_encode($quickReplyButton['content']['name'].'ma,e')
                        ],$buttonContent);
                    }
                }
            }

            $methodSettings['body_raw'] = preg_replace('/{button_template}(.*?){\/button_template}/is',implode(',',$buttonsArray),$methodSettings['body_raw']);

        } elseif (isset($paramsCustomer['params']['msg']) &&
            is_object($paramsCustomer['params']['msg']) &&
            isset($paramsCustomer['params']['msg']->meta_msg_array['content']['buttons_generic']) &&
            !empty($paramsCustomer['params']['msg']->meta_msg_array['content']['buttons_generic'])) {

            $methodSettings['body_raw'] = preg_replace('/\{plain_api\}(.*?)\{\/plain_api\}/ms','',$methodSettings['body_raw']);
            $methodSettings['body_raw'] = preg_replace('/\{interactive_api\}(.*?)\{\/interactive_api\}/ms','',$methodSettings['body_raw']);
            $methodSettings['body_raw'] = trim(str_replace(['{buttons_generic}','{/buttons_generic}'],'', $methodSettings['body_raw']));

            $matchCycles = [];
            preg_match_all('/{button_template_generic}(.*?){\/button_template_generic}/is',$methodSettings['body_raw'],$matchCycles);
            $buttonsArray = [];

            $methodSettings['body_raw'] = trim(str_replace('{{button_more_information}}',
                json_encode(isset($paramsCustomer['params']['msg']->meta_msg_array['content']['attr_options']['btn_title']) ? $paramsCustomer['params']['msg']->meta_msg_array['content']['attr_options']['btn_title'] : 'More information')
                , $methodSettings['body_raw']));

            foreach ($paramsCustomer['params']['msg']->meta_msg_array['content']['buttons_generic'] as $quickReplyButton) {
                if ($quickReplyButton['type'] == 'trigger' || $quickReplyButton['type'] == 'button') {
                    if (isset($matchCycles[1][0])) {
                        $templateButton = $matchCycles[1][0];
                        $templateButton = preg_replace('/\{url_btn_payload\}(.*?)\{\/url_btn_payload\}/ms','',$templateButton);
                        $buttonsArray[] = str_replace(['{{button_payload}}','{{button_title}}','{trigger_btn_payload}','{/trigger_btn_payload}'],[
                            json_encode(($quickReplyButton['type'] == 'button' ?  'bpayload__' : 'trigger__') . $quickReplyButton['content']['payload']. '__' . md5($quickReplyButton['content']['name']) .'__'.$paramsCustomer['params']['msg']->id),
                            json_encode($quickReplyButton['content']['name']),
                            '',
                            ''
                        ],$templateButton);
                    }
                } elseif ($quickReplyButton['type'] == 'url') {
                    if (isset($matchCycles[1][0])) {
                        $templateButton = $matchCycles[1][0];
                        $templateButton = preg_replace('/\{trigger_btn_payload\}(.*?)\{\/trigger_btn_payload\}/ms','',$templateButton);
                        $buttonsArray[] = str_replace(['{{button_payload}}','{{button_title}}','{url_btn_payload}','{/url_btn_payload}'],[
                            json_encode($quickReplyButton['content']['payload']),
                            json_encode($quickReplyButton['content']['name']),
                            '',
                            ''
                        ],$templateButton);
                    }
                }
            }

            $methodSettings['body_raw'] = preg_replace('/{button_template_generic}(.*?){\/button_template_generic}/is',implode(',',$buttonsArray),$methodSettings['body_raw']);

        } else {
            if (isset($methodSettings['body_raw'])) {
                $methodSettings['body_raw'] = preg_replace('/\{interactive_api\}(.*?)\{\/interactive_api\}/ms','',$methodSettings['body_raw']);
                $methodSettings['body_raw'] = preg_replace('/\{buttons_generic\}(.*?)\{\/buttons_generic\}/ms','',$methodSettings['body_raw']);
                $methodSettings['body_raw'] = trim(str_replace(['{plain_api}','{/plain_api}'],'', $methodSettings['body_raw']));
            }
        }

        if (!isset($paramsCustomer['params']['chat'])) {
            $paramsCustomer['params']['chat'] = $paramsCustomer['chat'];
        }

        $dynamicParamsVariables = self::extractDynamicParams($methodSettings, $paramsCustomer['params']);

        $dynamicReplaceVariables = self::extractDynamicVariables($methodSettings, $paramsCustomer['chat']);

        if (!isset($methodSettings['body_raw'])) {
            $methodSettings['body_raw'] = '';
        }

        // Handle previous visitor messages
        if (isset($dynamicReplaceVariables['{if_previous_visitor_messages}']) && $dynamicReplaceVariables['{if_previous_visitor_messages}'] === true) {
            $methodSettings['body_raw'] = trim(str_replace(['{if_previous_visitor_messages}','{/if_previous_visitor_messages}'],'', $methodSettings['body_raw']));
        } else {
            $methodSettings['body_raw'] = preg_replace('/\{if_previous_visitor_messages\}(.*?)\{\/if_previous_visitor_messages\}/ms','',$methodSettings['body_raw']);
        }

        if (isset($methodSettings['check_word']) && $methodSettings['check_word'] == true) {

            $locale = $paramsCustomer['chat']->chat_locale;

            if (empty($locale)) {
                $locale = 'en';
            } else {
                $locale = explode('-',$locale)[0];
            }

            if (!self::isValidMessage($msg_text, $locale)) {
                $msg_text = '';
            }

            if (!self::isValidMessage($msg_text_cleaned, $locale)) {
                $msg_text_cleaned = '';
            }
        }

        if ($file_api === true) {
            $methodSettings['body_raw_file'] = self::processReplyTo($methodSettings['body_raw_file'], $msg_text);
        } else {
            $methodSettings['body_raw'] = self::processReplyTo($methodSettings['body_raw'], $msg_text);
        }

        $replaceVariables = array(
            '{{msg}}' => $msg_text,
            '{{msg_shortened_256}}' => substr($msg_text,0,254),
            '{{msg_lowercase}}' => mb_strtolower($msg_text),
            '{{msg_clean}}' => trim($msg_text_cleaned),
            '{{msg_clean_lowercase}}' => mb_strtolower(trim($msg_text_cleaned)),
            '{{msg_url}}' => erLhcoreClassBBCodePlain::make_clickable($msg_text, array('sender' => 0, 'clean_event' => true)),
            '{{msg_url_lowercase}}' => erLhcoreClassBBCodePlain::make_clickable(mb_strtolower($msg_text), array('sender' => 0, 'clean_event' => true)),
            '{{msg_html}}' => erLhcoreClassBBCode::make_clickable($msg_text, array('sender' => 0)),
            '{{msg_html_nobr}}' => str_replace("<br />",'',erLhcoreClassBBCode::make_clickable($msg_text, array('sender' => 0, 'clean_event' => true))),
            '{{msg_html_lowercase}}' => erLhcoreClassBBCode::make_clickable(mb_strtolower($msg_text), array('sender' => 0)),
            '{{msg_html_lowercase_nobr}}' => str_replace("<br />",'',erLhcoreClassBBCode::make_clickable(mb_strtolower($msg_text), array('sender' => 0))),
            '{{chat_id}}' => $paramsCustomer['chat']->id,
            '{{lhc.nick}}' =>$paramsCustomer['chat']->nick,
            '{{lhc.email}}' => $paramsCustomer['chat']->email,
            '{{lhc.department}}' => (string)$paramsCustomer['chat']->department,
            '{{lhc.dep_id}}' => (string)$paramsCustomer['chat']->dep_id,
            '{{ip}}' => (string)erLhcoreClassIPDetect::getIP(),
            '{{media}}' => json_encode($media),
            '{{file_body}}' => $file_body,
            '{{file_url}}' => $file_url,
            '{{file_name}}' => $file_name,
            '{{file_size}}' => $file_size,
            '{{file_mime}}' => $file_mime,
            '{{timestamp}}' => time(),
            '{{date_utc}}' => (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s T'),
            '{{custom_args_1}}' => isset($paramsCustomer['action']['content']['attr_options']['custom_args_1']) ? $paramsCustomer['action']['content']['attr_options']['custom_args_1'] : null
        );

        $replaceVariables = array_merge($replaceVariables, $dynamicReplaceVariables);

        $replaceVariables = array_merge($replaceVariables, $dynamicParamsVariables);

        foreach ($replaceVariables as $keyVariable => $variableValue) {
            if (is_array($variableValue) || is_object($variableValue)) {
                $replaceVariables[$keyVariable] = json_encode($variableValue);
            }
        }

        $replaceVariablesJSON = array(
            '{{msg}}' => json_encode($msg_text),
            '{{msg_shortened_256}}' => json_encode(substr($msg_text,0,254)),
            '{{msg_lowercase}}' => json_encode(mb_strtolower($msg_text)),
            '{{msg_clean}}' => json_encode(trim($msg_text_cleaned)),
            '{{msg_clean_lowercase}}' => json_encode(mb_strtolower(trim($msg_text_cleaned))),
            '{{msg_url}}' => json_encode(erLhcoreClassBBCodePlain::make_clickable($msg_text, array('sender' => 0, 'clean_event' => true))),
            '{{msg_url_lowercase}}' => json_encode(erLhcoreClassBBCodePlain::make_clickable(mb_strtolower($msg_text), array('sender' => 0, 'clean_event' => true))),
            '{{msg_html}}' => json_encode(erLhcoreClassBBCode::make_clickable($msg_text, array('sender' => 0))),
            '{{msg_html_lowercase}}' => json_encode(erLhcoreClassBBCode::make_clickable(mb_strtolower($msg_text), array('sender' => 0))),
            '{{msg_html_nobr}}' => json_encode(str_replace("<br />","",erLhcoreClassBBCode::make_clickable($msg_text, array('sender' => 0)))),
            '{{msg_html_lowercase_nobr}}' => json_encode(str_replace("<br />",'',erLhcoreClassBBCode::make_clickable(mb_strtolower($msg_text), array('sender' => 0)))),
            '{{chat_id}}' => json_encode($paramsCustomer['chat']->id),
            '{{lhc.nick}}' => json_encode($paramsCustomer['chat']->nick),
            '{{lhc.email}}' => json_encode($paramsCustomer['chat']->email),
            '{{lhc.department}}' => json_encode((string)$paramsCustomer['chat']->department),
            '{{lhc.dep_id}}' => json_encode((string)$paramsCustomer['chat']->dep_id),
            '{{ip}}' => json_encode(erLhcoreClassIPDetect::getIP()),
            '{{media}}' => json_encode($media),
            '{{file_body}}' => json_encode($file_body),
            '{{file_url}}' => json_encode($file_url),
            '{{file_mime}}' => json_encode($file_mime),
            '{{file_name}}' =>json_encode($file_name),
            '{{file_size}}' =>json_encode($file_size),
            '{{custom_args_1}}' => json_encode(isset($paramsCustomer['action']['content']['attr_options']['custom_args_1']) ? $paramsCustomer['action']['content']['attr_options']['custom_args_1'] : null),
            '{{date_utc}}' => json_encode((new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s T')),
            '{{timestamp}}' => time()
        );

        foreach ($dynamicReplaceVariables as $keyDynamic => $valueDynamic) {
            $replaceVariablesJSON[$keyDynamic] = json_encode($valueDynamic);
        }

        foreach ($dynamicParamsVariables as $keyDynamic => $valueDynamic) {
            $replaceVariablesJSON[$keyDynamic] = json_encode($valueDynamic);
        }

        // Keep body only if specific variable is not empty
        if (isset($methodSettings['body_raw'])) {
            $matchesExtension = [];
            preg_match_all('/\{(not|is)_empty__(.*?)\}(.*?)\{\/(not|is)_empty\}/ms', $methodSettings['body_raw'], $matchesExtension);
            if (!empty($matchesExtension[2])) {
                foreach ($matchesExtension[2] as $indexExtension => $varCheck) {
                    $varsCheck = explode('||', $varCheck);
                    $allFilled = true;
                    foreach ($varsCheck as $varCheckReplace) {
                        if (
                            ($matchesExtension[1][$indexExtension] == 'not' && empty($replaceVariables['{{'.$varCheckReplace.'}}']))
                            ||
                            ($matchesExtension[1][$indexExtension] == 'is' && !empty($replaceVariables['{{'.$varCheckReplace.'}}']))
                        ) {
                            $allFilled = false;
                        }
                    }
                    if ($allFilled) {
                        $methodSettings['body_raw'] = str_replace($matchesExtension[0][$indexExtension],$matchesExtension[3][$indexExtension], $methodSettings['body_raw']);
                    } else {
                        $methodSettings['body_raw'] = str_replace($matchesExtension[0][$indexExtension],'', $methodSettings['body_raw']);
                    }
                }
            }
        }

        if (isset($methodSettings['conditions']) && is_array($methodSettings['conditions']) && !empty($methodSettings['conditions'])) {
            foreach ($methodSettings['conditions'] as $condition){

                if (isset($replaceVariables[$condition['key']]) && $replaceVariables[$condition['key']] !== null) {
                    $condition['key'] = $replaceVariables[$condition['key']];
                    $validCondition = true;
                } else {
                    $validCondition = false;
                }

                if ($validCondition === true && isset($condition['success_condition']) && $condition['success_condition'] != '' && isset($condition['value'])) {
                    if ( $condition['success_condition'] == 'eq' && !($condition['key'] == $condition['value'])) {
                        $validCondition = false;
                    } else if ($condition['success_condition'] == 'lt' && !($condition['key'] < $condition['value'])) {
                        $validCondition = false;
                    } else if ($condition['success_condition'] == 'lte' && !($condition['key'] <= $condition['value'])) {
                        $validCondition = false;
                    } else if ($condition['success_condition'] == 'neq' && !($condition['key'] != $condition['value'])) {
                        $validCondition = false;
                    } else if ($condition['success_condition'] == 'gte' && !($condition['key'] >= $condition['value'])) {
                        $validCondition = false;
                    } else if ($condition['success_condition'] == 'gt' && !($condition['key'] > $condition['value'])) {
                        $validCondition = false;
                    } else if ($condition['success_condition'] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$condition['value']),$condition['key'],0) == false) {
                        $validCondition = false;
                    } else if ($condition['success_condition'] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$condition['value']),$condition['key'],0) == true) {
                        $validCondition = false;
                    } else if ($condition['success_condition'] == 'contains' && strrpos($condition['value'],$condition['key']) === false) {
                        $validCondition = false;
                    }
                }

                if ($validCondition == false) {
                     return array(
                        'content' => 'Invalid conditions',
                        'http_code' => 404,
                        'http_data' => '',
                        'content_2' => '',
                        'content_3' =>  '',
                        'content_4' => '',
                        'content_5' =>  '',
                        'content_6' => '',
                        'http_error' => '',
                        'content_raw' => '',
                        'params_request' => '',
                        'meta' => '',
                        'id' => 0
                    );
                }
            }
        }

        if (isset($paramsCustomer['chat']) && $paramsCustomer['chat'] instanceof erLhcoreClassModelChat) {
            foreach ($paramsCustomer['chat']->additional_data_array as $keyItem => $addItem) {
                if (!is_string($addItem) || (is_string($addItem) && ($addItem != ''))) {
                    if (isset($addItem['identifier'])) {
                        if (is_string($addItem['value']) || is_numeric($addItem['value'])) {
                            $replaceVariables['{{lhc.add.' . $addItem['identifier'] . '}}'] = $addItem['value'];
                            $replaceVariablesJSON['{{lhc.add.' . $addItem['identifier'] . '}}'] = json_encode($addItem['value']);
                        }
                    } else if (isset($addItem['key'])) {
                        if (is_string($addItem['value']) || is_numeric($addItem['value'])) {
                            $replaceVariables['{{lhc.add.' . $addItem['key'] . '}}'] = $addItem['value'];
                            $replaceVariablesJSON['{{lhc.add.' . $addItem['key'] . '}}'] = json_encode($addItem['value']);
                        }
                    }
                }
            }

            foreach ($paramsCustomer['chat']->chat_variables_array as $keyItem => $addItem) {
                if (is_string($addItem) || is_numeric($addItem)) {
                    $replaceVariables['{{lhc.var.' . $keyItem . '}}'] = $addItem;
                    $replaceVariablesJSON['{{lhc.var.' . $keyItem . '}}'] = json_encode($addItem);
                }
            }
        }

        $queryArgs = array();

        if (isset($methodSettings['query']) && !empty($methodSettings['query'])) {
            foreach ($methodSettings['query'] as $dataQuery) {
                $queryArgs[$dataQuery['key']] = str_replace(array_keys($replaceVariables), array_values($replaceVariables), $dataQuery['value']);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, (isset($methodSettings['max_execution_time']) && is_numeric($methodSettings['max_execution_time']) && $methodSettings['max_execution_time'] >= 1 && $methodSettings['max_execution_time'] <= 60) ? (int)$methodSettings['max_execution_time'] : 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.29.0');

        if (isset($methodSettings['method']) && ($methodSettings['method'] == 'PUT' || $methodSettings['method'] == 'DELETE')) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methodSettings['method']);
        }

        $headers = array();

        if (isset($methodSettings['header']) && !empty($methodSettings['header'])) {
            foreach ($methodSettings['header'] as $header) {
                $headers[] = $header['key'] . ': ' . str_replace(array_keys($replaceVariables), array_values($replaceVariables), $header['value']);
            }
        }

        if (isset($methodSettings['method']) && $methodSettings['method'] == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        if (isset($methodSettings['authorization']) && $methodSettings['authorization'] == 'basicauth') {
            curl_setopt($ch, CURLOPT_USERPWD, str_replace(array_keys($replaceVariables), array_values($replaceVariables), $methodSettings['auth_username']) . ":" . str_replace(array_keys($replaceVariables), array_values($replaceVariables), $methodSettings['auth_password']));
        } elseif (isset($methodSettings['authorization']) && $methodSettings['authorization'] == 'NTLMauth') {
            curl_setopt($ch, CURLOPT_USERPWD, $methodSettings['auth_username'] . ":" . $methodSettings['auth_password']);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        } elseif (isset($methodSettings['authorization']) && $methodSettings['authorization'] == 'bearer' && isset($methodSettings['auth_bearer']) && $methodSettings['auth_bearer'] != '') {
            $headers[] = 'Authorization: Bearer ' . $methodSettings['auth_bearer'];
        } else if (isset($methodSettings['authorization']) && $methodSettings['authorization'] == 'apikey') {
            if ($methodSettings['api_key_location'] == 'header' && isset($methodSettings['auth_api_key_key']) && isset($methodSettings['auth_api_key_name'])) {
                $headers[] = $methodSettings['auth_api_key_name'] . ': ' . $methodSettings['auth_api_key_key'];
            } else if ($methodSettings['api_key_location'] == 'queryparams') {
                $queryArgs[$methodSettings['auth_api_key_name']] = $methodSettings['auth_api_key_key'];
            }
        }

        if (isset($methodSettings['userparams']) && !empty($methodSettings['userparams'])) {
            foreach ($methodSettings['userparams'] as $userParam) {

                $valueParam = '';

                if (isset($paramsCustomer['action']['content']['rest_api_method_params'][$userParam['id']]) && !empty($paramsCustomer['action']['content']['rest_api_method_params'][$userParam['id']])) {
                    $valueParam = $paramsCustomer['action']['content']['rest_api_method_params'][$userParam['id']];
                }

                if (!isset($userParam['location']) || $userParam['location'] == '') {
                    $queryArgs[$userParam['key']] = $valueParam;
                } elseif (isset($userParam['location']) && $userParam['location'] == 'post_param') {
                    $postParams[$userParam['key']] = $valueParam;
                } elseif (isset($userParam['location']) && $userParam['location'] == 'body_param') {
                    $methodSettings['body_raw'] = str_replace('{{' . $userParam['key'] . '}}', json_encode($valueParam), $file_api === true ? $methodSettings['body_raw_file'] : $methodSettings['body_raw']);
                }
            }
        }

        if (isset($methodSettings['body_request_type']) && ($methodSettings['body_request_type'] == 'form-data' || $methodSettings['body_request_type'] == 'form-data-urlencoded')) {

            $postParams = array();

            if (isset($methodSettings['postparams']) && !empty($methodSettings['postparams'])) {
                foreach ($methodSettings['postparams'] as $postParam) {
                    $postParams[$postParam['key']] = str_replace(array_keys($replaceVariables), array_values($replaceVariables), $postParam['value']);
                }

                if ($methodSettings['body_request_type'] == 'form-data-urlencoded') {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
                }
            }

            if ($file_api === true) {
                $rawReplaceArray = array();
                foreach ($replaceVariablesJSON as $keyVariable => $keyValue) {
                    $rawReplaceArray['raw_'.$keyVariable] = str_replace('\\\/','\/',self::trimOnce($keyValue));
                }

                $bodyPOST = str_replace(array_keys($rawReplaceArray), array_values($rawReplaceArray),  $methodSettings['body_raw_file']);
                $bodyPOST = str_replace(array_keys($replaceVariablesJSON), array_values($replaceVariablesJSON), $bodyPOST);
                $bodyPOST = preg_replace('/{{lhc\.(var|add)\.(.*?)}}/','""',$bodyPOST);

                $paramsPOST = json_decode($bodyPOST,true);

                foreach ($paramsPOST as $key => $postParam) {
                    if (isset($mediaFile) && $postParam === 'file_id_' . $mediaFile->id) {
                        $postParams[$key] = new CurlFile($mediaFile->file_path_server, $mediaFile->type, $mediaFile->upload_name);
                    } else {
                        $postParams[$key] = $postParam;
                    }
                }

                curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
            }

            if ($methodSettings['body_request_type'] == 'form-data-urlencoded') {
                $headers[] = 'Cache-Control: no-cache';
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            }

        } elseif (isset($methodSettings['body_request_type']) && $methodSettings['body_request_type'] == 'raw') {

            $bodyPOST = $file_api === true ? $methodSettings['body_raw_file'] : $methodSettings['body_raw'];

            $rawReplaceArray = array();
            foreach ($replaceVariablesJSON as $keyVariable => $keyValue) {

                if (str_contains($bodyPOST,'sensitive_'.$keyVariable)) {
                    $rawReplaceArray['sensitive_'.$keyVariable] = \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessage($keyValue);
                }

                if (str_contains($bodyPOST,'raw_sensitive_'.$keyVariable)) {
                    $rawReplaceArray['raw_sensitive_'.$keyVariable] = str_replace('\\\/','\/',self::trimOnce(\LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessage($keyValue)));
                }

                if (str_contains($bodyPOST,'raw_'.$keyVariable)) {
                    $rawReplaceArray['raw_'.$keyVariable] = str_replace('\\\/','\/',self::trimOnce($keyValue));
                }
            }

            $bodyPOST = str_replace(array_keys($rawReplaceArray), array_values($rawReplaceArray), $bodyPOST);
            $bodyPOST = str_replace(array_keys($replaceVariablesJSON), array_values($replaceVariablesJSON), $bodyPOST);
            $bodyPOST = preg_replace('/{{lhc\.(var|add)\.(.*?)}}/','""',$bodyPOST);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyPOST);

            if (isset($methodSettings['body_request_type_content'])) {
                if ($methodSettings['body_request_type_content'] == 'json') {
                    $headers[] = 'Content-Type: application/json';
                } else if ($methodSettings['body_request_type_content'] == 'text') {
                    $headers[] = 'Content-Type: text/plain';
                } else if ($methodSettings['body_request_type_content'] == 'js') {
                    $headers[] = 'Content-Type: application/javascript';
                } else if ($methodSettings['body_request_type_content'] == 'appxml') {
                    $headers[] = 'Content-Type: application/xml';
                } else if ($methodSettings['body_request_type_content'] == 'textxml') {
                    $headers[] = 'Content-Type: text/xml';
                }else if ($methodSettings['body_request_type_content'] == 'texthtml') {
                    $headers[] = 'Content-Type: text/html';
                }
            }
        }

        $queryArgsString = http_build_query($queryArgs);
        $replaceVariablesURL = [];

        foreach ($replaceVariables as $keyVariable => $variableValue) {
                $replaceVariablesURL['urlencode_' . $keyVariable] = urlencode($variableValue);
        }

        $url = rtrim($host) . str_replace(array_keys($replaceVariables), array_values($replaceVariables),str_replace(array_keys($replaceVariablesURL), array_values($replaceVariablesURL), (isset($methodSettings['suburl']) ? $methodSettings['suburl'] : ''))) . (!empty($queryArgsString) ? '?'.$queryArgsString : '');

        if (!filter_var($url, FILTER_VALIDATE_URL)) {

            if (isset($paramsCustomer['rest_api']->configuration_array['log_audit']) && $paramsCustomer['rest_api']->configuration_array['log_audit']) {
                erLhcoreClassLog::write(
                    json_encode([
                        'name' => '[' . $paramsCustomer['rest_api']->name . '] ' . (isset($methodSettings['name']) ? $methodSettings['name'] : 'unknwon_name'),
                        'http_code' => (isset($httpcode) ? $httpcode : 'unknown'),
                        'method' => (isset($methodSettings['method']) ? $methodSettings['method'] : 'unknwon'),
                        'request_type' => (isset($methodSettings['body_request_type']) ? $methodSettings['body_request_type'] : ''),
                        'params_request' => '',
                        'return_content' => 'Invalid URL filter_var validation failed. '.$url,
                        'http_error' => '',
                        'stream' => '',
                        'stream_lines' => '',
                        'msg_id' => (isset($paramsCustomer['params']['msg']) && is_object($paramsCustomer['params']['msg'])) ?  $paramsCustomer['params']['msg']->id : 0,
                        'msg_text' => $msg_text,
                    ], JSON_PRETTY_PRINT),
                    ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'Bot',
                        'category' => 'rest_api',
                        'line' => __LINE__,
                        'file' => __FILE__,
                        'object_id' => (isset($paramsCustomer['chat']) && is_object($paramsCustomer['chat']) ? $paramsCustomer['chat']->id : 0)
                    )
                );
            }

            if (isset($paramsCustomer['rest_api']->configuration_array['log_system']) && $paramsCustomer['rest_api']->configuration_array['log_system'] && isset($paramsCustomer['chat']) && is_object($paramsCustomer['chat'])) {
                $msgLog = new erLhcoreClassModelmsg();
                $msgLog->user_id = -1;
                $msgLog->chat_id = $paramsCustomer['chat']->id;
                $msgLog->time = time();
                $msgLog->meta_msg = json_encode(['content' => ['html' => [
                    'debug' => true,
                    'content' => json_encode([
                        'name' => '[' . $paramsCustomer['rest_api']->name . '] ' . (isset($methodSettings['name']) ? $methodSettings['name'] : 'unknown_name'),
                        'http_code' => (isset($httpcode) ? $httpcode : 'unknown'),
                        'method' => (isset($methodSettings['method']) ? $methodSettings['method'] : 'unknown'),
                        'request_type' => (isset($methodSettings['body_request_type']) ? $methodSettings['body_request_type'] : ''),
                        'params_request' => '',
                        'return_content' => 'Invalid URL filter_var validation failed. '.$url,
                        'http_error' => '',
                        'stream' => '',
                        'stream_lines' => '',
                        'msg_id' => (isset($paramsCustomer['params']['msg']) && is_object($paramsCustomer['params']['msg'])) ? $paramsCustomer['params']['msg']->id : 0,
                        'msg_text' => $msg_text,
                    ],JSON_PRETTY_PRINT)]]]);
                $msgLog->msg = '[' . $paramsCustomer['rest_api']->name . '] ' . '[i]'.(isset($methodSettings['name']) ? $methodSettings['name'] : 'unknown_name').'[/i]';
                $msgLog->saveThis();
            }

            return array(
                'content' => 'Invalid URL filter_var validation failed. '.$url,
                'content_raw' => 'Invalid URL filter_var validation failed. '.$url,
                'params_request' => '',
                'http_code' => '500',
                'http_error' => '500',
                'http_data' => '500',
                'content_2' => '',
                'content_3' => '',
                'content_4' => '',
                'content_5' => '',
                'content_6' => '',
                'meta' => array()
            );
        }

        $urlParts = parse_url($url);

        if (!in_array($urlParts['scheme'],['http','https']) || (class_exists('erLhcoreClassInstance') && isset($urlParts['port']) && !in_array($urlParts['port'],[80,443]))) {
            return array(
                'content' => 'Only HTTP/HTTPS protocols are supported. In automated hosting environment 80 and 443 ports only. '.$url,
                'content_raw' => 'Only HTTP/HTTPS protocols are supported. In automated hosting environment 80 and 443 ports only. '.$url,
                'params_request' => '',
                'http_code' => '500',
                'http_error' => '500',
                'http_data' => '500',
                'content_2' => '',
                'content_3' => '',
                'content_4' => '',
                'content_5' => '',
                'content_6' => '',
                'meta' => array()
            );
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $responseContent = [];
        $streamLines = [];
        $logRequest = is_object($paramsCustomer['rest_api']) &&
            (isset($paramsCustomer['rest_api']->configuration_array['log_audit']) && $paramsCustomer['rest_api']->configuration_array['log_audit']) ||
            (isset($paramsCustomer['rest_api']->configuration_array['log_system']) && $paramsCustomer['rest_api']->configuration_array['log_system']);
        $streamEvent = '';
        $streamBuffer = ''; // In streaming chunk might contain multiple json parts untill it's complete
        $streamContentBuffer = '';

        // Streaming request save stream to tmp file
        if (isset($methodSettings['streaming_request']) && $methodSettings['streaming_request'] == 1) {

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) use (& $streamBuffer, &$responseContent, $paramsCustomer, $methodSettings, & $streamLines, $logRequest, & $streamEvent, & $streamContentBuffer)  {
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $trimmed_data = trim($data);

                if (!empty($trimmed_data)) {
                    $streamContent = ['content' => '','content_2' => '','content_3' => '','content_4' => '','content_5' => '','content_6' => ''];
                    foreach (explode("\n",$trimmed_data) as $line) {
                        $line = trim($line);

                        if (isset($methodSettings['streaming_event_type_field']) && !empty($methodSettings['streaming_event_type_field'])) {
                            if ($line != "" && str_starts_with(trim($line),$methodSettings['streaming_event_type_field'].': ')) { // This is event indication
                                $streamEvent = str_replace($methodSettings['streaming_event_type_field'].': ','',$line);
                                $streamBuffer = '';
                            }
                        }

                        $dataStarted = str_starts_with(trim($line),'data: {');

                        if ($line != "" && ($dataStarted === true || !empty($streamBuffer))) {

                            if ($dataStarted === true) {
                                $streamBuffer = str_replace('data: {','{',$line);
                            } else {
                                $streamBuffer .= $line;
                            }

                            json_decode($streamBuffer);
                            if (json_last_error() != JSON_ERROR_NONE) {
                                if ($logRequest === true && $line != "") {
                                    $streamLines[] = self::getCurrentTimeWithMilliseconds(). ' [INVALID JSON] - [' . $streamEvent . '] - ' . $streamBuffer;
                                }
                                continue;
                            } elseif ($logRequest === true && $line != "") {
                                $streamLines[] = self::getCurrentTimeWithMilliseconds().' [VALID JSON] - [' . $streamEvent . '] - ' . $streamBuffer;
                            }

                            $responseStream = self::parseContentOutput([
                                'content' => $streamBuffer,
                                'paramsCustomer' => $paramsCustomer,
                                'methodSettings' => $methodSettings,
                                'httpcode' => $httpCode,
                                'url' => null,
                                'http_error' => null,
                                'http_data' => null,
                                'paramsRequest' => null,
                                'stream_event' => $streamEvent
                            ]);

                            if (isset($responseStream['conditions_met']) && $responseStream['conditions_met'] == 1) {
                                if (isset($responseStream['stream_content']) && $responseStream['stream_content'] == true) {
                                    $streamContent['content'] .= $responseStream['content'];
                                    $streamContent['content_2'] .= $responseStream['content_2'];
                                    $streamContent['content_3'] .= $responseStream['content_3'];
                                    $streamContent['content_4'] .= $responseStream['content_4'];
                                    $streamContent['content_5'] .= $responseStream['content_5'];
                                    $streamContent['content_6'] .= $responseStream['content_6'];
                                }
                                if (
                                    (isset($responseStream['stream_content']) && $responseStream['stream_content'] == true && empty($responseContent)) ||
                                    (isset($responseStream['stream_final']) && $responseStream['stream_final'] == true)
                                ) {
                                    $responseContent = $responseStream;
                                } else if (isset($responseStream['stream_content']) && $responseStream['stream_content'] == true) {
                                    $responseContent['content'] .= $responseStream['content'];
                                    $responseContent['content_2'] .= $responseStream['content_2'];
                                    $responseContent['content_3'] .= $responseStream['content_3'];
                                    $responseContent['content_4'] .= $responseStream['content_4'];
                                    $responseContent['content_5'] .= $responseStream['content_5'];
                                    $responseContent['content_6'] .= $responseStream['content_6'];
                                }
                            }
                        }
                    }

                    if (isset($responseStream['content']) && $responseStream['content'] != '' && isset($responseStream['conditions_met']) && $responseStream['conditions_met'] == 1) {
                        if (isset($streamContent['content']) && isset($responseStream['stream_content']) && $responseStream['stream_content'] == true){

                            // Are we streaming as HTML
                            if (isset($responseStream['stream_as_html']) && $responseStream['stream_as_html'] == true) {

                                $streamContentBuffer .= $streamContent['content'];

                                // Send chunk only if it's content is a valid markdown
                                if (self::isMarkdownRowComplete($streamContentBuffer)) {

                                    $streamContentBuffer = str_replace(array("\r\n", "\r"), "\n", $streamContentBuffer);

                                    // Send aggregated chunk to chat
                                    $paramsMessageRender = array('sender' => -2);
                                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.stream_flow', array(
                                        'restapi' => $paramsCustomer['rest_api'],
                                        'chat' => $paramsCustomer['chat'],
                                        'as_html' => true,
                                        'response' => [
                                            'content' => str_replace('[[EMPT]]','',erLhcoreClassBBCode::make_clickable(htmlspecialchars('[[EMPT]]'.$streamContentBuffer.'[[EMPT]]'), $paramsMessageRender)),
                                            'raw_content' => $streamContent]
                                    ));
                                    $streamContentBuffer = '';
                                }

                            } else { // Stream as plain text
                                // We need to replace space with non breaking space to preserve innerText attribute
                                if (str_ends_with($streamContent['content'],' ')) {
                                    $streamContent['content'] = rtrim($streamContent['content']) . "\u{00A0}";
                                }
                                // Send aggregated chunk to chat
                                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.stream_flow', array(
                                    'restapi' => $paramsCustomer['rest_api'],
                                    'chat' => $paramsCustomer['chat'],
                                    'as_html' => false,
                                    'response' => $streamContent
                                ));
                            }
                        }

                        if (isset($responseStream['stream_execute_trigger']) && $responseStream['stream_execute_trigger'] == true) {

                            if (isset($paramsCustomer['action']['content']['rest_api_method_output'][$responseStream['id']]) && is_numeric($paramsCustomer['action']['content']['rest_api_method_output'][$responseStream['id']])) {
                                $argsDefault = array(
                                      'replace_array' => array(
                                        '{content_1}' => $responseStream['content'],
                                        '{content_2}' => $responseStream['content_2'],
                                        '{content_3}' => $responseStream['content_3'],
                                        '{content_4}' => $responseStream['content_4'],
                                        '{content_5}' => $responseStream['content_5'],
                                        '{content_6}' => $responseStream['content_6'],
                                        '{content_1_json}' => json_encode($responseStream['content']),
                                        '{content_2_json}' => json_encode($responseStream['content_2']),
                                        '{content_3_json}' => json_encode($responseStream['content_3']),
                                        '{content_4_json}' => json_encode($responseStream['content_4']),
                                        '{content_5_json}' => json_encode($responseStream['content_5']),
                                        '{content_6_json}' => json_encode($responseStream['content_6']),
                                        '{http_code}' => $responseStream['http_code'],
                                        '{http_error}' => $responseStream['http_error'],
                                        '{content_raw}' => $responseStream['content_raw'],
                                        '{http_data}' => $responseStream['http_data']
                                    )
                                );

                                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($paramsCustomer['action']['content']['rest_api_method_output'][$responseStream['id']]);

                                erLhcoreClassGenericBotWorkflow::processTrigger($paramsCustomer['chat'], $trigger, true, array('args' => $argsDefault));

                                if ($logRequest === true) {
                                    $streamLines[] = self::getCurrentTimeWithMilliseconds().' [trigger exec] - [' . $trigger->name . '] [' . $trigger->id . ']';
                                }

                            }
                        }
                    }
                }

                return strlen($data);
            });
        }

        $paramsRequest = [
            'headers' => $headers,
            'url' => $url,
        ];

        if (isset($postParams)) {
            $paramsRequest['post'] = $postParams;
        }

        if (isset($bodyPOST)) {
            $paramsRequest['body'] = $bodyPOST;
        }

        $commandResponse = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.rest_api_make_request', array(
            'method_settings' => $methodSettings,
            'params_customer' => $paramsCustomer,
            'params_request' => $paramsRequest,
            'url' => $url,
            'ch'  => & $ch
        ));

        $overridden = false;

        $http_data = json_encode($paramsRequest);
        $http_error = '';
        $requestStarted = self::getCurrentTimeWithMilliseconds();
        
        if (isset($commandResponse['processed']) && $commandResponse['processed'] == true) {
            $content = $commandResponse['http_response'];
            $http_error = $commandResponse['http_error'];
            $httpcode = $commandResponse['http_code'];
            $overridden = true;
        } else {
            if (is_object($paramsCustomer['rest_api']) &&
                isset($paramsCustomer['rest_api']->configuration_array['ecache']) &&
                $paramsCustomer['rest_api']->configuration_array['ecache'] &&
                ($responseCache = erLhcoreClassModelGenericBotRestAPICache::findOne(['sort' => false, 'filter' => ['hash' => md5($http_data . $url), 'rest_api_id' => $paramsCustomer['rest_api']->id]])) &&
                $responseCache instanceof erLhcoreClassModelGenericBotRestAPICache) {
                    $content = $responseCache->response;
                    $httpcode = 200;
                    $overridden = true;
            } else {
                $content = curl_exec($ch);
            }
        }

        $requestFinished = self::getCurrentTimeWithMilliseconds();

        if ($overridden == false && curl_errno($ch)) {
            $http_error = curl_error($ch);
        }

        if ($overridden == false) {
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpcode == 200 &&
                is_object($paramsCustomer['rest_api']) &&
                isset($paramsCustomer['rest_api']->configuration_array['ecache']) &&
                $paramsCustomer['rest_api']->configuration_array['ecache']) {
                    $cacheRequest = new erLhcoreClassModelGenericBotRestAPICache();
                    $cacheRequest->hash = md5($http_data . $url);
                    $cacheRequest->response = $content;
                    $cacheRequest->rest_api_id = $paramsCustomer['rest_api']->id;

                    try {
                        $cacheRequest->saveThisOnly();
                    } catch (Exception $e) {
                        // Sometimes object already exists
                    }
            }
            curl_close($ch);
        }

        if ($logRequest === true) {

            $contentDebug = json_decode($content,true);

            $paramsRequestDebug = $paramsRequest;

            if (isset($paramsRequestDebug['body'])) {
                $contentDebugBody = json_decode($paramsRequestDebug['body'],true);
                if (is_array($contentDebugBody)) {
                    $paramsRequestDebug['body'] = $contentDebugBody;
                }
            }

            $code = explode(',',str_replace(' ','',isset($paramsCustomer['rest_api']->configuration_array['log_code']) ? $paramsCustomer['rest_api']->configuration_array['log_code'] : ''));

            $validCode = true;

            // Not empty
            // Code is not marked as ignore
            if (!empty($code) && in_array($httpcode,$code)) {
                $validCode = false;
            }

            if ($validCode === true && isset($paramsCustomer['rest_api']->configuration_array['log_audit']) && $paramsCustomer['rest_api']->configuration_array['log_audit']) {
                erLhcoreClassLog::write(
                    json_encode([
                        'name' => '[' . $paramsCustomer['rest_api']->name . '] ' . (isset($methodSettings['name']) ? $methodSettings['name'] : 'unknwon_name'),
                        'http_code' => (isset($httpcode) ? $httpcode : 'unknown'),
                        'method' => (isset($methodSettings['method']) ? $methodSettings['method'] : 'unknwon'),
                        'request_type' => (isset($methodSettings['body_request_type']) ? $methodSettings['body_request_type'] : ''),
                        'params_request' => $paramsRequestDebug,
                        'return_content' => is_array($contentDebug) ? $contentDebug : $content,
                        'http_error' => $http_error,
                        'started_at' => $requestStarted,
                        'finished_at' => $requestFinished,
                        'stream' => $responseContent,
                        'stream_lines' => $streamLines,
                        'msg_id' => (isset($paramsCustomer['params']['msg']) && is_object($paramsCustomer['params']['msg'])) ?  $paramsCustomer['params']['msg']->id : 0,
                        'msg_text' => $msg_text,
                    ], JSON_PRETTY_PRINT),
                    ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'Bot',
                        'category' => 'rest_api',
                        'line' => __LINE__,
                        'file' => __FILE__,
                        'object_id' => (isset($paramsCustomer['chat']) && is_object($paramsCustomer['chat']) ? $paramsCustomer['chat']->id : 0)
                    )
                );
            }

            if ($validCode === true && isset($paramsCustomer['rest_api']->configuration_array['log_system']) && $paramsCustomer['rest_api']->configuration_array['log_system'] && isset($paramsCustomer['chat']) && is_object($paramsCustomer['chat'])) {
                $msgLog = new erLhcoreClassModelmsg();
                $msgLog->user_id = -1;
                $msgLog->chat_id = $paramsCustomer['chat']->id;
                $msgLog->time = time();
                $msgLog->meta_msg = json_encode(['content' => ['html' => [
                    'debug' => true,
                    'content' => json_encode([
                    'name' => '[' . $paramsCustomer['rest_api']->name . '] ' . (isset($methodSettings['name']) ? $methodSettings['name'] : 'unknown_name'),
                    'http_code' => (isset($httpcode) ? $httpcode : 'unknown'),
                    'method' => (isset($methodSettings['method']) ? $methodSettings['method'] : 'unknown'),
                    'request_type' => (isset($methodSettings['body_request_type']) ? $methodSettings['body_request_type'] : ''),
                    'params_request' => $paramsRequestDebug,
                    'return_content' => is_array($contentDebug) ? $contentDebug : $content,
                    'http_error' => $http_error,
                    'started_at' => $requestStarted,
                    'finished_at' => $requestFinished,
                    'stream' => $responseContent,
                    'stream_lines' => $streamLines,
                    'msg_id' => (isset($paramsCustomer['params']['msg']) && is_object($paramsCustomer['params']['msg'])) ? $paramsCustomer['params']['msg']->id : 0,
                    'msg_text' => $msg_text,
                ],JSON_PRETTY_PRINT)]]]);
                $msgLog->msg = '[' . $paramsCustomer['rest_api']->name . '] ' . '[i]'.(isset($methodSettings['name']) ? $methodSettings['name'] : 'unknown_name').'[/i]';
                $msgLog->saveThis();
            }
        }

        if (!empty($responseContent)) {
            return $responseContent;
        }

        // Check vars is scope correct
        return self::parseContentOutput([
            'paramsCustomer' => $paramsCustomer,
            'methodSettings' => $methodSettings,
            'paramsRequest' => $paramsRequest,
            'replaceVariables' => $replaceVariables,
            'httpcode' => $httpcode,
            'url' => $url,
            'content' => $content,
            'http_error' => $http_error,
            'http_data' => $http_data
        ]);
    }
    public static function getCurrentTimeWithMilliseconds() {
        // Get the current time with microseconds as a float
        $microTime = microtime(true);

        // Format the time as H:i:s and add milliseconds
        $milliseconds = sprintf("%03d", ($microTime - floor($microTime)) * 1000);
        $timeWithMilliseconds = date("H:i:s", $microTime) . ".$milliseconds";

        // Print the time with milliseconds
        return $timeWithMilliseconds;
    }
    public static function parseContentOutput($processOutputParams) {

        extract($processOutputParams);

        if (isset($methodSettings['output']) && !empty($methodSettings['output'])) {

            $allOptional = true;

            // First check is there any required to check combinations and disable others if so.
            foreach ($methodSettings['output'] as $index => $outputCombination)
            {
                if (isset($paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk']) && $paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk'] == true) {
                    $allOptional = false;
                    break;
                }
            }

            // Sort by priority, first we will check the ones with higher priority
            usort($methodSettings['output'], function ($a, $b) {
                return !(isset($a['output_priority']) && is_numeric($a['output_priority']) && (!isset($b['output_priority']) || $a['output_priority'] > $b['output_priority'])) ? 1 : 0;
            });

            foreach ($methodSettings['output'] as $outputCombination)
            {
                if ($allOptional == false && (!isset($paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk']) || $paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk'] == false)) {
                    // One of the conditions is checked, but not this one.
                    continue;
                }

                // Streaming event does not match required one
                if (isset($outputCombination['streaming_event_type_value']) && $outputCombination['streaming_event_type_value'] != '' && (!isset($processOutputParams['stream_event']) || $outputCombination['streaming_event_type_value'] != $processOutputParams['stream_event'])) {
                    continue;
                }

                // Verify HTTP Status code
                if (!isset($outputCombination['success_header']) || $outputCombination['success_header'] == '' || in_array((string)$httpcode,explode(',',$outputCombination['success_header']))){

                    if (isset($outputCombination['success_location']) && $outputCombination['success_location'] != '') {

                        if (isset($outputCombination['format']) && $outputCombination['format'] == 'xml') {
                            $contentJSON = json_decode(json_encode(simplexml_load_string($content)),true);
                        } else {
                            $contentJSON = json_decode($content, true);
                        }

                        if ($outputCombination['success_location'] == '__all__') {
                            $successLocation = ['value' => $contentJSON, 'found' => true];
                        } else if (strpos($outputCombination['success_location'],'__max__') === 0) {
                            $successLocation = self::extractAttribute($contentJSON, str_replace('__max__','',$outputCombination['success_location']));
                            if ($successLocation['found'] === true && is_array($successLocation['value'])) {
                                $successLocation['value'] = max($successLocation['value']);
                            }
                        } else {
                            $successLocation = self::extractAttribute($contentJSON, $outputCombination['success_location']);
                        }

                        if ($successLocation['found'] === true) {
                            $responseValueSub = array();
                            for ($i = 2; $i <= 6; $i++) {
                                if (isset($outputCombination['success_location_' . $i]) && $outputCombination['success_location_' . $i] != '') {
                                    $aggregation = '';
                                    if ($outputCombination['success_location_' . $i] == '__all__') {
                                        $successLocationNumbered = ['value' => $contentJSON, 'found' => true];
                                    } else if (strpos($outputCombination['success_location_' . $i],'__max__') === 0) {
                                        $successLocationNumbered = self::extractAttribute($contentJSON, str_replace('__max__','',$outputCombination['success_location_' . $i]));
                                        $aggregation = 'max';
                                    } else {
                                        $successLocationNumbered = self::extractAttribute($contentJSON, $outputCombination['success_location_' . $i]);
                                    }
                                    if ($successLocationNumbered['found'] === true) {
                                        if ($aggregation === 'max' && is_array($successLocationNumbered['value'])) {
                                            $successLocationNumbered['value'] = max($successLocationNumbered['value']);
                                        }
                                        $responseValueSub[$i] = $successLocationNumbered['value'];
                                    }
                                }
                            }

                            $responseValueCompare2 = $responseValueCompare = $responseValue = $successLocation['value'];

                            // First condition
                            if (isset($outputCombination['success_condition_val']) && !empty($outputCombination['success_condition_val'])) {
                                $responseValueCompareLocation = self::extractAttribute($contentJSON, $outputCombination['success_condition_val']);
                                if ($responseValueCompareLocation['found'] === true && !is_array($responseValueCompareLocation['value'])) {
                                    $responseValueCompare = $responseValueCompareLocation['value'];
                                } else {
                                    // Attribute was not found
                                    continue;
                                }
                            }

                            // Second condition
                            if (isset($outputCombination['success_condition_val_2']) && !empty($outputCombination['success_condition_val_2'])) {
                                $responseValueCompareLocation = self::extractAttribute($contentJSON, $outputCombination['success_condition_val_2']);

                                if ($responseValueCompareLocation['found'] === true && !is_array($responseValueCompareLocation['value'])) {
                                    $responseValueCompare2 = $responseValueCompareLocation['value'];
                                } else {
                                    // Attribute was not found
                                    continue;
                                }
                            }


                        } else {
                            continue; // Required attribute was not found
                        }
                    } else {
                        $responseValueCompare2 = $responseValueCompare = $responseValue = $content;
                    }

                    foreach([   [
                        'success_compare_value' => 'success_compare_value',
                        'success_condition' => 'success_condition',
                        'live_value' => $responseValueCompare,
                    ],
                                [
                                    'success_compare_value' => 'success_compare_value_2',
                                    'success_condition' => 'success_condition_2',
                                    'live_value' => $responseValueCompare2,
                                ]
                            ] as $attrCompare) {
                        if (isset($outputCombination[$attrCompare['success_condition']]) && $outputCombination[$attrCompare['success_condition']] != '' && isset($outputCombination[$attrCompare['success_compare_value']]) && $outputCombination[$attrCompare['success_compare_value']] != '') {
                            if ( $outputCombination[$attrCompare['success_condition']] == 'eq' && !($attrCompare['live_value'] == $outputCombination[$attrCompare['success_compare_value']])) {
                                continue 2;
                            } else if ($outputCombination[$attrCompare['success_condition']] == 'lt' && !($attrCompare['live_value'] < $outputCombination[$attrCompare['success_compare_value']])) {
                                continue 2;
                            } else if ($outputCombination[$attrCompare['success_condition']] == 'lte' && !($attrCompare['live_value'] <= $outputCombination[$attrCompare['success_compare_value']])) {
                                continue 2;
                            } else if ($outputCombination[$attrCompare['success_condition']] == 'neq' && !($attrCompare['live_value'] != $outputCombination[$attrCompare['success_compare_value']])) {
                                continue 2;
                            } else if ($outputCombination[$attrCompare['success_condition']] == 'gte' && !($attrCompare['live_value'] >= $outputCombination[$attrCompare['success_compare_value']])) {
                                continue 2;
                            } else if ($outputCombination[$attrCompare['success_condition']] == 'gt' && !($attrCompare['live_value'] > $outputCombination[$attrCompare['success_compare_value']])) {
                                continue 2;
                            } else if ($outputCombination[$attrCompare['success_condition']] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$outputCombination[$attrCompare['success_compare_value']]),$attrCompare['live_value'],0) == false) {
                                continue 2;
                            } else if ($outputCombination[$attrCompare['success_condition']] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$outputCombination[$attrCompare['success_compare_value']]),$attrCompare['live_value'],0) == true) {
                                continue 2;
                            } else if ($outputCombination[$attrCompare['success_condition']] == 'contains' && strrpos($attrCompare['live_value'], $outputCombination[$attrCompare['success_compare_value']]) === false) {
                                continue 2;
                            }
                        }
                    }

                    $meta = array();

                    if (isset($outputCombination['success_location_meta']) && $outputCombination['success_location_meta'] != '') {
                        $contentJSON = json_decode($content,true);
                        $metaResponse = self::extractAttribute($contentJSON, $outputCombination['success_location_meta']);

                        if ($metaResponse['found'] == true) {
                            $meta = $metaResponse['value'];
                        }
                    }

                    $responseFormatted = array(
                        'content' => $responseValue,
                        'content_raw' => $content,
                        'http_code' => $httpcode,
                        'http_error' => $http_error,
                        'http_data' => $http_data,
                        'content_2' => (isset($responseValueSub[2]) ? $responseValueSub[2] : ''),
                        'content_3' => (isset($responseValueSub[3]) ? $responseValueSub[3] : ''),
                        'content_4' => (isset($responseValueSub[4]) ? $responseValueSub[4] : ''),
                        'content_5' => (isset($responseValueSub[5]) ? $responseValueSub[5] : ''),
                        'content_6' => (isset($responseValueSub[6]) ? $responseValueSub[6] : ''),
                        'meta' => $meta,
                        'conditions_met' => true,
                        'params_request' => $paramsRequest,
                        'id' => $outputCombination['id'],
                        'stream_content' => (isset($outputCombination['stream_content']) && $outputCombination['stream_content'] == true),
                        'stream_as_html' => (isset($outputCombination['stream_as_html']) && $outputCombination['stream_as_html'] == true),
                        'stream_execute_trigger' => (isset($outputCombination['stream_execute_trigger']) && $outputCombination['stream_execute_trigger'] == true),
                        'stream_final' => (isset($outputCombination['stream_final']) && $outputCombination['stream_final'] == true)
                    );

                    if (isset($outputCombination['success_preg_replace']) && $outputCombination['success_preg_replace'] != '') {
                        $replaceRules = explode("\n", $outputCombination['success_preg_replace']);
                        foreach ($replaceRules as $replaceRule) {
                            $replaceRuleOptions = explode('==>',$replaceRule);
                            for ($i = 1; $i <= 6; $i++) {
                                $responseFormatted['content' . ($i > 1 ? '_' . $i : '')] = preg_replace('/'.$replaceRuleOptions[0].'/is',(isset($replaceRuleOptions[1]) ? $replaceRuleOptions[1] : ''), $responseFormatted['content' . ($i > 1 ? '_' . $i : '')]);
                            }
                        }
                    }

                    if (isset($outputCombination['method_name']) && !empty(trim($outputCombination['method_name']))) {
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_rest_api_method.' . trim($outputCombination['method_name']),
                            array(  'method_settings' => $methodSettings,
                                'params_customer' => $paramsCustomer,
                                'params_request' => $paramsRequest,
                                'url' => $url,
                                'custom_args' => str_replace(array_keys($replaceVariables), array_values($replaceVariables), (isset($outputCombination['method_name_args']) ? $outputCombination['method_name_args'] : '')),
                                'response' => $responseFormatted)
                        );
                    }

                    return $responseFormatted;
                }
            }

            // We did not found matching response. Return everything.
            return array(
                'content' => $content,
                'content_raw' => $content,
                'http_code' => $httpcode,
                'http_error' => $http_error,
                'http_data' => $http_data,
                'params_request' => $paramsRequest,
                'conditions_met' => false,
                'content_2' => '',
                'content_3' => '',
                'content_4' => '',
                'content_5' => '',
                'content_6' => '',
                'meta' => array()
            );
        }

        return array(
            'content' => $content,
            'content_raw' => $content,
            'http_code' => $httpcode,
            'http_error' => $http_error,
            'http_data' => $http_data,
            'params_request' => $paramsRequest,
            'conditions_met' => false,
            'content_2' => '',
            'content_3' => '',
            'content_4' => '',
            'content_5' => '',
            'content_6' => '',
            'meta' => array()
        );
    }

    public static function processReplyTo($bodyRequest, & $msg_text)
    {
        if (strpos($bodyRequest,'{reply_to}') !== false) {
            $matchesReplyTo = [];
            preg_match_all('#\[quote="?([0-9]+)"?\](.*?)\[/quote\]#is', $msg_text, $matchesReplyTo);
            if (isset($matchesReplyTo[0]) && is_array($matchesReplyTo[0]) && !empty($matchesReplyTo[0])){
                foreach ($matchesReplyTo[0] as $index => $matchReplyTo) {
                    $msg = erLhcoreClassModelmsg::fetch($matchesReplyTo[1][$index]);
                    if (is_object($msg)) {
                        // Reply To Message Object was found so we can adjust message itself
                        $msg_text = str_replace($matchReplyTo, '', $msg_text);
                        $metaData = $msg->meta_msg_array;
                        $iwh_msg_id = '';
                        if (isset($metaData['iwh_msg_id'])) {
                            $iwh_msg_id = $metaData['iwh_msg_id'];
                        }
                        $bodyRequest = str_replace(['{reply_to}','{/reply_to}','raw_{{iwh_msg_id}}','raw_{{db_msg_id}}'],['','', str_replace('\\\/','\/',self::trimOnce(json_encode($iwh_msg_id))), str_replace('\\\/','\/',self::trimOnce(json_encode($msg->id)))],$bodyRequest);
                        $bodyRequest = str_replace(['{{iwh_msg_id}}','{{db_msg_id}}'],[json_encode($iwh_msg_id),json_encode($msg->id)],$bodyRequest);
                    }
                }
            }
            // Message object not found remove API block from request body
            $bodyRequest = preg_replace('/\{reply_to\}(.*?)\{\/reply_to\}/ms','',$bodyRequest);
        }

        return $bodyRequest;
    }

    public static function isMarkdownRowComplete(string $row): bool {
        // Check for proper balancing of bold markers (**)
        $boldCount = substr_count($row, '**');
        if ($boldCount % 2 !== 0) {
            return false; // Unmatched bold markers
        }

        // Check for proper balancing of single backticks (`) for inline code
        $singleBacktickCount = substr_count($row, '`');
        if ($singleBacktickCount % 2 !== 0) {
            return false; // Unmatched single backtick
        }

        // Check for proper balancing of triple backticks (```) for code blocks
        $tripleBacktickCount = substr_count($row, '```');
        if ($tripleBacktickCount % 2 !== 0) {
            return false; // Unmatched triple backtick
        }
        
        $bracketsCount = substr_count($row, '[')+substr_count($row, ']');
        $linkBracketCount = substr_count($row, '(')+substr_count($row, ')');
        if ($bracketsCount > 0 && ($bracketsCount + $linkBracketCount) % 2 !== 0 || str_ends_with($row, ']')) {
            return false;
        }

        // All markers are balanced
        return true;
    }

    public static function extractDynamicParams($methodSettings, $params) {

        $dynamicVariables = [];
        $requiredVars = [];

        $userData = array(
            'dynamic_variables' => & $dynamicVariables,
            'required_vars' => & $requiredVars,
            'params' => $params,
        );

        array_walk_recursive($methodSettings, function ($item, $key, $userData) {

            $matchesValues = [];
            preg_match_all('~\{\{args\.((?:[^\{\}\}]++|(?R))*)\}\}~', $item,$matchesValues);

            // Replace value
            if (!empty($matchesValues[0])) {
                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                    $valueAttribute = self::extractAttribute($userData['params'], $matchesValues[1][$indexElement], '.');
                    $userData['dynamic_variables'][$elementValue] = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
                }
            }

            // Replace key
            $matchesValues = [];
            preg_match_all('~\{\{args\.((?:[^\{\}\}]++|(?R))*)\}\}~', $key, $matchesValues);
            if (!empty($matchesValues[0])) {
                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                    $valueAttribute = self::extractAttribute($userData['params'], $matchesValues[1][$indexElement], '.');
                    $userData['dynamic_variables'][$elementValue] = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
                }
            }

            // Look for checking variables
            $matchesValues = [];
            preg_match_all('~\{(not|is)_empty__args\.((?:[^\{\}\}]++|(?R))*)\}~', $key, $matchesValues);
            if (!empty($matchesValues[0])) {
                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                    $valueAttribute = self::extractAttribute($userData['params'], $matchesValues[2][$indexElement], '.');
                    $userData['dynamic_variables']['{{args.' . $matchesValues[2][$indexElement] .'}}'] = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
                }
            }

            $matchesValues = [];
            preg_match_all('~\{(not|is)_empty__args\.((?:[^\{\}\}]++|(?R))*)\}~', $item, $matchesValues);
            if (!empty($matchesValues[0])) {
                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                    $valueAttribute = self::extractAttribute($userData['params'], $matchesValues[2][$indexElement], '.');
                    $userData['dynamic_variables']['{{args.' . $matchesValues[2][$indexElement] .'}}'] = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
                }
            }

        }, $userData);

        return $userData['dynamic_variables'];
    }

    public static function extractDynamicVariables($methodSettings, $chat) {

         $dynamicVariables = [];
         $requiredVars = [];
         
         $userData = array(
             'dynamic_variables' => & $dynamicVariables,
             'required_vars' => & $requiredVars,
             'chat' => $chat,
         );
         
         array_walk_recursive($methodSettings, function ($item, $key, $userData) {
            $matchesValues = [];
            preg_match_all('~\{\{lhc\.((?:[^\{\}\}]++|(?R))*)\}\}~', $item,$matchesValues);

            if (!empty($matchesValues[0])) {
                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                    $userData['dynamic_variables'][$elementValue] = $userData['chat']->{$matchesValues[1][$indexElement]};
                }
            }

            $matchesValues = [];
            preg_match_all('~\{\{lhc\.((?:[^\{\}\}]++|(?R))*)\}\}~', $key, $matchesValues);
            if (!empty($matchesValues[0])) {
                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                    $userData['dynamic_variables'][$elementValue] = $userData['chat']->{$matchesValues[1][$indexElement]};
                }
            }

            $matchesValues = [];
            preg_match_all('~\{\{previous_visitor_messages_url__([0-9]+)\}\}~', $item, $matchesValues);
            if (!empty($matchesValues[0])) {
                $userData['dynamic_variables']['{if_previous_visitor_messages}'] = false;
                foreach ($matchesValues[0] as $indexElement => $elementValue) {

                    $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => $matchesValues[1][$indexElement], 'sort' => 'id DESC', 'filter' => array('user_id' => 0, 'chat_id' => $userData['chat']->id))));

                    foreach ($messages as $indexMessage => $message) {
                        $messages[$indexMessage]->msg = $messages[$indexMessage]->msg . ".";
                    }

                    // Fetch chat messages
                    $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                    $tpl->set('chat', $userData['chat']);
                    $tpl->set('messages', $messages);
                    $tpl->set('remove_meta', true);

                    $userData['dynamic_variables'][$elementValue] = trim($tpl->fetch());

                    if (!empty($userData['dynamic_variables'][$elementValue])) {
                        $userData['dynamic_variables']['{if_previous_visitor_messages}'] = true;
                    }
                }
            }

            // Detect does customer want's somewhere all messages
            if (strpos($item,'{{msg_all_html}}') !== false && !in_array('{{msg_all_html}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_all_html}}';

                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false,'sort' => 'id DESC', 'filter' => array('chat_id' => $userData['chat']->id))));

                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/full.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);
                $userData['dynamic_variables']['{{msg_all_html}}'] = $tpl->fetch();
            }

            // Detect does customer want's somewhere all messages
            if (strpos($item,'{{msg_items}}') !== false && !in_array('{{msg_items}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_items}}';
                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false,'sort' => 'id DESC', 'filter' => array('chat_id' => $userData['chat']->id))));
                $userData['dynamic_variables']['{{msg_items}}'] = $messages;
            }

            if (strpos($item,'{{subject_ids}}') !== false && !in_array('{{subject_ids}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{subject_ids}}';
                $userData['dynamic_variables']['{{subject_ids}}'] = erLhAbstractModelSubjectChat::getCount(['filter' => ['chat_id' => $userData['chat']->id]],'count','subject_id','subject_id',false, true, true);
            }

            if (strpos($item,'{{subject_list}}') !== false && !in_array('{{subject_list}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{subject_list}}';
                $userData['dynamic_variables']['{{subject_list}}'] = [];
                foreach (erLhAbstractModelSubjectChat::getList(['filter' => ['chat_id' => $userData['chat']->id]]) as $chatSubject){
                    $userData['dynamic_variables']['{{subject_list}}'][] = $chatSubject->subject;
                }
            }

            if (strpos($item,'{{subject_list_names}}') !== false && !in_array('{{subject_list_names}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{subject_list_names}}';
                $userData['dynamic_variables']['{{subject_list_names}}'] = '';
                $subjectItems = [];
                foreach (erLhAbstractModelSubjectChat::getList(['filter' => ['chat_id' => $userData['chat']->id]]) as $chatSubject){
                    $subjectItems[] = (string)$chatSubject->subject;
                }
                $userData['dynamic_variables']['{{subject_list_names}}'] = implode("\n",$subjectItems);
            }

            if (strpos($item,'{{msg_all}}') !== false && !in_array('{{msg_all}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_all}}';
                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false,'sort' => 'id DESC', 'filter' => array('chat_id' => $userData['chat']->id))));
                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);
                $userData['dynamic_variables']['{{msg_all}}'] = $tpl->fetch();
            }

            if (
                (strpos($item,'{{msg_all_conversation}}') !== false && !in_array('{{msg_all_conversation}}',$userData['required_vars'])) ||
                (strpos($item,'{{msg_all_conversation_br}}') !== false && !in_array('{{msg_all_conversation_br}}',$userData['required_vars']))
            ) {
                $userData['required_vars'][] = '{{msg_all_conversation}}';
                $userData['required_vars'][] = '{{msg_all_conversation_br}}';
                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false, 'filternot' => array('user_id' => -1), 'sort' => 'id DESC', 'filter' => array('chat_id' => $userData['chat']->id))));
                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);
                $tpl->set('remove_whisper', true);
                $userData['dynamic_variables']['{{msg_all_conversation}}'] = $tpl->fetch();
                $userData['dynamic_variables']['{{msg_all_conversation_br}}'] = nl2br($userData['dynamic_variables']['{{msg_all_conversation}}']);
            }

            if (

                (strpos($item,'{{media_all}}') !== false && !in_array('{{media_all}}',$userData['required_vars'])) ||
                (strpos($item,'{{media_all_links}}') !== false && !in_array('{{media_all_links}}',$userData['required_vars'])) ||
                (strpos($item,'{{media_all_links_raw}}') !== false && !in_array('{{media_all_links_raw}}',$userData['required_vars']))
            ) {
                 $userData['required_vars'][] = '{{media_all}}';
                 $userData['required_vars'][] = '{{media_all_links}}';
                 $userData['required_vars'][] = '{{media_all_links_raw}}';
                 $media = array();
                 $mediaLinks = array();
                 $mediaLinksRaw = array();
                 foreach (erLhcoreClassModelmsg::getList(array('limit' => false,'sort' => 'id DESC', 'filter' => array('chat_id' => $userData['chat']->id))) as $chatMessage) {
                     $chatMessageText = $chatMessage->msg;
                     $matches = array();
                     preg_match_all('/\[file="?(.*?)"?\]/', $chatMessageText, $matches);

                     foreach ($matches[1] as $index => $body) {
                         $parts = explode('_', $body);
                         $fileID = $parts[0];
                         $hash = $parts[1];
                         try {
                             $file = erLhcoreClassModelChatFile::fetch($fileID);
                             if (is_object($file) && $hash == $file->security_hash) {
                                 $url = erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$file->id}/{$hash}";;
                                 $media[] = array(
                                     'id' => $file->id,
                                     'size' => $file->size,
                                     'upload_name' => $file->upload_name,
                                     'type' => $file->type,
                                     'extension' => $file->extension,
                                     'hash' => $hash,
                                     'url' => $url
                                 );
                                 $mediaLinks[] = $url . " [" . $file->upload_name ."]";
                                 $mediaLinksRaw[] = $url;
                             }
                         } catch (Exception $e) {

                         }
                     }
                 }
                 $userData['dynamic_variables']['{{media_all}}'] = $media;
                 $userData['dynamic_variables']['{{media_all_links}}'] = implode("\n",$mediaLinks);
                 $userData['dynamic_variables']['{{media_all_links_raw}}'] = implode("\n",$mediaLinksRaw);
             }

            // All messages without [<date>] [<nick>] and system messages
            if (strpos($item,'{{msg_all_content}}') !== false && !in_array('{{msg_all_content}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_all_content}}';

                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false, 'filternot' => array('user_id' => -1), 'sort' => 'id DESC', 'filter' => array('chat_id' => $userData['chat']->id))));

                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);
                $tpl->set('remove_meta', true);

                $userData['dynamic_variables']['{{msg_all_content}}'] = $tpl->fetch();
            }

             // All messages since operator took over without [<date>] [<nick>] and system messages
            if (strpos($item,'{{msg_all_since_transfer_content}}') !== false && !in_array('{{msg_all_since_transfer_content}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_all_since_transfer_content}}';

                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false, 'filternot' => array('user_id' => -1), 'sort' => 'id DESC','filter', 'filtergte' => array('time' => $userData['chat']->pnd_time), 'filter' => array('chat_id' => $userData['chat']->id))));

                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);
                $tpl->set('remove_meta', true);

                $userData['dynamic_variables']['{{msg_all_since_transfer_content}}'] = $tpl->fetch();
            }

            // All operator messages from chat
            if (strpos($item,'{{msg_all_op_msg_content}}') !== false && !in_array('{{msg_all_op_msg_content}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_all_op_msg_content}}';

                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false, 'filtergt' => array('user_id' => 0), 'sort' => 'id DESC','filter' => array('chat_id' => $userData['chat']->id))));

                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);
                $tpl->set('remove_meta', true);

                $userData['dynamic_variables']['{{msg_all_op_msg_content}}'] = $tpl->fetch();
            }

            // All visitor messages without meta
            if (strpos($item,'{{msg_all_vis_msg_content}}') !== false && !in_array('{{msg_all_vis_msg_content}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_all_vis_msg_content}}';

                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false, 'sort' => 'id DESC','filter' => array('user_id' => 0, 'chat_id' => $userData['chat']->id))));

                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);
                $tpl->set('remove_meta', true);

                $userData['dynamic_variables']['{{msg_all_vis_msg_content}}'] = $tpl->fetch();
            }

            // All visitor messages since transfer without meta
            if (strpos($item,'{{msg_all_vis_since_transfer_content}}') !== false && !in_array('{{msg_all_vis_since_transfer_content}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_all_vis_since_transfer_content}}';

                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false, 'sort' => 'id DESC', 'filtergte' => array('time' => $userData['chat']->pnd_time), 'filter' => array('user_id' => 0, 'chat_id' => $userData['chat']->id))));

                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);
                $tpl->set('remove_meta', true);

                $userData['dynamic_variables']['{{msg_all_vis_since_transfer_content}}'] = $tpl->fetch();
            }

            // Detect does customer want's somewhere footprint
            if (strpos($item,'{{footprint}}') !== false && !in_array('{{footprint}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{footprint}}';

                $footprints = erLhcoreClassModelChatOnlineUserFootprint::getList(array('limit' => 25,'sort' => 'id DESC', 'filter' => array('chat_id' => $userData['chat']->id)));

                $itemsFootprint = array();
                foreach ($footprints as $footprint) {
                    $itemsFootprint[] = $footprint->time_ago . ' | ' . $footprint->page;
                }

                $userData['dynamic_variables']['{{footprint}}'] = implode("\n",$itemsFootprint);
            }

             if (strpos($item,'{{survey}}') !== false && !in_array('{{survey}}',$userData['required_vars'])) {
                 $userData['required_vars'][] = '{{survey}}';
                 $userData['dynamic_variables']['{{survey}}'] = erLhcoreClassChatMail::getSurveyContent($userData['chat']);
             }

        }, $userData);

        return $userData['dynamic_variables'];
    }

    public static function extractAttribute($partData, $string, $separator = ':')
    {

        $stringParts = explode('^',$string);

        $parts = explode($separator, $stringParts[0]);

        $partFound = true;
        foreach ($parts as $part) {

            if (strpos($part,'[') === 0) {

                $conditions = explode('=', str_replace(['[',']'],'',$part));

                $foundConditions = false;
                foreach ($partData as $partItem) {
                    if ($partItem[$conditions[0]] == $conditions[1]) {
                        $partData = $partItem;
                        $foundConditions = true;
                        continue;
                    }
                }

                if ($foundConditions == false) {
                    $partFound = false;
                    break;
                }

            } else {
                if (is_object($partData)) {
                    $partDataValue = $partData->{$part};
                    if (isset($partDataValue)) {
                        $partData = $partDataValue;
                    } else {
                        $partFound = false;
                        break;
                    }
                } else {
                    $partPlainData = explode('___',$part);
                    if (isset($partPlainData[1]) && $partPlainData[1] === 'array_pop' && isset($partData[$partPlainData[0]]) && is_array($partData[$partPlainData[0]])) {
                        $partData = array_pop($partData[$partPlainData[0]]);
                    } elseif (isset($partData[$part]) ) {
                        $partData = $partData[$part];
                    } else {
                        $partFound = false;
                        break;
                    }
                }
            }
        }

        if (isset($stringParts[1])) {

            $combinations = explode('==',$stringParts[1]);
            $paramsOutput = [];

            for ($i = 0; $i < count($combinations)/2; $i++) {
                $paramsOutput[$combinations[$i * 2]] = $combinations[$i+1];
            }

            if (is_array($partData)) {
                if (isset($paramsOutput['implode'])) {
                    $output = "";
                    foreach ($partData as $partDataItem) {
                        $output .= (strpos($paramsOutput['implode'],'{item}') === false ? (string)$partDataItem : '').str_replace(["{n}","{item}"],["\n",(string)$partDataItem],$paramsOutput['implode']);
                    }
                    $partData = trim($output);
                }
            }
        }

        return array('found' => $partFound, 'value' => $partData);
    }

}

?>
