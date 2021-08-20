<?php

class erLhcoreClassGenericBotActionRestapi
{

    public static function process($chat, $action, $trigger, $params)
    {

        if (isset($action['content']['rest_api']) && is_numeric($action['content']['rest_api']) && isset($action['content']['rest_api_method']) && !empty($action['content']['rest_api_method'])) {

            $restAPI = erLhcoreClassModelGenericBotRestAPI::fetch($action['content']['rest_api']);

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
                if ((isset($action['content']['attr_options']['background_process']) && $action['content']['attr_options']['background_process'] == true)) {

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
                    if ((!isset($params['do_not_save']) || $params['do_not_save'] == false) && class_exists('erLhcoreClassExtensionLhcphpresque')) {
                        $event->saveThis();
                        $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
                        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_rest_api_queue', 'erLhcoreClassLHCBotWorker', array('inst_id' => $inst_id, 'action' => 'rest_api', 'event_id' => $event->id));
                        return ;
                    }
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.rest_api_before_request', array(
                    'restapi' => & $restAPI,
                    'chat' => $chat
                ));

                $response = self::makeRequest($restAPI->configuration_array['host'], $method, array('action' => $action, 'rest_api_method_params' => $action['content']['rest_api_method_params'], 'chat' => $chat, 'params' => $params));

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

                        return $argsDefault;

                    } else {
                        // Do nothing as user did not chose any trigger to execute
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
                        'trigger_id' => $action['content']['rest_api_method_output']['default_trigger']
                    );

                    if (isset($params['msg'])) {
                        $argsDefault['msg'] = $params['msg'];
                    } elseif (isset($params['msg_text'])) {
                        $argsDefault['msg_text'] = $params['msg_text'];
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

                    $msg->time = time() + 1;
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

    public static function makeRequest($host, $methodSettings, $paramsCustomer)
    {
        $msg_text = '';
        $msg_text_cleaned = '';

        if (isset($paramsCustomer['params']['msg'])) {
            $msg_text_cleaned = $msg_text = $paramsCustomer['params']['msg']->msg;
        } elseif (isset($paramsCustomer['params']['msg_text'])) {
            $msg_text_cleaned = $msg_text = $paramsCustomer['params']['msg_text'];
        }

        // We have to extract attached files and send them separately
        $matches = array();
        preg_match_all('/\[file="?(.*?)"?\]/', $msg_text, $matches);

        $media = array();
        $files = array();

        foreach ($matches[1] as $index => $body) {
            $parts = explode('_', $body);
            $fileID = $parts[0];
            $hash = $parts[1];
            try {
                $file = erLhcoreClassModelChatFile::fetch($fileID);
                if (is_object($file) && $hash == $file->security_hash) {

                    if (isset($_SERVER['HTTP_HOST'])) {
                        $url = (erLhcoreClassSystem::$httpsMode == true ? 'https:' : 'http:') . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$file->id}/{$hash}";
                    } else {
                        if (class_exists('erLhcoreClassInstance')) {
                            $site_address = 'https://' . erLhcoreClassInstance::$instanceChat->address . '.' . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'seller_domain');
                        } else {
                            $site_address = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->settings['site_address'];
                        }
                        $url = $site_address . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$file->id}/{$hash}";
                    }

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

        $file_body = null;
        $file_url = null;
        $file_name = null;

        $file_api = false;
        
        // Switch to file API if it's only one file send
        if (isset($methodSettings['body_raw_file']) && $methodSettings['body_raw_file'] != '' && count($files) == 1 && trim($msg_text_cleaned) == '') {
            foreach ($files as $mediaFile) {

                if (isset($methodSettings['suburl_file']) && !empty($methodSettings['suburl_file'])) {
                    $methodSettings['suburl'] = $methodSettings['suburl_file'];
                }

                $file_body = 'data:'.$mediaFile->type.';base64,'.base64_encode(file_get_contents($mediaFile->file_path_server));
                $file_name = $mediaFile->upload_name;
                $file_api = true;

                if (isset($_SERVER['HTTP_HOST'])) {
                    $file_url = (erLhcoreClassSystem::$httpsMode == true ? 'https:' : 'http:') . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$mediaFile->id}/{$mediaFile->security_hash}";
                } else {
                    if (class_exists('erLhcoreClassInstance')) {
                        $site_address = 'https://' . erLhcoreClassInstance::$instanceChat->address . '.' . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'seller_domain');
                    } else {
                        $site_address = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->settings['site_address'];
                    }
                    $file_url = $site_address . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$mediaFile->id}/{$mediaFile->security_hash}";
                }
            }
        }

        $dynamicParamsVariables = self::extractDynamicParams($methodSettings, $paramsCustomer['params']);

        $dynamicReplaceVariables = self::extractDynamicVariables($methodSettings, $paramsCustomer['chat']);

        $replaceVariables = array(
            '{{msg}}' => $msg_text,
            '{{msg_clean}}' => trim($msg_text_cleaned),
            '{{msg_url}}' => erLhcoreClassBBCodePlain::make_clickable($msg_text, array('sender' => 0)),
            '{{chat_id}}' => $paramsCustomer['chat']->id,
            '{{lhc.nick}}' =>$paramsCustomer['chat']->nick,
            '{{lhc.email}}' => $paramsCustomer['chat']->email,
            '{{lhc.department}}' => (string)$paramsCustomer['chat']->department,
            '{{lhc.dep_id}}' => (string)$paramsCustomer['chat']->dep_id,
            '{{ip}}' => (string)erLhcoreClassIPDetect::getIP(),
            '{{media}}' => json_encode($media),
            '{{file_body}}' => $file_body,
            '{{file_url}}' => $file_url,
            '{{file_name}}' => $file_name
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
            '{{msg_clean}}' => json_encode(trim($msg_text_cleaned)),
            '{{msg_url}}' => json_encode(erLhcoreClassBBCodePlain::make_clickable($msg_text, array('sender' => 0))),
            '{{chat_id}}' => json_encode($paramsCustomer['chat']->id),
            '{{lhc.nick}}' => json_encode($paramsCustomer['chat']->nick),
            '{{lhc.email}}' => json_encode($paramsCustomer['chat']->email),
            '{{lhc.department}}' => json_encode((string)$paramsCustomer['chat']->department),
            '{{lhc.dep_id}}' => json_encode((string)$paramsCustomer['chat']->dep_id),
            '{{ip}}' => json_encode(erLhcoreClassIPDetect::getIP()),
            '{{media}}' => json_encode($media),
            '{{file_body}}' => json_encode($file_body),
            '{{file_url}}' => json_encode($file_url),
            '{{file_name}}' =>json_encode($file_name)
        );

        foreach ($dynamicReplaceVariables as $keyDynamic => $valueDynamic) {
            $replaceVariablesJSON[$keyDynamic] = json_encode($valueDynamic);
        }

        foreach ($dynamicParamsVariables as $keyDynamic => $valueDynamic) {
            $replaceVariablesJSON[$keyDynamic] = json_encode($valueDynamic);
        }

        if (isset($methodSettings['conditions']) && is_array($methodSettings['conditions']) && !empty($methodSettings['conditions'])) {
            foreach ($methodSettings['conditions'] as $condition){

                if (isset($replaceVariables[$condition['key']]) && $replaceVariables[$condition['key']] !== null) {
                    $condition['key'] = $replaceVariables[$condition['key']];
                    $validCondition = true;
                } else {
                    $validCondition = false;
                }

                if ($validCondition === true && isset($condition['success_condition']) && $condition['success_condition'] != '' && isset($condition['value']) && $condition['value'] != '') {
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
                        'meta' => '',
                        'id' => 0
                    );
                }
            }
        }

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

        $queryArgs = array();

        if (isset($methodSettings['query']) && !empty($methodSettings['query'])) {
            foreach ($methodSettings['query'] as $dataQuery) {
                $queryArgs[$dataQuery['key']] = str_replace(array_keys($replaceVariables), array_values($replaceVariables), $dataQuery['value']);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, (isset($methodSettings['max_execution_time']) && is_numeric($methodSettings['max_execution_time']) && $methodSettings['max_execution_time'] >= 1 && $methodSettings['max_execution_time'] <= 30) ? (int)$methodSettings['max_execution_time'] : 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

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
            curl_setopt($ch, CURLOPT_USERPWD, $methodSettings['auth_username'] . ":" . $methodSettings['auth_password']);
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
            if (isset($methodSettings['postparams']) && !empty($methodSettings['postparams'])) {
                $postParams = array();
                foreach ($methodSettings['postparams'] as $postParam) {
                    $postParams[$postParam['key']] = str_replace(array_keys($replaceVariables), array_values($replaceVariables), $postParam['value']);
                }

                if ($methodSettings['body_request_type'] == 'form-data-urlencoded') {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
                }
            }

            if ($methodSettings['body_request_type'] == 'form-data-urlencoded') {
                $headers[] = 'Cache-Control: no-cache';
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            }

        } elseif (isset($methodSettings['body_request_type']) && $methodSettings['body_request_type'] == 'raw') {

            $rawReplaceArray = array();
            foreach ($replaceVariablesJSON as $keyVariable => $keyValue) {
                $rawReplaceArray['raw_'.$keyVariable] = trim($keyValue,"\"");
            }

            $bodyPOST = str_replace(array_keys($rawReplaceArray), array_values($rawReplaceArray), $file_api === true ? $methodSettings['body_raw_file'] : $methodSettings['body_raw']);
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
        $url = rtrim($host) . str_replace(array_keys($replaceVariables), array_values($replaceVariables), (isset($methodSettings['suburl']) ? $methodSettings['suburl'] : '')) . (!empty($queryArgsString) ? '?'.$queryArgsString : '');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $content = curl_exec($ch);

        $http_error = '';

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

        $http_data = json_encode($paramsRequest);

        if (curl_errno($ch)) {
            $http_error = curl_error($ch);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

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

            foreach ($methodSettings['output'] as $outputCombination)
            {
                if ($allOptional == false && (!isset($paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk']) || $paramsCustomer['action']['content']['rest_api_method_output'][$outputCombination['id'] . '_chk'] == false)) {
                    // One of the conditions is checked, but not this one.
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

                        $successLocation = self::extractAttribute($contentJSON, $outputCombination['success_location']);

                        if ($successLocation['found'] === true) {

                            $responseValueSub = array();
                            for ($i = 2; $i <= 6; $i++) {
                                if (isset($outputCombination['success_location_' . $i]) && $outputCombination['success_location_' . $i] != '') {
                                    $successLocationNumbered = self::extractAttribute($contentJSON,$outputCombination['success_location_' . $i]);
                                    if ($successLocationNumbered['found'] === true) {
                                        $responseValueSub[$i] = $successLocationNumbered['value'];
                                    }
                                }
                            }

                            $responseValueCompare = $responseValue = $successLocation['value'];
                            if (isset($outputCombination['success_condition_val']) && !empty($outputCombination['success_condition_val'])) {
                                $responseValueCompareLocation = self::extractAttribute($contentJSON, $outputCombination['success_condition_val']);
                                if ($responseValueCompareLocation['found'] === true && !is_array($responseValueCompareLocation['value'])) {
                                    $responseValueCompare = $responseValueCompareLocation['value'];
                                } else {
                                    // Attribute was not found
                                    continue;
                                }
                            }
                        } else {
                            continue; // Required attribute was not found
                        }
                    } else {
                        $responseValueCompare = $responseValue = $content;
                    }

                    if (isset($outputCombination['success_condition']) && $outputCombination['success_condition'] != '' && isset($outputCombination['success_compare_value']) && $outputCombination['success_compare_value'] != '') {
                        if ( $outputCombination['success_condition'] == 'eq' && !($responseValueCompare == $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'lt' && !($responseValueCompare < $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'lte' && !($responseValueCompare <= $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'neq' && !($responseValueCompare != $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'gte' && !($responseValueCompare >= $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'gt' && !($responseValueCompare > $outputCombination['success_compare_value'])) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$outputCombination['success_compare_value']),$responseValueCompare,0) == false) {
                            continue;
                        } else if ($outputCombination['success_condition'] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresence(explode(',',$outputCombination['success_compare_value']),$responseValueCompare,0) == true) {
                            continue;
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

                    return array(
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
                        'id' => $outputCombination['id']);
                }
            }

            // We did not found matching response. Return everything.
            return array(
                'content' => $content,
                'content_raw' => $content,
                'http_code' => $httpcode,
                'http_error' => $http_error,
                'http_data' => $http_data,
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
            'content_2' => '',
            'content_3' => '',
            'content_4' => '',
            'content_5' => '',
            'content_6' => '',
            'meta' => array()
        );
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

            if (!empty($matchesValues[0])) {
                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                    $valueAttribute = self::extractAttribute($userData['params'], $matchesValues[1][$indexElement], '.');
                    $userData['dynamic_variables'][$elementValue] = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
                }
            }

            $matchesValues = [];
            preg_match_all('~\{\{args\.((?:[^\{\}\}]++|(?R))*)\}\}~', $key, $matchesValues);
            if (!empty($matchesValues[0])) {
                foreach ($matchesValues[0] as $indexElement => $elementValue) {
                    $valueAttribute = self::extractAttribute($userData['params'], $matchesValues[1][$indexElement], '.');
                    $userData['dynamic_variables'][$elementValue] = $valueAttribute['found'] == true ? $valueAttribute['value'] : null;
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

            if (strpos($item,'{{msg_all}}') !== false && !in_array('{{msg_all}}',$userData['required_vars'])) {
                $userData['required_vars'][] = '{{msg_all}}';

                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => false,'sort' => 'id DESC', 'filter' => array('chat_id' => $userData['chat']->id))));
                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $userData['chat']);
                $tpl->set('messages', $messages);

                $userData['dynamic_variables']['{{msg_all}}'] = $tpl->fetch();
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
                    if (isset($partData[$part]) ) {
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
